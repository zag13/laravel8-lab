<?php

namespace App\Http\Rules;

use Illuminate\Contracts\Validation\Rule;

class CustomLimitRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (php_sapi_name() != 'cli' && $value > 500) return false;
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return '最大查询数量不可超过500条';
    }
}
