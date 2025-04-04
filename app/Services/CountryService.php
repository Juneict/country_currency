<?php

namespace App\Services;

use App\Models\Country;
use Illuminate\Support\Facades\Http;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

class CountryService
{
    public function getAllCountries(): Collection
    {
        return Country::with(['cities', 'currency'])
            ->orderBy('name')
            ->get();
    }

    public function getCountry(int $id): Country
    {
        return Country::with(['cities', 'currency'])->findOrFail($id);
    }

    public function createCountry(array $countryData, array $currencyData): Country
    {
        $country = Country::create($countryData);
        $country->currency()->create($currencyData);
        
        return $country;
    }

    public function updateCountry(Country $country, array $countryData, array $currencyData): Country
    {
        $country->update($countryData);

        if ($country->currency) {
            $country->currency->update($currencyData);
        } else {
            $country->currency()->create($currencyData);
        }

        return $country;
    }

    public function deleteCountry(Country $country): bool
    {
        if ($country->currency) {
            $country->currency->delete();
        }
        
        return $country->delete();
    }

    public function fetchCountriesFromAPI(int $offset = 0, int $limit = 5): array
    {
        $baseUrl = 'https://wft-geo-db.p.rapidapi.com/v1/geo/countries';

        $response = Http::withHeaders([
            'x-rapidapi-host' => 'wft-geo-db.p.rapidapi.com',
            'x-rapidapi-key' => 'b016e92a02msh9e57d546c5c3863p167631jsnc234d2055635'
        ])->get($baseUrl, [
            'offset' => $offset,
            'limit' => $limit,
        ]);

        if ($response->failed()) {
            throw new \Exception('API request failed: ' . ($response->json()['message'] ?? $response->status()));
        }

        $data = $response->json();
        if (!isset($data['data'])) {
            throw new \Exception('Invalid API response format');
        }

        return $this->processCountries($data);
    }

    private function processCountries(array $data): array
    {
        $countries = $data['data'];
        $totalCount = $data['metadata']['totalCount'] ?? 0;
        $processedCount = 0;

        foreach ($countries as $country) {
            try {
                $currencyCode = $country['currencyCodes'][0] ?? null;

                Country::updateOrCreate(
                    ['code' => $country['code']],
                    [
                        'name' => $country['name'],
                        'currency_code' => $currencyCode,
                        'wikiDataId' => $country['wikiDataId'] ?? null,
                    ]
                );
                $processedCount++;
            } catch (\Exception $e) {
                Log::error("Failed to process country {$country['code']}: " . $e->getMessage());
                continue;
            }
        }

        return [
            'processedCount' => $processedCount,
            'totalCount' => $totalCount
        ];
    }
}