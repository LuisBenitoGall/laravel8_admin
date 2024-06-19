<?php

namespace App\Http\Controllers\Api;

use App\Models\Company;
use App\Models\CompanyModules;
use App\Models\StaffPick;
use App\Models\StaffPickCandidates;
use App\Models\StaffPickObservations;
use App\Models\User;
use App\Models\UserDoc;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\StaffPickResource;
use App\Http\Resources\StaffPickCollection;

class CompanyStaffPicksController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Company $company
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Company $company)
    {
        // $this->authorize('view', $company);
        $slug = 'timetables';
        $companies = Company::where('name', 'LIKE', '%ADN%')->get();

        $staffPicks = StaffPick::whereHas('company', function ($q) use ($companies) {
            $q->whereIn('id', $companies->pluck('id'));
        })->where('status', true)
            ->latest()
            ->paginate();
        //  dd($companies->pluck('id'));
        /*  $staffPicks = $company
              ->staffPicks()
              ->latest()
              ->paginate();
*/
        return new StaffPickCollection($staffPicks);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Company $company
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Company $company)
    {
        //$this->authorize('create', StaffPick::class);

        $validated = $request->validate([
            'name' => ['required', 'max:255', 'string'],
            'surname' => ['required', 'max:255', 'string'],
            'email' => ['required', 'email'],
            'process_id' => ['required', 'max:99999999', 'numeric'],
            'observations' => ['required', 'string'],
            'doc' => ['required', 'mimes:pdf,jpeg,png'],
        ]);


        if (User::where('email', '=', $request->email)->exists()) {
        $user =    User::where('email', '=', $request->email)->first();
        } else {
            $user = new User();
            $user->name = $request->name;
            $user->surname = $request->surname;
            $user->email = $request->email;
            $user->save();
        }


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
        $doc_data = $this->saveDoc($request, $user->id);

        if( $request->has('with_doc_data') && $request->with_doc_data == true ) {
            $result_data = [
                'user' => $user,
                'doc' => $doc_data,
            ];
            return response()->json($result_data, 201);
        }

        return new StaffPickResource($user);
    }

    public function saveDoc($request, $user_id)
    {
        if ($request->hasFile('doc')) {
            $file = $request->file('doc');
            $path = public_path() . '/docs/users';
            $fileName = uniqid() . $file->getClientOriginalName();
            $file->move($path, $fileName);

            $doc = new UserDoc();
            $doc->doc = $fileName;
            $doc->user_id = $user_id;
            $doc->type = 'cv';
            $save_success = $doc->save();

            // si el guardado ha sido correcto, devolvemos la información del documento
            if ($save_success) {
                return [
                    'success' => true,
                    'file' => $fileName,
                    'url' => url('/docs/users/' . $fileName),
                ];
            }
        }

        // si no se encuentra el archivo, devolvemos un array vacío
        return [
            'success' => false,
            'file' => '',
            'url' => '',
        ];
    }
}
