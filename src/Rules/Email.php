<?php

namespace Blurpa\EasyValidator\Rules;

use Blurpa\EasyValidator\Rule;

class Email extends Rule
{
    protected $message = 'The email field is an invalid format.';

    public function validate($item, $options)
    {
        return filter_var($item, FILTER_VALIDATE_EMAIL);
    }
}
