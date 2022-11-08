<?php

namespace App\Http\Controllers\Api;

use App\Models\Bank;
use Illuminate\Http\Request;
use App\Http\Resources\BankResource;
use App\Http\Controllers\Controller;
use App\Http\Resources\BankCollection;
use App\Http\Requests\BankStoreRequest;
use App\Http\Requests\BankUpdateRequest;

class BankController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // $this->authorize('view-any', Bank::class);

        //$search = $request->get('search', '');

        $banks = Bank::latest()->paginate();

        return new BankCollection($banks);
    }

    /**
     * @param \App\Http\Requests\BankStoreRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(BankStoreRequest $request)
    {
        // $this->authorize('create', Bank::class);

        $validated = $request->validated();

        $bank = Bank::create($validated);

        return new BankResource($bank);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Bank $bank
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Bank $bank)
    {
        // $this->authorize('view', $bank);

        return new BankResource($bank);
    }

    /**
     * @param \App\Http\Requests\BankUpdateRequest $request
     * @param \App\Models\Bank $bank
     * @return \Illuminate\Http\Response
     */
    public function update(BankUpdateRequest $request, Bank $bank)
    {
        // $this->authorize('update', $bank);

        $validated = $request->validated();

        $bank->update($validated);

        return new BankResource($bank);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Bank $bank
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Bank $bank)
    {
        //$this->authorize('delete', $bank);

        $bank->delete();

        return response()->noContent();
    }
}
