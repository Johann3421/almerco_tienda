<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Category;
use App\Models\Group;
use App\Models\Subgroup;

class CategoryWebController extends Controller
{
    public function index()
    {
        $Subgroup = Category::with('groups')->get();
        return $Subgroup;
    }
}
