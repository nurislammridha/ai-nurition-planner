<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Workout extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'age',
        'height',
        'weight',
        'fitness_goals',
        'gender',
        'training_level',
        'preferred_training_style',
        'training_days_per_week',
        'preferred_session_length',
        'lifestyle_activity_level',
        'stress_level',
        'sleep_quality',
        'injuries_health_conditions',
        'available_equipments',
        'plan_duration',
        'workout_plan'
    ];

    protected $casts = [
        'injuries_health_conditions' => 'array',
        'available_equipments' => 'array',
    ];
}
