<?php

namespace App\Exceptions;

use Exception;

class FrontendProxyException extends Exception
{
    public function __construct($message = "Frontend proxy error", $code = 500, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function render($request)
    {
        return response()->json([
            'error' => 'Frontend Proxy Error',
            'message' => $this->getMessage(),
        ], $this->getCode());
    }
}
