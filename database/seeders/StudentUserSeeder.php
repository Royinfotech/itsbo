<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StudentUser;
use App\Models\Student;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class StudentUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function () {
            // First create students with their information
            $testStudents = [
                [
                    'student_id' => 'ST001',
                    'student_name' => 'Juan Dela Cruz',
                    'year_level' => '3',
                    'birthdate' => '2002-01-01',
                    'age' => 21,
                    'birthplace' => 'Cebu City',
                    'email' => 'juan@example.com',
                    'username' => 'juan2023',
                    'password' => Hash::make('password123'), // Added password field
                    'photo' => null,
                    'status' => 'active'
                ],
                [
                    'student_id' => 'ST002',
                    'student_name' => 'Maria Santos',
                    'year_level' => '2',
                    'birthdate' => '2003-05-15',
                    'age' => 20,
                    'birthplace' => 'Manila City',
                    'email' => 'maria@example.com',
                    'username' => 'maria2023',
                    'password' => Hash::make('password123'), // Added password field
                    'photo' => null,
                    'status' => 'active'
                ]
            ];

            foreach ($testStudents as $studentData) {
                // Create student record first
                $student = Student::updateOrCreate(
                    ['student_id' => $studentData['student_id']],
                    $studentData
                );

                // Only create student_users entry if student is active (approved)
                if ($student->status === 'active') {
                    // Create login credentials in student_users table
                    StudentUser::updateOrCreate(
                        ['student_id' => $student->student_id],
                        [
                            'student_id' => $student->student_id,
                            'email' => $student->email,
                            'password' => Hash::make('password123')
                        ]
                    );

                    $this->command->info("Created student user account for: {$student->student_name}");
                }
            }
        });
    }
}
