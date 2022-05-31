<?php

namespace App\View\Components;

use Illuminate\View\Component;

class ratingStars extends Component{
    public $name;
    public $points;
    public $stars;
    
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(
        $name,
        $points,
        $stars = 5        
    ){
        $this->name = $name;
        $this->points = $points;
        $this->stars = $stars;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render(){
        return view('components.rating-stars');
    }
}
