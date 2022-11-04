<?php

namespace App\Http\Controllers\Api;

use App\Models\Company;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\QuestionResource;
use App\Http\Resources\QuestionCollection;

class CompanyQuestionsController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Company $company
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Company $company)
    {
        // $this->authorize('view', $company);

        $questions = $company
            ->questions()
            ->latest()
            ->paginate();

        return new QuestionCollection($questions);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Company $company
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Company $company)
    {
        //$this->authorize('create', Question::class);

        $validated = $request->validate([
            'name' => ['required', 'max:255', 'string'],
            'slug' => ['required', 'max:255', 'string'],
            'question' => ['nullable', 'max:255', 'string'],
            'options' => ['nullable', 'max:255', 'string'],
            'right_option' => ['nullable', 'max:255', 'string'],
            'videos' => ['nullable', 'max:255', 'string'],
            'type' => ['nullable', 'numeric'],
            'answer_type' => ['nullable', 'numeric'],
            'keywords' => ['nullable', 'max:255', 'string'],
            'keywords_min' => ['required', 'numeric'],
            'time' => ['nullable', 'numeric'],
            'status' => ['required', 'numeric'],
        ]);

        $question = $company->questions()->create($validated);

        return new QuestionResource($question);
    }
}
