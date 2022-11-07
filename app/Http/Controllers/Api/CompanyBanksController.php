<?php
namespace App\Http\Controllers\Api;

use App\Models\Bank;
use App\Models\Company;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\BankCollection;

class CompanyBanksController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Company $company
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Company $company)
    {
        //  $this->authorize('view', $company);

        $search = $request->get('search', '');

        $banks = $company
            ->banks()
            ->search($search)
            ->latest()
            ->paginate();

        return new BankCollection($banks);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Company $company
     * @param \App\Models\Bank $bank
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Company $company, Bank $bank)
    {
        // $this->authorize('update', $company);

        $company->banks()->syncWithoutDetaching([$bank->id]);

        return response()->noContent();
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Company $company
     * @param \App\Models\Bank $bank
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Company $company, Bank $bank)
    {
        // $this->authorize('update', $company);

        $company->banks()->detach($bank);

        return response()->noContent();
    }
}
