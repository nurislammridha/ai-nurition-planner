<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreWorkoutPlannerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'age' => 'required|integer',
            'height' => 'required|numeric',
            'weight' => 'required|numeric',
            'fitness_goals' => 'required|string',
            'gender' => 'required|string',
            'training_level' => 'required|string',
            'preferred_training_style' => 'required|string',
            'training_days_per_week' => 'required|string',
            'preferred_session_length' => 'required|string',
            'lifestyle_activity_level' => 'required|string',
            'stress_level' => 'required|string',
            'sleep_quality' => 'required|string',
            // 'injuries_health_conditions' => 'nullable|array',
            // 'available_equipments' => 'nullable|array',
            'plan_duration' => 'required|string', // e.g., 7, 15, 30 days
        ];
    }
}
