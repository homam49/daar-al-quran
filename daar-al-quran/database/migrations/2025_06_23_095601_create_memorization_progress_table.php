<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMemorizationProgressTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('memorization_progress', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->integer('surah_number')->nullable(); // Surah number (103-114) for surahs only
            $table->string('surah_name')->nullable(); // Surah name in Arabic
            $table->enum('type', ['page', 'surah'])->default('page'); // Type of content
            $table->integer('page_number')->nullable(); // Page number (1-581) for pages only
            $table->string('content_name'); // Display name (either "صفحة X" or surah name)
            $table->text('content_details')->nullable(); // Additional details about the content
            $table->enum('status', ['not_started', 'in_progress', 'memorized', 'reviewed'])->default('not_started');
            $table->unsignedBigInteger('teacher_id')->nullable(); // Teacher who verified
            $table->timestamp('started_at')->nullable(); // When student started memorizing
            $table->timestamp('completed_at')->nullable(); // When memorization was completed
            $table->timestamp('last_reviewed_at')->nullable(); // Last review date
            $table->text('notes')->nullable(); // Teacher notes
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('teacher_id')->references('id')->on('users')->onDelete('set null');
            
            // Unique constraint to prevent duplicate entries for each content type
            $table->unique(['student_id', 'type', 'page_number', 'surah_number'], 'unique_student_content');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('memorization_progress');
    }
}
