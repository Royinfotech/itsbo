<?php

namespace App\Services;

use App\Models\Student;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class StudentService
{
    public function registerStudent(array $data)
    {
        $this->validate($data);

        $student = new Student();
        $student->student_name = $data['student_name'];
        $student->student_id = $data['student_id'];
        $student->year_level = $data['year_level'];
        $student->birthdate = $data['birthdate'];
        $student->age = $data['age'];
        $student->birthplace = $data['birthplace'];
        $student->email = $data['email'];
        $student->username = $data['username'];
        $student->password = Hash::make($data['password']);
        $student->save();

        return $student;
    }

    protected function validate(array $data)
    {
        $validator = \Validator::make($data, [
            'student_name' => 'required|string|max:255',
            'student_id' => 'required|string|max:255|unique:students',
            'year_level' => 'required|integer',
            'birthdate' => 'required|date',
            'age' => 'required|integer',
            'birthplace' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:students',
            'username' => 'required|string|max:255|unique:students',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }
}