<?php

namespace App\ServiceQuiz;

class TokenService
{
    static public function generateToken()
    {
        return sha1(random_bytes(40));
    }
}
