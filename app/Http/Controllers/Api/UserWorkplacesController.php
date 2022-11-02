<?php
namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Workplace;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\WorkplaceCollection;

class UserWorkplacesController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\User $user
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, User $user)
    {
        //  $this->authorize('view', $user);

        $search = $request->get('search', '');

        $workplaces = $user
            ->workplaces()
            ->search($search)
            ->latest()
            ->paginate();

        return new WorkplaceCollection($workplaces);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\User $user
     * @param \App\Models\Workplace $workplace
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, User $user, Workplace $workplace)
    {
        // $this->authorize('update', $user);

        $user->workplaces()->syncWithoutDetaching([$workplace->id]);

        return response()->noContent();
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\User $user
     * @param \App\Models\Workplace $workplace
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, User $user, Workplace $workplace)
    {
        // $this->authorize('update', $user);

        $user->workplaces()->detach($workplace);

        return response()->noContent();
    }
}
