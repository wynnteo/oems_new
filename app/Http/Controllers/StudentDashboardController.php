<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use stdClass;

class StudentDashboardController extends Controller
{
    public function index() 
    {
        return view('student.dashboard');
    }
}