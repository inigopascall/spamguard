<?php

namespace App\Exceptions;

use Exception;

class SpamGuardUnauthorized extends Exception
{
    public function report(Exception $exception)
    {
        // ...report silently
    }

    public function render ()
    {
        return response()->view('errors/bannedbyspamguard', [], 403);
    }
}
