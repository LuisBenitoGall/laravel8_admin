<?php
namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Workplace;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserCollection;

class WorkplaceUsersController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Workplace $workplace
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Workplace $workplace)
    {
        //  $this->authorize('view', $workplace);

        $search = $request->get('search', '');

        $users = $workplace
            ->users()
            ->search($search)
            ->latest()
            ->paginate();

        return new UserCollection($users);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Workplace $workplace
     * @param \App\Models\User $user
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Workplace $workplace, User $user)
    {
        // $this->authorize('update', $workplace);

        $workplace->users()->syncWithoutDetaching([$user->id]);

        return response()->noContent();
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Workplace $workplace
     * @param \App\Models\User $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Workplace $workplace, User $user)
    {
        // $this->authorize('update', $workplace);

        $workplace->users()->detach($user);

        return response()->noContent();
    }
}
