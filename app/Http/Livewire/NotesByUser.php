<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Helpers\Helpers;
use Illuminate\Support\Facades\Auth;

//Models:
use App\Models\Note;

class NotesByUser extends Component{
    /**
     * 1. Render componente.
     * 2. Eliminar nota.
     */

    /**
     * 1. Render componente.
     */
    public function render(){
        $notes = Note::where('user_id', Auth::user()->id)
        ->orderBy('created_at', 'DESC')
        ->get();

        $languages = Helpers::languages();

        return view('livewire.notes-by-user', compact('notes', 'languages'));
    }

    /**
     * 2. Eliminar nota.
     */
    public function delete(Note $note){
        $note->delete();
        $qty = Note::where('user_id', Auth::user()->id)->count();
        $this->emit('response', $qty);
        $this->render();
    }
}
