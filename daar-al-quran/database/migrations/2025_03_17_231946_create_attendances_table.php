<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->enum('status', ['present', 'absent', 'late']);
            $table->text('note')->nullable();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('class_session_id');
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('class_session_id')->references('id')->on('class_sessions')->onDelete('cascade');
            $table->unique(['student_id', 'class_session_id']); // Prevent duplicate attendance records
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attendances');
    }
}
