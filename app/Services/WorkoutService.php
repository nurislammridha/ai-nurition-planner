<?php

namespace App\Services;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WorkoutService
{
    public function __construct(private readonly OpenAiService $openAi) {}

    public function generatePlan(Request $request)
    {
        // Calculate session segments based on preferred_session_length
        $totalMinutes = (int) $request->preferred_session_length;
        $warmupMinutes = (int) round($totalMinutes * 0.15);       // 15% for warm-up
        $cooldownMinutes = (int) round($totalMinutes * 0.10);      // 10% for cool-down
        $mainWorkoutMinutes = $totalMinutes - $warmupMinutes - $cooldownMinutes;

        // Safety check (in case rounding causes issues)
        if ($mainWorkoutMinutes <= 0) {
            $mainWorkoutMinutes = max(5, $totalMinutes - 10);
            $warmupMinutes = 5;
            $cooldownMinutes = 5;
        }

        $openAiResponse = $this->openAi->sendRequest(
            [
                [
                    'role' => 'system',
                    'content' => 'You are a certified personal trainer and expert workout planner. Your plans must consider safety, training level, injuries, and available equipment. Only suggest exercises the user can safely perform with the equipment they have.'
                ],
                [
                    'role' => 'user',
                    'content' =>
                    "Please create a personalized workout plan that strictly lasts for {$request->plan_duration} days based on the following user profile:\n\n" .
                        "Name: {$request->name}\n" .
                        "Age: {$request->age}\n" .
                        "Gender: {$request->gender}\n" .
                        "Height: {$request->height} cm\n" .
                        "Weight: {$request->weight} kg\n" .
                        "Fitness Goal: {$request->fitness_goals}\n" .
                        "Training Level: {$request->training_level}\n" .
                        "Preferred Training Style: {$request->preferred_training_style}\n" .
                        "Training Days per Week: {$request->training_days_per_week}\n" .
                        "Preferred Session Length: {$totalMinutes} minutes\n" .
                        "Lifestyle Activity Level: {$request->lifestyle_activity_level}\n" .
                        "Stress Level: {$request->stress_level}\n" .
                        "Sleep Quality: {$request->sleep_quality}\n" .
                        "Injuries/Health Conditions: " . implode(', ', $request->injuries_health_conditions ?? []) . "\n" .
                        "Available Equipment: " . implode(', ', $request->available_equipments ?? []) . "\n\n" .

                        "⚠️ VERY IMPORTANT:\n" .
                        "- Generate exactly {$request->plan_duration} daily workout entries in JSON.\n" .
                        "- The total time per day MUST add up to exactly {$totalMinutes} minutes. Divide as:\n" .
                        "  - Warm-Up: {$warmupMinutes} minutes\n" .
                        "  - Main Workout: {$mainWorkoutMinutes} minutes\n" .
                        "  - Cool-Down: {$cooldownMinutes} minutes\n" .
                        "- If needed, repeat similar days but avoid exact repetition.\n" .
                        "- Respond ONLY in valid JSON format wrapped with triple backticks (```).\n" .
                        "- No explanation or extra text outside the JSON.\n\n" .

                        "The JSON structure must match this format:\n\n" .
                        "```json\n" .
                        "{\n" .
                        "  \"training_days_per_week\": {$request->training_days_per_week},\n" .
                        "  \"plan_duration\": {$request->plan_duration},\n" .
                        "  \"plan\": [\n" .
                        "    {\n" .
                        "      \"day\": \"Day 1\",\n" .
                        "      \"workout\": [\n" .
                        "        \"**Warm-Up ({$warmupMinutes} minutes):**\",\n" .
                        "        \"Exercise A\",\n" .
                        "        \"...\",\n" .
                        "        \"**Workout ({$mainWorkoutMinutes} minutes):**\",\n" .
                        "        \"Exercise X\",\n" .
                        "        \"...\",\n" .
                        "        \"**Cool-Down ({$cooldownMinutes} minutes):**\",\n" .
                        "        \"Stretch A\",\n" .
                        "        \"...\"\n" .
                        "      ]\n" .
                        "    },\n" .
                        "    ... (up to Day {$request->plan_duration})\n" .
                        "  ],\n" .
                        "  \"tips\": \"Final safety and motivation tips.\"\n" .
                        "}\n" .
                        "```"
                ]
            ],
            env('OPENAI_API_KEY_WORKOUT')
        );

        // Log::info('OpenAI API response:', $openAiResponse->json());

        if ($openAiResponse->successful()) {
            $plan = $openAiResponse->json()['choices'][0]['message']['content'] ?? 'No plan available.';
        } else {
            throw new Exception("Workout plan creation failed. Please try again.", 400);
            Log::debug('Error: ' . $openAiResponse->status() . ' - ' . $openAiResponse->body());
        }

        return $plan;
    }
}
