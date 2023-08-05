<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

//Models:
use App\Models\User;

class EmailNotUnique implements Rule{
    protected $confirm_email;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($confirm_email){
        $this->confirm_email = $confirm_email;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value){
        if($this->confirm_email){
            return true; 
        }else{
            return User::select('id')
            ->where('email', $value)
            ->doesntExist();
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(){
        return trans('textos.email_existe');
    }
}
