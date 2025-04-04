<?php

namespace App\Console\Commands;

use App\Models\City;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class FetchCities extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:cities {code : Country Code or WikiIDs} {offset=0 : Starting offset for pagination}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $baseUrl = 'https://wft-geo-db.p.rapidapi.com/v1/geo/cities';

        $offset = (int) $this->argument('offset');
        $countryCode = $this->argument('code');
        $limit = 5;
        $totalCount = 0;

        $response = Http::withHeaders([
            'x-rapidapi-host' => 'wft-geo-db.p.rapidapi.com',
            'x-rapidapi-key' => 'b016e92a02msh9e57d546c5c3863p167631jsnc234d2055635'
        ])->get($baseUrl , [
            'offset' => $offset,
            'countryIds' => $countryCode,
        ]);

        if($response->failed())
        {
            $this->error('API request failed');
            $this->error('Status Code: ' . $response->status());
            $this->error('Response Body: ' . $response->body());
            return 1;
        }

        $data = $response->json();
        $cities = $data['data'];
        $totalCount = $data['metadata']['totalCount'];

        foreach ($cities as $city) {
            City::updateOrCreate([
                'wikiDataId' => $city['wikiDataId'],
                'name' => $city['name'],
                'country_code' => $city['countryCode'],
                'region' => $city['region'],
                'region_code' => $city['regionCode'],
                'latitude' => $city['latitude'],
                'longitude' => $city['longitude'],
                'population' => $city['population'],
            ]);
        }

        $this->info("Fetched and stored " . count($cities) . " cities from offset $offset with limit $limit.");
        $this->info("Total countries available: $totalCount");
        $this->info("Next offset to fetch: " . ($offset + $limit));

        return 0;
    }
}
