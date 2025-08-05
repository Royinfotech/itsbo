<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('event_id');
            $table->string('student_id');
            $table->date('attendance_date');
            
            // Morning attendance with status and remarks
            $table->boolean('am_in')->default(false);
            $table->timestamp('am_in_time')->nullable();
            $table->string('am_in_status')->nullable(); // present, late, absent
            $table->text('am_in_remarks')->nullable();
            
            $table->boolean('am_out')->default(false);
            $table->timestamp('am_out_time')->nullable();
            $table->string('am_out_status')->nullable();
            $table->text('am_out_remarks')->nullable();
            
            // Afternoon attendance with status and remarks
            $table->boolean('pm_in')->default(false);
            $table->timestamp('pm_in_time')->nullable();
            $table->string('pm_in_status')->nullable();
            $table->text('pm_in_remarks')->nullable();
            
            $table->boolean('pm_out')->default(false);
            $table->timestamp('pm_out_time')->nullable();
            $table->string('pm_out_status')->nullable();
            $table->text('pm_out_remarks')->nullable();
            
            $table->timestamps();

            // Foreign keys with proper indexing
            $table->foreign('event_id')
                  ->references('event_id')
                  ->on('events')
                  ->onDelete('cascade');
            
            $table->foreign('student_id')
                  ->references('student_id')
                  ->on('students')
                  ->onDelete('cascade');

            // Add indexes for better query performance
            $table->index('attendance_date');
            $table->index(['event_id', 'attendance_date']);
            $table->index(['student_id', 'attendance_date']);

            // Unique constraint to prevent duplicate attendance records
            $table->unique(['event_id', 'student_id', 'attendance_date'], 'unique_attendance');
        });
    }

    public function down()
    {
        Schema::dropIfExists('attendances');
    }
};