<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use GuzzleHttp\Client;

class Embeddable implements Rule
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
        $client = new Client();
        $response = $client->get($value);
        if($response->hasHeader('X-Frame-Options') && $response->getHeader('X-Frame-Options')[0] == 'SAMEORIGIN') {
            return false;
        }
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Deze URL staat embedding niet toe.';
    }
}
