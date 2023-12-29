<?php
// app/Exceptions/AuthorizationException.php

class AuthorizationException extends Exception
{
    public function render($request)
    {
        return response()->json([
            'message' => 'Forbidden access.',
            'code' => 403,
        ], 403);
    }
}

