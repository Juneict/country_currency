<?php

namespace App\Services;

use App\Models\City;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Database\Eloquent\Collection;

class CityService
{
    public function getCitiesByCountry(int $country_id): Collection
    {
        return City::where('country_id', $country_id)->get();
    }

    public function createCity(array $validated): City
    {
        return City::create($validated);
    }

    public function updateCity(City $city, array $validated): bool
    {
        return $city->update($validated);
    }

    public function deleteCity(City $city): bool
    {
        return $city->delete();
    }

    public function fetchCitiesFromAPI(int $country_id, int $offset = 0, int $limit = 5)
    {
        $baseUrl = "https://wft-geo-db.p.rapidapi.com/v1/geo/cities";
        
        $response = Http::withHeaders([
            'x-rapidapi-host' => 'wft-geo-db.p.rapidapi.com',
            'x-rapidapi-key'  => 'b016e92a02msh9e57d546c5c3863p167631jsnc234d2055635'
        ])->get($baseUrl, [
            'countryIds' => $country_id,
            'offset'     => $offset,
            'limit'      => $limit,
        ]);

        if ($response->failed()) {
            throw new \Exception('API request failed: ' . ($response->json()['message'] ?? $response->status()));
        }

        $data = $response->json();
        if (!isset($data['data'])) {
            throw new \Exception('Invalid API response format');
        }

        return $this->processCities($data);
    }

    private function processCities(array $data): array
    {
        $cities = $data['data'];
        $totalCount = $data['metadata']['totalCount'] ?? 0;
        $processedCount = 0;

        foreach ($cities as $city) {
            try {
                City::updateOrCreate(
                    ['wikiDataId' => $city['wikiDataId']],
                    [
                        'name'         => $city['name'],
                        'country_code' => $city['countryCode'],
                        'region'       => $city['region'],
                        'region_code'  => $city['regionCode'],
                        'latitude'     => (string) $city['latitude'],
                        'longitude'    => (string) $city['longitude'],
                        'population'   => $city['population'],
                    ]
                );
                $processedCount++;
            } catch (\Exception $e) {
                Log::error("Failed to process city with wikiDataId {$city['wikiDataId']}: " . $e->getMessage());
                continue;
            }
        }

        return [
            'processedCount' => $processedCount,
            'totalCount' => $totalCount
        ];
    }
}