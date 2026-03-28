<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OnboardingController extends Controller
{
    /**
     * Mostrar pantalla de bienvenida
     */
    public function bienvenida()
    {
        return view('onboarding.bienvenida');
    }
}
