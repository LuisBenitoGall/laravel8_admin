<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\Helpers;
use App\Notifications\UserMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use File;
use Carbon\Carbon;

//Models:
use App\Models\Notification;

class NotificationController extends Controller{
    /**
     * 1. Notificaciones por usuario.
     * 1.1. Listado de notificaciones no leídas por usuario para DataTable.
     * 1.2. Listado de notificaciones leídas por usuario para DataTable.
     * 2. Mostrar notificación.
     * 3. Eliminar notificación.
     * 4. Marcar - desmarcar como leída.
     * 5. Obtener nuevas notificaciones.
     */
    
    private $option = 'notificaciones'; 
    private $module = 'dashboard';
    private $languages;

    public function __construct(){
        //Idiomas:
        $this->languages = Helpers::languages();
    }
    
    /**
     * 1. Notificaciones por usuario.
     */
    public function index($tab = false){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.notificaciones_mis',
            'module' => $this->module,
            'slug' => ''
        ]; 
        
        return view('admin.notifications.index')->with(compact('header', 'tab'));  
    }

    /**
     * 1.1. Listado de notificaciones no leídas por usuario para DataTable.
     */
    public function getUnreadDT(){
        $notifications = auth()->user()->unreadNotifications;

        foreach($notifications as $row){
            //Enviamos las fechas ya formateadas:
            $row->alta = $row->created_at->format('d-m-Y H:i:s');    
        }

        return datatables($notifications)->toJson();
    }

    /**
     * 1.2. Listado de notificaciones leídas por usuario para DataTable.
     */
    public function getReadDT(){
        $notifications = auth()->user()->readNotifications;

        foreach($notifications as $row){
            //Enviamos las fechas ya formateadas:
            $row->alta = $row->created_at->format('d-m-Y H:i:s');   

            $row->leido = $row->read_at->format('d-m-Y H:i:s');
        }

        return datatables($notifications)->toJson();
    }

    /**
     * 2. Mostrar notificación.
     *
     * @param Notification $notification
     * @return \Illuminate\Http\Response
     */
    public function show(Notification $notification){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.notificacion',
            'module' => $this->module,
            'slug' => ''
        ]; 

        $notification_data = $notification->data? json_decode($notification->data):false;

        //Marcamos como leída:
        if(!$notification->read_at){
            $notification->markAsRead($notification);    
        }

        $languages = $this->languages;

        return view('admin.notifications.show')->with(compact('header', 'notification', 'notification_data', 'languages')); 
    }

    /**
     * 3. Eliminar notificación.
     *
     * @param Notification $notification
     * @return \Illuminate\Http\Response
     */
    public function destroy(Notification $notification){
        $tab = $notification->read_at? 'read':'';
        $notification->delete();

        $msg = trans('textos.notificacion_deleted');
        return redirect()->route('notifications.index', [$tab])->with(compact('msg'));    
    }

    /**
     * 4. Marcar - desmarcar como leída.
     */
    public function readStatus(Request $request, Notification $notification, $status){
        if($request->ajax()){
            if($status){
                $notification->markAsRead($notification);
            }else{
                $notification->read_at = NULL;
                $notification->save();    
            }

            return response()->json($status);
        }    
    }

    /**
     * 5. Obtener nuevas notificaciones.
     */
    public function newNotifications(Request $request){
        if($request->ajax()){    
            $notifications = auth()->user()->unreadNotifications;

            if($notifications->count()){
                //Ampliando info objetos:
                foreach($notifications as $notification){
                    $notification->time_ago = $notification->created_at->diffForHumans();
                }

                return response()->json($notifications);
            }else{
                return response()->json(false);    
            }
        }
    }
}
