<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentsTable extends Migration
{
    public function up()
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('student_name');
            $table->string('student_id')->unique();
            $table->integer('year_level');
            $table->date('birthdate');
            $table->integer('age');
            $table->string('birthplace');
            $table->string('email')->unique();
            $table->string('username')->unique();
            $table->string('password');
            $table->timestamps();
            $table->string('photo')->nullable();
            $table->string('status')->default('pending');

        });
    }

    public function down()
    {
        Schema::dropIfExists('students');
    }
}