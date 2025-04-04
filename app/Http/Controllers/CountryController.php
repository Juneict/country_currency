<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Services\CountryService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CountryController extends Controller
{
    protected $countryService;

    public function __construct(CountryService $countryService)
    {
        $this->countryService = $countryService;
    }

    public function index()
    {
        return Inertia::render('Countries/Index', [
            'countries' => $this->countryService->getAllCountries(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Countries/Create');
    }

    public function store(Request $request)
    {
        $countryValidated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:countries',
            'currency_code' => 'nullable|string|max:255',
            'wikiDataId' => 'nullable|string|max:255',
        ]);

        $currencyValidated = $request->validate([
            'currency.code' => 'required|string|max:10',
            'currency.symbol' => 'nullable|string|max:10',
            'currency.name' => 'nullable|string|max:255',
        ]);

        $this->countryService->createCountry($countryValidated, $currencyValidated['currency']);

        return redirect()->route('countries.index');
    }

    public function show($id)
    {
        return Inertia::render('Countries/Show', [
            'country' => $this->countryService->getCountry($id)
        ]);
    }

    public function edit($id)
    {
        return Inertia::render('Countries/Edit', [
            'country' => $this->countryService->getCountry($id)
        ]);
    }

    public function update(Request $request, $id)
    {
        $country = Country::findOrFail($id);

        $countryValidated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:countries,code,' . $country->id,
            'currency_code' => 'nullable|string|max:255',
            'wikiDataId' => 'nullable|string|max:255',
        ]);

        $currencyValidated = $request->validate([
            'currency.code' => 'required|string|max:10',
            'currency.symbol' => 'nullable|string|max:10',
        ]);

        $this->countryService->updateCountry($country, $countryValidated, $currencyValidated['currency']);

        return redirect()->route('countries.index');
    }

    public function destroy($id)
    {
        $country = Country::findOrFail($id);
        $this->countryService->deleteCountry($country);
        return redirect()->route('countries.index');
    }

    public function fetchFromAPI(Request $request)
    {
        try {
            $offset = (int) $request->input('offset', 0);
            $limit = (int) $request->input('limit', 5);

            $result = $this->countryService->fetchCountriesFromAPI($offset, $limit);

            return redirect()->route('countries.index')
                ->with('success', "{$result['processedCount']} countries processed out of {$result['totalCount']}");

        } catch (\Exception $e) {
            session()->flash('error', 'Failed to fetch countries: ' . $e->getMessage());
            return redirect()->route('countries.index');
        }
    }
}
