<?php

namespace App\Http\Controllers\v1\Auth;

use App\Http\Controllers\Controller;
use App\Traits\Auth\ResetsPasswords;

class ResetPasswordController extends Controller
{
    use ResetsPasswords;
}
