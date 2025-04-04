<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Country;
use App\Services\CityService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CityController extends Controller
{
    protected $cityService;

    public function __construct(CityService $cityService)
    {
        $this->cityService = $cityService;
    }

    public function index($country_id)
    {
        $cities = $this->cityService->getCitiesByCountry($country_id);

        return Inertia::render('Cities/Index', [
            'cities' => $cities,
        ]);
    }

    public function create()
    {
        $countries = Country::all();
        return Inertia::render('Cities/Create', ['countries' => $countries]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'country_id'    => 'required|integer',
            'region'        => 'required|string|max:255',
            'region_code'   => 'required|string|max:255',
            'latitude'      => 'required|string|max:255',
            'longitude'     => 'required|string|max:255',
            'population'    => 'nullable|integer'
        ]);

        $this->cityService->createCity($validated);

        return redirect()->route('countries.index');
    }

    public function edit($id)
    {
        $city = City::findOrFail($id);
        return Inertia::render('Cities/Edit', [
            'city' => $city
        ]);
    }

    public function update(Request $request, $id)
    {
        $city = City::findOrFail($id);

        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'region'        => 'required|string|max:255',
            'region_code'   => 'required|string|max:255',
            'latitude'      => 'required|string|max:255',
            'longitude'     => 'required|string|max:255',
            'population'    => 'nullable|integer'
        ]);

        $this->cityService->updateCity($city, $validated);

        return redirect()->route('countries.index');
    }

    public function destroy($id)
    {
        $city = City::findOrFail($id);
        $this->cityService->deleteCity($city);
        return redirect()->route('city.index');
    }

    public function fetchFromAPI(Request $request)
    {
        try {
            $country_id = $request->input('country_id');
            if (!$country_id) {
                throw new \Exception('Missing country_id parameter');
            }

            $offset = (int) $request->input('offset', 0);
            $limit = (int) $request->input('limit', 5);

            $result = $this->cityService->fetchCitiesFromAPI($country_id, $offset, $limit);

            return redirect()->route('cities.index', ['country_code' => $request->country_code])
                ->with('success', "{$result['processedCount']} cities processed out of {$result['totalCount']}");

        } catch (\Exception $e) {
            session()->flash('error', 'Failed to fetch cities: ' . $e->getMessage());
            return redirect()->route('cities.index', ['country_code' => $request->country_code]);
        }
    }
}
