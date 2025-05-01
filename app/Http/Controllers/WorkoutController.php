<?php

namespace App\Http\Controllers;

use App\Models\Workout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

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

    public function show(Workout $workout)
    {
        $parsed = $this->parseWorkoutPlan($workout->workout_plan);
        // @dd($parsed['plan']);
        return view('workout.show', [
            'workout' => $workout,
            'workoutPlan' => $parsed['plan'],
            'healthTips' => $parsed['tips']
        ]);
    }
    private function parseWorkoutPlan($rawText)
    {
        $parsedPlan = [];
        $tips = '';
        $inTips = false;

        // Clean and normalize the text
        $rawText = preg_replace('/\*\*(.*?)\*\*/', '**$1**', $rawText); // Normalize bold
        $rawText = preg_replace('/\r\n|\r/', "\n", $rawText);           // Normalize line endings

        // Match each day's block (including Active Recovery, etc.)
        preg_match_all('/\*\*Day\s*(\d+):\s*(.*?)\*\*\n([\s\S]*?)(?=(\*\*Day\s*\d+:|\*\*|$))/i', $rawText, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $dayNumber = (int)trim($match[1]);
            $dayTitle = 'Day ' . $dayNumber . ': ' . trim($match[2]);
            $content = trim($match[3]);

            $sections = [];
            $currentSection = null;

            $lines = preg_split("/\n/", $content);

            foreach ($lines as $line) {
                $line = trim($line);
                if (empty($line)) continue;

                if (preg_match('/^(Warm-up|Cool[- ]?down|Workout|Dynamic Warm-up|Stretching|Recovery):?/i', $line, $sectionMatch)) {
                    $currentSection = ucfirst(strtolower($sectionMatch[1]));
                    $sections[$currentSection] = [];
                } elseif (preg_match('/^[\d\-\•]+[\.\)]?\s*/', $line) || $currentSection) {
                    // If it's a bullet/numbered item or already inside a section
                    if (!$currentSection) {
                        $currentSection = 'Workout'; // Default section if none is found
                        $sections[$currentSection] = [];
                    }
                    $sections[$currentSection][] = $line;
                } else {
                    // If it’s standalone info (like on Day 7)
                    $sections['Info'][] = $line;
                }
            }

            $parsedPlan["Day $dayNumber"] = $sections;
        }

        // Handle health tips from end of raw text
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

    // private function parseWorkoutPlan($rawText)
    // {
    //     $parsedPlan = [];
    //     $tips = '';
    //     $inTips = false;

    //     // Clean the text
    //     $rawText = preg_replace('/(\*\*+)(\s*\n)?/', '', $rawText); // Remove bold markers
    //     $rawText = preg_replace('/---+/', '', $rawText);           // Remove horizontal rules
    //     $rawText = preg_replace('/\r\n|\r/', "\n", $rawText);      // Normalize newlines

    //     // Match day blocks
    //     preg_match_all('/Day\s*(\d+):([\s\S]*?)(?=(Day\s*\d+:|Rest Day:|\z))/i', $rawText, $dayMatches, PREG_SET_ORDER);

    //     foreach ($dayMatches as $match) {
    //         $dayNumber = (int)$match[1];
    //         $content = trim($match[2]);

    //         $sections = [];
    //         $currentSection = null;

    //         // Split content into lines
    //         $lines = preg_split("/\n/", $content);

    //         foreach ($lines as $line) {
    //             $line = trim($line);

    //             if (empty($line)) continue;

    //             // Detect sections
    //             if (preg_match('/^(WarmUp|Workout|Cool[- ]?Down|Dynamic Warm[- ]?Up):\s*$/i', $line, $sectionMatch)) {
    //                 $currentSection = ucfirst(strtolower(trim($sectionMatch[1])));
    //                 $sections[$currentSection] = [];
    //             } elseif ($currentSection) {
    //                 // Add item under current section
    //                 $sections[$currentSection][] = $line;
    //             }
    //         }

    //         $parsedPlan["Day $dayNumber"] = $sections;
    //     }

    //     // Handle special days (like Rest Day or Active Recovery)
    //     if (preg_match_all('/\*\*(Rest Day|Day \d+: [^\*]+)\*\*\n\n([\s\S]*?)(?=(\*\*|$))/', $rawText, $restMatches, PREG_SET_ORDER)) {
    //         foreach ($restMatches as $match) {
    //             $dayTitle = trim($match[1]);
    //             $details = array_filter(array_map('trim', explode("\n", trim($match[2]))));
    //             $parsedPlan[$dayTitle] = ['Info' => $details];
    //         }
    //     }

    //     // Extract Tips
    //     $lines = preg_split("/\n/", $rawText);
    //     foreach ($lines as $line) {
    //         $line = trim($line);
    //         if (preg_match('/^(This|Adjust|Stay|Listen|Focus|Remember|Tips:|Ensure|Consult)/i', $line)) {
    //             $inTips = true;
    //         }

    //         if ($inTips) {
    //             $tips .= $line . ' ';
    //         }
    //     }

    //     return [
    //         'plan' => $parsedPlan,
    //         'tips' => trim($tips),
    //     ];
    // }

    //edit
    public function edit(Workout $workout)
    {
        return view('edit', compact('workout'));
    }
    public function update(Request $request, Workout $workout)
    {
        $request->validate([
            'age' => 'required|integer',
            'height' => 'required|numeric',
            'weight' => 'required|numeric',
            'gender' => 'required|string',
            'name' => 'required|string',
            'goal' => 'required|string',
            'diet_type' => 'required|string',
            'meals_per_day' => 'required|string',
            'plan_duration' => 'required|string',
        ]);


        // Call OpenAI API for AI-Powered Nutrition Plan
        $openaiResponse = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
            'Content-Type' => 'application/json',
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are a certified nutritionist and expert diet planner. Always prioritize user safety and strictly follow dietary restrictions. Never suggest any ingredient the user is allergic to. Even indirect suggestions, alternatives, or substitutions that include allergens must be avoided.'
                ],
                [
                    'role' => 'user',
                    'content' =>
                    "Please create a personalized diet plan based on the following information. ⚠️ I have a serious allergy to the following foods: " . implode(', ', $request->allergies) . ".\n\n" .
                        "🚫 DO NOT include these allergens in any meal. Not as main ingredients, not as condiments, and not as alternatives. That includes any type of fish, seafood, sauces, stocks, oils, or supplements made from these ingredients.\n" .
                        "If a recipe usually contains these allergens, exclude or replace them with safe alternatives.\n\n" .
                        "My details:\n" .
                        "Age: {$request->age} years\n" .
                        "Gender: {$request->gender}\n" .
                        "Height: {$request->height} cm\n" .
                        "Weight: {$request->weight} kg\n" .
                        "Goal: {$request->goal}\n" .
                        "Health Conditions: " . implode(', ', $request->health_conditions) . "\n" .
                        "Diet Type: {$request->diet_type}\n" .
                        "Meals per day: {$request->meals_per_day}\n" .
                        "Plan duration: {$request->plan_duration} days\n\n" .
                        "⚠️ IMPORTANT: You must generate a complete, unique meal plan for exactly {$request->plan_duration} days. Do not repeat or reuse any days or meals. Do not say 'repeat the above for the remaining days'. The plan must contain individual meals for each day from Day 1 to Day {$request->plan_duration}. This is a strict requirement. If the user requests 15 days, return exactly 15 full days. If 30 days, return 30 days. No summarizing or skipping.\n\n" .
                        "⚠️ Again, double-check every meal to ensure it is 100% free from all allergens. If any meal is unsafe, it must be reworked. This is a health-critical instruction."

                    // "Plan duration: {$request->plan_duration} days\n\n" .
                    // "⚠️ Again, double-check every meal to ensure it is 100% free from all allergens. If any meal is unsafe, it must be reworked. This is a health-critical instruction. Ensure plan duration is for {$request->plan_duration} days"
                ]
            ],
            'temperature' => 0.7
        ]);


        // $plan = $openaiResponse->json()['choices'][0]['message']['content'] ?? 'No plan available.';
        Log::info('OpenAI API response:', $openaiResponse->json());

        if ($openaiResponse->successful()) {
            $plan = $openaiResponse->json()['choices'][0]['message']['content'] ?? 'No plan available.';
        } else {
            $plan = 'Error: ' . $openaiResponse->status() . ' - ' . $openaiResponse->body();
        }
        //save to db
        $workout->update([
            'age' => $request->age,
            'height' => $request->height,
            'weight' => $request->weight,
            'gender' => $request->gender,
            'goal' => $request->goal,
            'meals_per_day' => $request->meals_per_day,
            'diet_type' => $request->diet_type,
            'health_conditions' => $request->health_conditions,
            'allergies' => $request->allergies,
            'nutrition_plan' => $plan
        ]);
        // return view('nutrition', compact('plan'));
        return redirect()->route('workout.index')->with('success', 'Workout Plan updated successfully!');
    }
    //remove
    public function destroy(Workout $workout)
    {
        $workout->delete();
        return redirect()->route('workout.index')->with('success', 'Post deleted successfully');
    }
    public function exportPdf($id)
    {
        $workout = Workout::findOrFail($id);
        $parsed = $this->parseWorkoutPlan($workout->workout_plan);
        $workoutPlan = $parsed['plan']; // Adjust according to your data
        $healthTips =  $parsed['tips']; // Adjust if needed

        $pdf = Pdf::loadView('workout.export', compact('workout', 'workoutPlan', 'healthTips'));
        return $pdf->download('workout_plan.pdf');
    }
    public function exportDoc($id)
    {
        $workout = Workout::findOrFail($id);
        $parsed = $this->parseWorkoutPlan($workout->workout_plan);
        $workoutPlan = $parsed['plan']; // Adjust according to your data
        $healthTips =  $parsed['tips']; // Adjust if needed

        $html = view('workout.export', compact('workout', 'workoutPlan', 'healthTips'))->render();

        return response($html)
            ->header('Content-Type', 'application/msword')
            ->header('Content-Disposition', 'attachment; filename="workout_plan.doc"');
    }
    //for editing plain data
    public function editDay($id, $day)
    {
        $workout = Workout::findOrFail($id);
        $parsed = $this->parseWorkoutPlan($workout->workout_plan);
        $dayKey = "Day $day";
        // dd($meals);
        return view('workout.edit-day', [
            'day' => $day,
            'meals' => $parsed['plan'][$dayKey] ?? [],
            'workoutId' => $id
        ]);
    }

    private function rebuildRawText(string $intro, array $plan, string $tips): string
    {
        $text = trim($intro) . "\n\n";

        foreach ($plan as $day => $meals) {
            $text .= "$day:\n";
            foreach ($meals as $mealType => $items) {
                foreach ($items as $item) {
                    $text .= "- $mealType: $item\n";
                }
            }
            $text .= "\n";
        }

        if (!empty($tips)) {
            $text .= trim($tips);
        }

        return trim($text);
    }
    public function updateDay(Request $request, $id, $day)
    {
        $nutrition = Workout::findOrFail($id);
        $rawText = $nutrition->nutrition_plan;

        $parsed = $this->parseWorkoutPlan($rawText);
        $dayKey = "Day $day";

        // Update only the selected day
        $updatedMeals = [];
        foreach ($request->input('meals') as $mealType => $items) {
            $items = array_filter($items);
            $updatedMeals[$mealType] = $items;
        }

        // Replace Day X
        $parsed['plan'][$dayKey] = $updatedMeals;

        // Extract dynamic intro from raw text
        $intro = '';
        if (preg_match('/^(.*?)(?=\nDay\s*1:)/is', $rawText, $introMatch)) {
            $intro = trim($introMatch[1]);
        }

        // Rebuild full raw text
        $newRawText = $this->rebuildRawText($intro, $parsed['plan'], $parsed['tips']);

        $nutrition->nutrition_plan = $newRawText;
        // dd($newRawText);
        $nutrition->save();

        return redirect()->route('workout.show', $nutrition->id)->with('success', "Day $day updated successfully.");
    }
}
