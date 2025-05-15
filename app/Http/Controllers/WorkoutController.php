<?php

namespace App\Http\Controllers;

use App\Models\Workout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

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
            'Authorization' => 'Bearer ' . env('OPENAI_API_KEY_WORKOUT'),
            'Content-Type' => 'application/json',
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-4o-mini',
            // 'model' => 'gpt-3.5-turbo',
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
                        "⚠️ VERY IMPORTANT:\n" .
                        "- Respond ONLY with valid pure JSON format wrapped inside triple backticks (```).\n" .
                        "- Do NOT include any explanation or text before or after the JSON.\n" .
                        "- The JSON structure must match this format:\n\n" .

                        "```json\n" .
                        "{\n" .
                        "  \"training_days_per_week\": {$request->training_days_per_week},\n" .
                        "  \"plan_duration\": {$request->plan_duration},\n" .
                        "  \"plan\": [\n" .
                        "    {\n" .
                        "      \"day\": \"Day 1\",\n" .
                        "      \"workout\": [\n" .
                        "        \"**Warm-Up (5 minutes):**\",\n" .
                        "        \"Exercise A\",\n" .
                        "        \"Exercise B\",\n" .
                        "        \"...\",\n" .
                        "        \"**Workout (40 minutes):**\",\n" .
                        "        \"Exercise X\",\n" .
                        "        \"...\",\n" .
                        "        \"**Cool-Down (5 minutes):**\",\n" .
                        "        \"Stretch A\",\n" .
                        "        \"...\"\n" .
                        "      ]\n" .
                        "    },\n" .
                        "    ... (Repeat for {$request->plan_duration} days)\n" .
                        "  ],\n" .
                        "  \"tips\": \"Final safety and motivation tips.\"\n" .
                        "}\n" .
                        "```"
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
        $planJson = parseChatGptNutritionPlan($plan);

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
            'workout_plan' => $planJson,
        ]);

        return redirect()->route('workout.index')->with('success', 'Workout plan created successfully!');
    }

    public function show(Workout $workout)
    {
        $workoutPlan = $workout->workout_plan['plan'] ?? [];
        $healthTips = $workout->workout_plan['tips'] ?? null;
        return view('workout.show', compact('workout', 'workoutPlan', 'healthTips'));
    }
    private function parseWorkoutPlan($rawText)
    {
        $parsedPlan = [];
        $tips = '';
        $inTips = false;

        // Normalize text
        $rawText = preg_replace('/\*\*(.*?)\*\*/', '**$1**', $rawText);
        $rawText = preg_replace('/\r\n|\r/', "\n", $rawText);

        // Match all days
        preg_match_all('/\*\*Day\s*(\d+):\s*(.*?)\*\*\n([\s\S]*?)(?=\*\*Day\s*\d+:|\Z)/i', $rawText, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $dayNumber = (int)trim($match[1]);
            $dayTitle = 'Day ' . $dayNumber . ': ' . trim($match[2]);
            $content = trim($match[3]);

            $sections = [];
            $lines = preg_split("/\n/", $content);
            $currentSection = null;

            foreach ($lines as $line) {
                $line = trim($line);
                if (empty($line)) continue;

                // Detect section headers like Warm-up, Cool-down, etc.
                if (preg_match('/^(Warm-up|Cool[- ]?down|Workout|Stretching|Recovery):?\s*(.*)$/i', $line, $sectionMatch)) {
                    $currentSection = ucfirst(strtolower($sectionMatch[1]));
                    $sections[$currentSection] = [];

                    // Add inline content if any
                    if (!empty($sectionMatch[2])) {
                        $sections[$currentSection][] = $sectionMatch[2];
                    }
                }
                // Check for list items (numbered or bullets)
                elseif (preg_match('/^(\d+\.|\-|\•)\s*(.+)$/', $line, $itemMatch)) {
                    if (!$currentSection) {
                        $currentSection = 'Workout';
                        $sections[$currentSection] = [];
                    }
                    $sections[$currentSection][] = $itemMatch[2];
                } else {
                    // Non-sectioned info like rest days or free text
                    if (!$currentSection) {
                        $currentSection = 'Info';
                        $sections[$currentSection] = [];
                    }
                    $sections[$currentSection][] = $line;
                }
            }

            $parsedPlan["Day $dayNumber"] = $sections;
        }

        // Extract tips from final paragraph
        $lines = preg_split("/\n/", $rawText);
        foreach ($lines as $line) {
            $line = trim($line);
            if (preg_match('/^(Tips|Remember|Stay|Adjust|Focus|Ensure|Listen|Consult|Engage|Hydrate|Rest|Aim)\b/i', $line)) {
                $inTips = true;
            }
            if ($inTips) {
                $tips .= $line . ' ';
            }
        }

        return [
            'plan' => $parsedPlan,
            'tips' => trim($tips),
        ];
    }


    //edit
    public function edit(Workout $workout)
    {
        return view('workout.edit', compact('workout'));
    }
    public function update(Request $request, Workout $workout)
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
            'Authorization' => 'Bearer ' . env('OPENAI_API_KEY_WORKOUT'),
            'Content-Type' => 'application/json',
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-4o-mini',
            // 'model' => 'gpt-3.5-turbo',
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
                        "⚠️ VERY IMPORTANT:\n" .
                        "- Respond ONLY with valid pure JSON format wrapped inside triple backticks (```).\n" .
                        "- Do NOT include any explanation or text before or after the JSON.\n" .
                        "- The JSON structure must match this format:\n\n" .

                        "```json\n" .
                        "{\n" .
                        "  \"training_days_per_week\": {$request->training_days_per_week},\n" .
                        "  \"plan_duration\": {$request->plan_duration},\n" .
                        "  \"plan\": [\n" .
                        "    {\n" .
                        "      \"day\": \"Day 1\",\n" .
                        "      \"workout\": [\n" .
                        "        \"**Warm-Up (5 minutes):**\",\n" .
                        "        \"Exercise A\",\n" .
                        "        \"Exercise B\",\n" .
                        "        \"...\",\n" .
                        "        \"**Workout (40 minutes):**\",\n" .
                        "        \"Exercise X\",\n" .
                        "        \"...\",\n" .
                        "        \"**Cool-Down (5 minutes):**\",\n" .
                        "        \"Stretch A\",\n" .
                        "        \"...\"\n" .
                        "      ]\n" .
                        "    },\n" .
                        "    ... (Repeat for {$request->plan_duration} days)\n" .
                        "  ],\n" .
                        "  \"tips\": \"Final safety and motivation tips.\"\n" .
                        "}\n" .
                        "```"
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
        $planJson = parseChatGptNutritionPlan($plan);
        //save to db

        $workout->update([
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
            'workout_plan' => $planJson,
        ]);
        // return view('nutrition', compact('plan'));
        return redirect()->route('workout.index')->with('success', 'Workout Plan updated successfully!');
    }
    //remove
    public function destroy(Workout $workout)
    {
        $workout->delete();
        return redirect()->route('workout.index')->with('success', 'Workout plan deleted successfully');
    }

    public function exportPdf($id)
    {
        $workout = Workout::findOrFail($id);

        // workout_plan is already an array due to cast
        $workoutPlan = $workout->workout_plan;
        $healthTips = $workout->health_tips;

        $pdf = Pdf::loadView('workout.export', compact('workout', 'workoutPlan', 'healthTips'))
            ->setPaper('a4')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
            ]);

        return $pdf->download('workout_plan.pdf');
    }

    public function exportDoc($id)
    {
        $workout = Workout::findOrFail($id);

        // workout_plan is already an array due to cast
        $workoutPlan = $workout->workout_plan;
        $healthTips = $workout->health_tips;

        $html = view('workout.export', compact('workout', 'workoutPlan', 'healthTips'))->render();

        return response($html)
            ->header('Content-Type', 'application/vnd.ms-word')
            ->header('Content-Disposition', 'attachment; filename="workout_plan.doc"');
    }



    //for editing plain data
    public function editDay($id, $day)
    {
        $workout = Workout::findOrFail($id);
        $dayKey = "Day $day";

        $dayPlan = collect($workout->workout_plan['plan'])
            ->firstWhere('day', $dayKey);

        $meals = [];

        if ($dayPlan && isset($dayPlan['workout']) && count($dayPlan['workout']) === 1 && strtolower($dayPlan['workout'][0]) === 'rest day') {
            $meals = ['rest' => ['Rest Day']];
        } else {
            $currentSection = null;

            foreach ($dayPlan['workout'] as $item) {
                if (Str::startsWith($item, '**') && Str::endsWith($item, '**')) {
                    $currentSection = trim($item, '** ');
                    $meals[$currentSection] = [];
                } elseif ($currentSection) {
                    $meals[$currentSection][] = $item;
                }
            }
        }

        return view('workout.edit-day', compact('day', 'meals', 'workout'));
    }

    public function updateDay(Request $request, $id, $day)
    {
        $workout = Workout::findOrFail($id);
        $dayKey = "Day $day";

        $raw = $workout->workout_plan;

        // Parse the full JSON directly
        $fullPlan = is_array($raw) ? $raw : json_decode($raw, true);

        // Rebuild this day only
        $updatedDayWorkout = [];

        foreach ($request->input('meals') as $section => $items) {
            $sectionTitle = "**" . trim($section) . "**";
            $updatedDayWorkout[] = $sectionTitle;

            foreach ($items as $item) {
                if (trim($item) !== '') {
                    $updatedDayWorkout[] = trim($item);
                }
            }
        }

        // Find and update this day in the full plan
        foreach ($fullPlan['plan'] as &$dayPlan) {
            if ($dayPlan['day'] === $dayKey) {
                $dayPlan['workout'] = $updatedDayWorkout;
                break;
            }
        }

        // Save back
        $workout->workout_plan = $fullPlan;
        $workout->save();

        return redirect()->route('workout.show', $workout->id)->with('success', "Day $day updated successfully.");
    }
}
