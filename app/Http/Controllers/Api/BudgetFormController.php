<?php

namespace App\Http\Controllers\Api;

use App\Models\BudgetForms as BudgetForm;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\BudgetFormResource;
use App\Http\Resources\BudgetFormCollection;
use App\Http\Requests\BudgetFormStoreRequest;
use App\Http\Requests\BudgetFormUpdateRequest;

class BudgetFormController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // $this->authorize('view-any', BudgetForm::class);

        //$search = $request->get('search', '');

        $budgetForms = BudgetForm::latest()->paginate();

        return new BudgetFormCollection($budgetForms);
    }

    /**
     * @param \App\Http\Requests\BudgetFormStoreRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(BudgetFormStoreRequest $request)
    {
        // $this->authorize('create', BudgetForm::class);

        $validated = $request->validated();

        $budgetForm = BudgetForm::create($validated);

        return new BudgetFormResource($budgetForm);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\BudgetForm $budgetForm
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, BudgetForm $budgetForm)
    {
        // $this->authorize('view', $budgetForm);

        return new BudgetFormResource($budgetForm);
    }

    /**
     * @param \App\Http\Requests\BudgetFormUpdateRequest $request
     * @param \App\Models\BudgetForm $budgetForm
     * @return \Illuminate\Http\Response
     */
    public function update(
        BudgetFormUpdateRequest $request,
        BudgetForm $budgetForm
    ) {
        // $this->authorize('update', $budgetForm);

        $validated = $request->validated();

        $budgetForm->update($validated);

        return new BudgetFormResource($budgetForm);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\BudgetForm $budgetForm
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, BudgetForm $budgetForm)
    {
        //$this->authorize('delete', $budgetForm);

        $budgetForm->delete();

        return response()->noContent();
    }
}
