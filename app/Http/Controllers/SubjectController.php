<?php

namespace App\Http\Controllers;

use App\Http\Resources\SubjectResource;
use App\Models\Subject;
use App\Models\Classes;
use App\Http\Resources\ClassesResource;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index()
    {

        $subjects = SubjectResource::collection(Subject::orderBy('class_id', 'asc')->get());
        return inertia('Subjects/Index', ['subjects' => $subjects]);
    }

    public function create()
    {
        $classes = ClassesResource::collection(Classes::all());
        return inertia('Subjects/Create' , ['classes' => $classes]);
    }
}
