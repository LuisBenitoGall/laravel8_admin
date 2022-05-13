<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Helpers\Helpers;
use Illuminate\Support\Facades\Auth;

//Models:
use App\Models\Note;

class NoteCreate extends Component{
    /**
     * 1. Render componente.
     * 2. Guardar nota.
     */
    
    public $title;
    public $note;

    protected $rules = [
        'title' => 'required|string|max:255',
        'note' => 'required'
    ];
    
    /**
     * 1. Render componente.
     */
    public function render(){
        return view('livewire.note-create');
    }

    /**
     * 2. Guardar nota.
     */
    public function store(){
        $this->validate();

        dd($this->title);

        $note = Note::create([
            'user_id' => Auth::user()->id,
            'title' => $this->title,
            'note' => $this->note    
        ]);

        $this->emit('response');
    }
}
