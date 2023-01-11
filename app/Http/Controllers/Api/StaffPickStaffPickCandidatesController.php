<?php

namespace App\Http\Controllers\Api;

use App\Models\StaffPick;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\StaffPickCandidateResource;
use App\Http\Resources\StaffPickCandidateCollection;

class StaffPickStaffPickCandidatesController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\StaffPick $staffPick
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, StaffPick $staffPick)
    {
        // $this->authorize('view', $staffPick);

        $staffPickCandidates = $staffPick
            ->staffPickCandidates()
            ->latest()
            ->paginate();

        return new StaffPickCandidateCollection($staffPickCandidates);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\StaffPick $staffPick
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, StaffPick $staffPick)
    {
        //$this->authorize('create', StaffPickCandidate::class);

        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'assessment' => ['required', 'numeric'],
            'status' => ['required', 'numeric'],
        ]);

        $staffPickCandidate = $staffPick
            ->staffPickCandidates()
            ->create($validated);

        return new StaffPickCandidateResource($staffPickCandidate);
    }
}
