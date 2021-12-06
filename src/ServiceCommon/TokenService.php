<?php

namespace App\ServiceCommon;

class TokenService
{
    static public function generateToken()
    {
        return sha1(random_bytes(40));
    }
}
