<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NutritionInput extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'age',
        'height',
        'weight',
        'gender',
        'goal',
        'plan_duration',
        'meals_per_day',
        'diet_type',
        'health_conditions',
        'allergies',
        'nutrition_plan'
    ];

    protected $casts = [
        'health_conditions' => 'array',
        'allergies' => 'array',
    ];
}
