<?php

namespace App\Http\Controllers\Api;

use App\Models\Question;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\AnswerResource;
use App\Http\Resources\AnswerCollection;

class QuestionAnswersController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Question $question
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Question $question)
    {
        // $this->authorize('view', $question);

        $answers = $question
            ->answers()
            ->latest()
            ->paginate();

        return new AnswerCollection($answers);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Question $question
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Question $question)
    {
        //$this->authorize('create', Answer::class);

        $validated = $request->validate([
            'name' => ['required', 'max:255', 'string'],
            'slug' => ['required', 'max:255', 'string'],
            'answer' => ['required', 'max:255', 'string'],
            'points' => ['nullable', 'numeric'],
            'is_true' => ['nullable', 'boolean'],
            'status' => ['required', 'numeric'],
        ]);

        $answer = $question->answers()->create($validated);

        return new AnswerResource($answer);
    }
}
