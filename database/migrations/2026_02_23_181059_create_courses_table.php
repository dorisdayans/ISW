<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->string('code')->unique();
            $table->string('course_name');

            // Enum numérico: 1=Por iniciar, 2=En curso, 3=Finalizado
            $table->smallInteger('status');

            $table->integer('capacity');
            $table->integer('enrolled_count')->default(0);
            $table->integer('available_seats')->default(0);

            $table->date('start_date');
            $table->time('start_time');

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
