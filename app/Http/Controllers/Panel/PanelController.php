<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PanelController extends Controller
{
    public function index()
    {
        return Inertia::render('Panel/Panel');
    }

    public function index2()
    {
        return Inertia::render('Dashboard');
    }
}
