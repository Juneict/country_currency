<?php

namespace App\Console\Commands;

use App\Models\Country;
use App\Models\City;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Sleep;

class FetchCountries extends Command
{
    protected $signature = 'import:countries-with-cities
                            {--limit=10 : Number of cities to fetch per request (max 10)}
                            {--country-offset=0 : Offset for countries fetching}
                            {--country-limit=5 : Limit for countries fetching}
                            {--max-cities=50 : Maximum cities to store per country}
                            {--delay=1 : Base delay between requests in seconds}
                            {--max-retries=3 : Maximum retry attempts for failed requests}';

    protected $description = 'Import countries with their cities (max 50 per country) from GeoDB API';

    public function handle()
    {
        if (!$this->importCountries()) {
            return 1;
        }

        $this->importCitiesForCountries();

        $this->info("\nAll done! Countries and cities imported successfully.");
        return 0;
    }

    protected function importCountries(): bool
    {
        $this->info("Starting countries import...");

        $baseUrl = 'https://wft-geo-db.p.rapidapi.com/v1/geo/countries';
        $offset = (int) $this->option('country-offset');
        $limit = (int) $this->option('country-limit');

        $response = Http::withHeaders($this->getApiHeaders())
            ->get($baseUrl, [
                'offset' => $offset,
                'limit' => $limit
            ]);

        if ($response->failed()) {
            $this->handleApiError($response, 'countries');
            return false;
        }

        $data = $response->json();
        $countries = $data['data'];
        $totalCount = $data['metadata']['totalCount'];

        $bar = $this->output->createProgressBar(count($countries));
        $bar->start();

        foreach ($countries as $country) {
            $currencyCode = $country['currencyCodes'][0] ?? 'N/A';

            Country::updateOrCreate(
                ['code' => $country['code']],
                [
                    'name' => $country['name'],
                    'currency_code' => $currencyCode,
                    'wikiDataId' => $country['wikiDataId'],
                ]
            );

            $bar->advance();
            Sleep::for($this->option('delay'))->seconds();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info(sprintf(
            "✅ Fetched %d/%d countries (offset %d, limit %d)",
            count($countries),
            $totalCount,
            $offset,
            $limit
        ));

        return true;
    }

    protected function importCitiesForCountries(): void
    {
        $perRequestLimit = min(10, (int) $this->option('limit')); // API max is 10
        $maxCitiesPerCountry = (int) $this->option('max-cities');
        $baseDelay = (float) $this->option('delay');
        $maxRetries = (int) $this->option('max-retries');
        $countries = Country::all();

        $this->info(sprintf(
            "\nStarting cities import (max %d per country, %d per request)...",
            $maxCitiesPerCountry,
            $perRequestLimit
        ));

        foreach ($countries as $country) {
            $this->info("\n🌍 Country: {$country->name} ({$country->code})");

            $baseUrl = 'https://wft-geo-db.p.rapidapi.com/v1/geo/cities';
            $offset = 0;
            $totalCitiesStored = 0;
            $totalAvailable = 0;

            do {
                // Get cities batch with retry logic
                $citiesBatch = $this->fetchCitiesWithRetry(
                    $baseUrl,
                    $country->code,
                    $offset,
                    $perRequestLimit,
                    $maxRetries,
                    $baseDelay
                );

                if (empty($citiesBatch)) {
                    break; // Skip if we couldn't fetch cities
                }

                // Store cities (but don't exceed our max)
                $remainingCapacity = $maxCitiesPerCountry - $totalCitiesStored;
                $citiesToStore = array_slice($citiesBatch, 0, $remainingCapacity);

                foreach ($citiesToStore as $city) {
                    City::updateOrCreate(
                        [
                            'country_id' => $country['id'],
                            'name' => $city['name'],
                            'region' => $city['region'],
                            'region_code' => $city['regionCode'],
                            'latitude' => $city['latitude'],
                            'longitude' => $city['longitude'],
                            'population' => $city['population'],
                        ]
                    );
                }

                $storedCount = count($citiesToStore);
                $totalCitiesStored += $storedCount;

                $this->info(sprintf(
                    "  - Stored %d cities (offset %d, total %d/%d)",
                    $storedCount,
                    $offset,
                    $totalCitiesStored,
                    min($maxCitiesPerCountry, $totalAvailable)
                ));

                if ($totalCitiesStored >= $maxCitiesPerCountry) {
                    break;
                }

                $offset += $perRequestLimit;
                Sleep::for($baseDelay)->seconds();

            } while ($totalCitiesStored < $totalAvailable && $totalCitiesStored < $maxCitiesPerCountry);

            $this->info("  🏙️  Total stored: {$totalCitiesStored} cities");
        }
    }

    protected function fetchCitiesWithRetry(
        string $url,
        string $countryCode,
        int $offset,
        int $limit,
        int $maxRetries,
        float $baseDelay
    ): array {
        $attempt = 0;

        while ($attempt < $maxRetries) {
            try {
                $response = Http::withHeaders($this->getApiHeaders())
                    ->get($url, [
                        'offset' => $offset,
                        'limit' => $limit,
                        'countryIds' => $countryCode,
                    ]);

                if ($response->tooManyRequests()) {
                    $retryAfter = $response->header('Retry-After') ?? $baseDelay * pow(2, $attempt);
                    $this->warn("    ⏳ Rate limited. Waiting {$retryAfter}s...");
                    Sleep::for($retryAfter)->seconds();
                    $attempt++;
                    continue;
                }

                if ($response->failed()) {
                    $this->handleApiError($response, "cities for {$countryCode}");
                    $attempt++;
                    Sleep::for($baseDelay * pow(2, $attempt))->seconds();
                    continue;
                }

                $data = $response->json();
                $this->totalAvailable = $data['metadata']['totalCount'] ?? 0;
                return $data['data'] ?? [];

            } catch (\Exception $e) {
                $attempt++;
                $this->error("    Attempt {$attempt} failed: " . $e->getMessage());
                Sleep::for($baseDelay * pow(2, $attempt))->seconds();
            }
        }

        $this->error("    Failed after {$maxRetries} attempts. Skipping...");
        return [];
    }

    protected function getApiHeaders(): array
    {
        return [
            'x-rapidapi-host' => 'wft-geo-db.p.rapidapi.com',
            'x-rapidapi-key' => 'b016e92a02msh9e57d546c5c3863p167631jsnc234d2055635',
            'Accept' => 'application/json',
        ];
    }

    protected function handleApiError($response, string $context): void
    {
        $this->error("    ❌ API error for {$context}");
        $this->error("    Status: " . $response->status());

        if ($response->tooManyRequests()) {
            $retryAfter = $response->header('Retry-After') ?? 30;
            $this->error("    Rate limited. Please try again after {$retryAfter}s.");
        } else {
            $errorBody = json_decode($response->body(), true);
            $this->error("    Error: " . ($errorBody['message'] ?? $response->body()));
        }
    }
}
