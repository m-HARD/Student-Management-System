<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Classes;
use Illuminate\Http\Request;
use App\Http\Resources\ClassesResource;
use App\Http\Resources\StudentResource;
use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $studentQuery = Student::search($request);
        $classes = ClassesResource::collection(Classes::all());

        return inertia('Students/Index', [
            'students' => StudentResource::collection(
                $studentQuery->paginate(10)
            ),
            'classes' => $classes,
            'search' => request('search') ?? ''
        ]);
    }

    public function create()
    {
        $classes = ClassesResource::collection(Classes::all());
        return inertia('Students/Create', [
            "classes" => $classes
        ]);
    }

    public function show(Student $student)
    {
        return inertia(
            'Students/Show',
            ['student' => StudentResource::make($student)]
        );
    }

    public function store()
    {
        //getting the parent data from the request 
        $parent_data = [
            'name' => request('parent_name'),
            'email' =>  request('parent_email'),
            'phone'  => request('phone'),
            'address'  => request('address'),
            'house_number' => request('house_number')
        ];
        $parent =  $this->createOrUpdateParent($parent_data);
        //getting the student data from the request 
        $studentData = [
            'name' => request('name'),
            'email' =>  request('email'),
            'class_id' => request('class_id'),
            'parent_id' => $parent->id,
        ];
        Student::create($studentData);

        return redirect()->route('students.index');
    }

    public function edit(Student $student)
    {
        $classes = ClassesResource::collection(Classes::all());

        return inertia('Students/Edit', [
            "classes" => $classes,
            "student" => StudentResource::make($student)
        ]);
    }
    public function update(UpdateStudentRequest $request, Student $student)
    {
        //update the record with the validated user data
        $student->update($request->validated());
        //redirect to see the changes implemented
        return redirect()->route('students.index');
    }

    public function destroy(Student $student)
    {
        $student->delete();

        return redirect()->route('students.index');
    }

    protected function createOrUpdateParent($data)
    {
        $student = new Student;
        request()->validate([
            'parent_name' => ['required', 'min:3'],
            'parent_email' => ['required', 'email'],
            'phone' => ['required', 'min:9'],
            'address' => ['nullable', 'string'],
            'house_number' => ['nullable']
        ]);
        $parent = $student->parent()->updateOrCreate(['id' => $student->parent_id], [
            'parent_name' => request('parent_name'),
            'parent_email' => request('parent_email'),
            'phone' => request('phone'),
            'address' => request('address'),
            'house_number' => request('house_number')
        ]);
        return $parent;
    }
}
