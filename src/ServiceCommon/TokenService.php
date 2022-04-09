<?php

namespace App\ServiceCommon;

use Exception;

class TokenService
{
    /**
     * @return string
     *
     * @throws Exception
     */
    static public function generateToken(): string
    {
        return sha1(random_bytes(40));
    }
}
