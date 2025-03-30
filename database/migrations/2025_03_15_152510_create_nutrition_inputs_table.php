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
        Schema::create('nutrition_inputs', function (Blueprint $table) {
            $table->id();
            $table->integer('age');
            $table->float('height');
            $table->float('weight');
            $table->string('gender');
            $table->string('name');
            $table->string('plan_duration');
            $table->string('goal');
            $table->string('diet_type');
            $table->integer('meals_per_day');
            $table->json('health_conditions')->nullable();
            $table->json('allergies')->nullable();
            $table->text('nutrition_plan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nutrition_inputs');
    }
};
