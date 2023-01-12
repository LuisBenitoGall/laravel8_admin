<?php

namespace App\Http\Controllers\Api;

use App\Models\StaffPick;
use App\Models\StaffPickCandidates;
use App\Models\StaffPickObservations;
use App\Models\User;
use App\Models\UserDoc;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\StaffPickResource;
use App\Http\Resources\StaffPickCollection;
use App\Http\Requests\StaffPickStoreRequest;
use App\Http\Requests\StaffPickUpdateRequest;
use Illuminate\Http\Response;

class StaffPickController extends Controller
{
    /**
     * @param Request $request
     * @return StaffPickCollection
     */
    public function index(Request $request)
    {


        $staffPicks = StaffPick::latest()->paginate();

        return new StaffPickCollection($staffPicks);
    }

    /**
     * @param \App\Http\Requests\StaffPickStoreRequest $request
     * @return Response
     */
    public function store(Request $request)
    {


        $validated = $request->validated();
        $user = new User();
        $user->name = $request->name;
        $user->surname = $request->surname;
        $user->email = $request->email;
        $user->save();


        //Creamos candidatura:
        $spc = new StaffPickCandidates();
        $spc->staff_pick_id = $request->process_id;
        $spc->user_id = $user->id;
        $spc->save();

        //Guardamos comentarios:
        $spo = new StaffPickObservations();
        $spo->candidate_id = $spc->id;
        $spo->meeting_date = now();
        $spo->observations = $request->observations;
        $spo->save();

        //Guardamos imagen:
     //   $this->saveImage($request, $user_id);

        //Guardamos documento:
        $this->saveDoc($request, $user->id);

        return new StaffPickResource($user);
    }

    /**
     * @param Request $request
     * @param \App\Models\StaffPick $staffPick
     * @return Response
     */
    public function show(Request $request, StaffPick $staffPick)
    {


        return new StaffPickResource($staffPick);
    }

    /**
     * @param \App\Http\Requests\StaffPickUpdateRequest $request
     * @param \App\Models\StaffPick $staffPick
     * @return Response
     */
    public function update(
        StaffPickUpdateRequest $request,
        StaffPick              $staffPick
    )
    {


        $validated = $request->validated();

        $staffPick->update($validated);

        return new StaffPickResource($staffPick);
    }

    /**
     * @param Request $request
     * @param \App\Models\StaffPick $staffPick
     * @return Response
     */
    public function destroy(Request $request, StaffPick $staffPick)
    {

        $staffPick->delete();

        return response()->noContent();
    }

    public function saveDoc($request, $user_id){
        if($request->hasFile('doc')){
            $file = $request->file('doc');
            $path = public_path().'/docs/users';
            $fileName = uniqid().$file->getClientOriginalName();
            $file->move($path, $fileName);

            $doc = new UserDoc();
            $doc->doc = $fileName;
            $doc->user_id = $user_id;
            $doc->type = 'cv';
            $doc->save();
        }
    }
}
