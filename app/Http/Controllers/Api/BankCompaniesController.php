<?php
namespace App\Http\Controllers\Api;

use App\Models\Bank;
use App\Models\Company;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\CompanyCollection;

class BankCompaniesController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Bank $bank
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Bank $bank)
    {
        //  $this->authorize('view', $bank);

        $search = $request->get('search', '');

        $companies = $bank
            ->companies()
            ->search($search)
            ->latest()
            ->paginate();

        return new CompanyCollection($companies);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Bank $bank
     * @param \App\Models\Company $company
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Bank $bank, Company $company)
    {
        // $this->authorize('update', $bank);

        $bank->companies()->syncWithoutDetaching([$company->id]);

        return response()->noContent();
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Bank $bank
     * @param \App\Models\Company $company
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Bank $bank, Company $company)
    {
        // $this->authorize('update', $bank);

        $bank->companies()->detach($company);

        return response()->noContent();
    }
}
