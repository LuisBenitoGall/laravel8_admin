<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use File;

//Models:
use App\Models\Vehicle;
use App\Models\VehicleImages;

class VehicleImagesController extends Controller{
    /**
     * 1. Guardar imagen.
     * 2. Eliminar imagen.
     * 3. Destacar imagen.
     * 4. Imagen thumbnail.
     */
    
    private $option = 'vehiculos';
    private $module = 'logistics';

    public function __construct(){
        $this->middleware('module_setted:logistics');
    }

    /**
     * 1. Guardar imagen.
     *
     * @param \Illuminate\Http\Request $request $id
     */
    public function store(Request $request, $vehicle){
        if($request->ajax()){ 
            if($request->hasFile('file')){
                $file = $request->file('file');
                $path = public_path().'/img/vehicles';
                $fileName = Helpers::slugFiles(uniqid().$file->getClientOriginalName());
                $moved = $file->move($path, $fileName);

                //si el archivo ha sido guardado correctamente:
                if($moved){
                    $img = new VehicleImages;
                    $img->image = $fileName;
                    $img->vehicle_id = $vehicle;
                    $img->save();
                }else{
                    return false;
                }
            }   
        }
    }

    /**
     * 2. Eliminar imagen.
     *
     * @param  obj $request string $image
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $vehicle, $image){
        if($image != ''){
            $foto = VehicleImages::select('image')
            ->where('id', $image)
            ->first();

            File::delete(public_path().'/img/vehicles/'.$foto->image);
            VehicleImages::where('id', $image)->delete();

            $msg = trans('textos.imagen_deleted');
            return redirect()->route('vehicles.edit', [$vehicle, 'images'])->with(compact('msg'));  
        
        }else{
            return redirect('/');
        } 
    } 

    /**
     * 3. Destacar imagen.
     */
    public function setFeatured(Request $request, $vehicle, $image){
        if($vehicle && $image != ''){   
            DB::table('vehicle_images')
            ->where('vehicle_id', $vehicle)
            ->update(['featured' => 0]);

            $img = VehicleImages::find($image);
            $img->featured = 1;
            $img->save();

        }else{
            return redirect('/');
        }  
    }

    /**
     * 4. Imagen thumbnail.
     */
    public function setThumb(Request $request, $vehicle, $image){
        if($vehicle && $image != ''){   
            DB::table('vehicle_images')
            ->where('vehicle_id', $vehicle)
            ->update(['thumbnail' => 0]);

            $img = VehicleImages::find($image);
            $img->thumbnail = 1;
            $img->save();

        }else{
            return redirect('/');
        }  
    }
}
