<?php

namespace App\Http\Controllers;

use App\Models\Workout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WorkoutController extends Controller
{
    public function index()
    {
        $workouts = Workout::latest()->get();
        return view('workout.index', compact('workouts'));
    }

    public function create()
    {
        return view('workout.create');
    }

    public function store(Request $request)
    {
        $request->validate([
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
        ]);
        $openaiResponse = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
            'Content-Type' => 'application/json',
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are a certified personal trainer and expert workout planner. Your plans must consider safety, training level, injuries, and available equipment. Only suggest exercises the user can safely perform with the equipment they have.'
                ],
                [
                    'role' => 'user',
                    'content' =>
                    "Please create a personalized workout plan for {$request->plan_duration} days based on the following information:\n\n" .
                        "Name: {$request->name}\n" .
                        "Age: {$request->age}\n" .
                        "Gender: {$request->gender}\n" .
                        "Height: {$request->height} cm\n" .
                        "Weight: {$request->weight} kg\n" .
                        "Fitness Goal: {$request->fitness_goals}\n" .
                        "Training Level: {$request->training_level}\n" .
                        "Preferred Training Style: {$request->preferred_training_style}\n" .
                        "Training Days per Week: {$request->training_days_per_week}\n" .
                        "Preferred Session Length: {$request->preferred_session_length}\n" .
                        "Lifestyle Activity Level: {$request->lifestyle_activity_level}\n" .
                        "Stress Level: {$request->stress_level}\n" .
                        "Sleep Quality: {$request->sleep_quality}\n" .
                        "Injuries/Health Conditions: " . implode(', ', $request->injuries_health_conditions ?? []) . "\n" .
                        "Available Equipment: " . implode(', ', $request->available_equipments ?? []) . "\n\n" .
                        "⚠️ IMPORTANT:\n" .
                        "- Ensure the plan includes different exercises each day for {$request->plan_duration} days.\n" .
                        "- Suggest warm-up and cool-down for each session.\n" .
                        "- Tailor exercises based on training level and injuries.\n" .
                        "- ONLY use the listed available equipment.\n" .
                        "- Do NOT repeat workouts. Each day must be unique.\n" .
                        "- Include brief rest guidance on non-training days if applicable."
                ]
            ],
            'temperature' => 0.7
        ]);
        Log::info('OpenAI API response:', $openaiResponse->json());

        if ($openaiResponse->successful()) {
            $plan = $openaiResponse->json()['choices'][0]['message']['content'] ?? 'No plan available.';
        } else {
            $plan = 'Error: ' . $openaiResponse->status() . ' - ' . $openaiResponse->body();
        }
        // dd($plan);
        // dd($openaiResponse);
        Workout::create([
            'name' => $request->name,
            'age' => $request->age,
            'height' => $request->height,
            'weight' => $request->weight,
            'fitness_goals' => $request->fitness_goals,
            'gender' => $request->gender,
            'training_level' => $request->training_level,
            'preferred_training_style' => $request->preferred_training_style,
            'training_days_per_week' => $request->training_days_per_week,
            'preferred_session_length' => $request->preferred_session_length,
            'lifestyle_activity_level' => $request->lifestyle_activity_level,
            'stress_level' => $request->stress_level,
            'sleep_quality' => $request->sleep_quality,
            'injuries_health_conditions' => $request->injuries_health_conditions,
            'available_equipments' => $request->available_equipments,
            'plan_duration' => $request->plan_duration,
            'workout_plan' => $plan,
        ]);

        return redirect()->route('workout.index')->with('success', 'Workout plan created successfully!');
    }
}
