<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\StaffPickCandidateResource;
use App\Http\Resources\StaffPickCandidateCollection;

class UserStaffPickCandidatesController extends Controller
{
    /**
     * @param Request $request
     * @param User $user
     * @return StaffPickCandidateCollection
     */
    public function index(Request $request, User $user)
    {


        $staffPickCandidates = $user
            ->staffPickCandidates()
            ->latest()
            ->paginate();

        return new StaffPickCandidateCollection($staffPickCandidates);
    }

    /**
     * @param Request $request
     * @param User $user
     * @return StaffPickCandidateResource
     */
    public function store(Request $request, User $user)
    {


        $validated = $request->validate([
            'assessment' => ['required', 'numeric'],
            'status' => ['required', 'numeric'],
        ]);

        $staffPickCandidate = $user->staffPickCandidates()->create($validated);

        return new StaffPickCandidateResource($staffPickCandidate);
    }
}
