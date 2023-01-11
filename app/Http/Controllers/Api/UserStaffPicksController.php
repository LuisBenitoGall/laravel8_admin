<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\StaffPickResource;
use App\Http\Resources\StaffPickCollection;

class UserStaffPicksController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\User $user
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, User $user)
    {
        // $this->authorize('view', $user);

        $staffPicks = $user
            ->staffPicks2()
            ->latest()
            ->paginate();

        return new StaffPickCollection($staffPicks);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\User $user
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, User $user)
    {
        //$this->authorize('create', StaffPick::class);

        $validated = $request->validate([
            'name' => ['nullable', 'max:255', 'string'],
            'slug' => ['nullable', 'max:255', 'string'],
            'excerpt' => ['nullable', 'max:255', 'string'],
            'content' => ['nullable', 'max:255', 'string'],
            'published_at' => ['required', 'date'],
            'published_end' => ['required', 'date'],
            'company_id' => ['required', 'exists:companies,id'],
        ]);

        $staffPick = $user->staffPicks2()->create($validated);

        return new StaffPickResource($staffPick);
    }
}
