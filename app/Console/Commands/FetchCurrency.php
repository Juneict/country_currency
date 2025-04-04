<?php

namespace App\Console\Commands;

use App\Models\Country;
use App\Models\Currency;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Sleep;

class FetchCurrency extends Command
{
    protected $signature = 'import:currencies
                            {code? : Country Code (optional, imports for all countries if omitted)}
                            {--delay=1 : Base delay between requests in seconds}
                            {--max-retries=3 : Maximum retry attempts for failed requests}';

    protected $description = 'Import currency data for a specific country or all countries from GeoDB API';

    public function handle()
    {
        $this->info("Starting currencies import...");

        if ($countryCode = $this->argument('code')) {
            $country = Country::where('code', $countryCode)->first();
            if (!$country) {
                $this->error("Country with code {$countryCode} not found in database");
                return 1;
            }
            return $this->importCurrenciesForCountry($country) ? 0 : 1;
        }

        $countries = Country::all();
        $bar = $this->output->createProgressBar($countries->count());
        $bar->start();

        $success = true;
        foreach ($countries as $country) {
            if (!$this->importCurrenciesForCountry($country)) {
                $success = false;
            }
            $bar->advance();
            Sleep::for($this->option('delay'))->seconds();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("✅ Currency import completed " . ($success ? "successfully" : "with some errors"));
        return $success ? 0 : 1;
    }

    protected function importCurrenciesForCountry(Country $country): bool
    {
        $this->info("\n💰 Processing currencies for: {$country->name} ({$country->code})");

        $baseUrl = 'https://wft-geo-db.p.rapidapi.com/v1/locale/currencies';
        $currencies = $this->fetchCurrenciesWithRetry(
            $baseUrl,
            $country->code,
            (int) $this->option('max-retries'),
            (float) $this->option('delay')
        );

        if ($currencies === null) {
            return false;
        }

        if (empty($currencies)) {
            $this->info("  No currencies found for {$country->name}");
            return true;
        }

        foreach ($currencies as $currency) {
            try {
                Currency::updateOrCreate(
                    [
                        'code' => $currency['code'],
                        'country_id' => $country->id
                    ],
                    [
                        'country_id' => $country->id,
                        'symbol' => $currency['symbol'] ?? null,
                    ]
                );
            } catch (\Exception $e) {
                $this->error("  Failed to store currency {$currency['code']}: " . $e->getMessage());
                return false;
            }
        }

        $this->info("  Stored " . count($currencies) . " currencies");
        return true;
    }

    protected function fetchCurrenciesWithRetry(
        string $url,
        string $countryCode,
        int $maxRetries,
        float $baseDelay
    ): ?array {
        $attempt = 0;

        while ($attempt < $maxRetries) {
            try {
                $response = Http::withHeaders($this->getApiHeaders())
                    ->get($url, [
                        'countryId' => $countryCode,
                    ]);

                if ($response->tooManyRequests()) {
                    $retryAfter = $response->header('Retry-After') ?? $baseDelay * pow(2, $attempt);
                    $this->warn("  ⏳ Rate limited. Waiting {$retryAfter}s...");
                    Sleep::for($retryAfter)->seconds();
                    $attempt++;
                    continue;
                }

                if ($response->failed()) {
                    $this->handleApiError($response, "currencies for {$countryCode}");
                    $attempt++;
                    Sleep::for($baseDelay * pow(2, $attempt))->seconds();
                    continue;
                }

                $data = $response->json();
                return $data['data'] ?? [];

            } catch (\Exception $e) {
                $attempt++;
                $this->error("  Attempt {$attempt} failed: " . $e->getMessage());
                Sleep::for($baseDelay * pow(2, $attempt))->seconds();
            }
        }

        $this->error("  Failed to fetch currencies after {$maxRetries} attempts");
        return null;
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
        $this->error("  ❌ API error for {$context}");
        $this->error("  Status: " . $response->status());

        if ($response->tooManyRequests()) {
            $retryAfter = $response->header('Retry-After') ?? 30;
            $this->error("  Rate limited. Please try again after {$retryAfter}s");
        } else {
            $errorBody = json_decode($response->body(), true);
            $this->error("  Error: " . ($errorBody['message'] ?? $response->body()));
        }
    }
}
