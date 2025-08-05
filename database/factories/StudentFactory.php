<?php

namespace Database\Factories;

use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class StudentFactory extends Factory
{
    protected $model = Student::class;

    public function definition()
    {
        $yearLevels = ['1st Year', '2nd Year', '3rd Year', '4th Year'];
        
        return [
            'student_name' => fake()->name(),
            'student_id' => 'ST' . str_pad(fake()->unique()->randomNumber(3), 3, '0', STR_PAD_LEFT),
            'year_level' => fake()->randomElement($yearLevels),
            'birthdate' => fake()->dateTimeBetween('-22 years', '-17 years'),
            'age' => fake()->numberBetween(17, 22),
            'birthplace' => fake()->city(),
            'email' => fake()->unique()->safeEmail(),
            'username' => fake()->unique()->userName(),
            'password' => Hash::make('password123'), // Default password for testing
            'photo' => null,
            'status' => 'active'
        ];
    }
}