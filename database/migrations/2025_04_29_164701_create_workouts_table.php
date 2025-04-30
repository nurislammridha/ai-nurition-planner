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
        Schema::create('workouts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedInteger('age');
            $table->float('height');
            $table->float('weight');
            $table->string('fitness_goals');
            $table->string('gender');
            $table->string('training_level');
            $table->string('preferred_training_style');
            $table->string('training_days_per_week');
            $table->string('preferred_session_length');
            $table->string('lifestyle_activity_level');
            $table->string('stress_level');
            $table->string('sleep_quality');
            $table->string('plan_duration');
            $table->text('workout_plan')->nullable();
            $table->json('injuries_health_conditions')->nullable();
            $table->json('available_equipments')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workouts');
    }
};
