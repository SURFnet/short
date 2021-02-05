<?php

namespace App\Exception;

class ShortUserException extends \Exception
{
    protected $message = "ShortUrl does not exist";
}
