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

    // private function parseWorkoutPlan($rawText)
    // {
    //     $parsedPlan = [];
    //     $tips = '';
    //     $inTips = false;

    //     // Clean and normalize the text
    //     $rawText = preg_replace('/\*\*(.*?)\*\*/', '**$1**', $rawText); // Normalize bold
    //     $rawText = preg_replace('/\r\n|\r/', "\n", $rawText);           // Normalize line endings

    //     // Match each day's block (including Active Recovery, etc.)
    //     preg_match_all('/\*\*Day\s*(\d+):\s*(.*?)\*\*\n([\s\S]*?)(?=(\*\*Day\s*\d+:|\*\*|$))/i', $rawText, $matches, PREG_SET_ORDER);

    //     foreach ($matches as $match) {
    //         $dayNumber = (int)trim($match[1]);
    //         $dayTitle = 'Day ' . $dayNumber . ': ' . trim($match[2]);
    //         $content = trim($match[3]);

    //         $sections = [];
    //         $currentSection = null;

    //         $lines = preg_split("/\n/", $content);

    //         foreach ($lines as $line) {
    //             $line = trim($line);
    //             if (empty($line)) continue;

    //             if (preg_match('/^(Warm-up|Cool[- ]?down|Workout|Dynamic Warm-up|Stretching|Recovery):?/i', $line, $sectionMatch)) {
    //                 $currentSection = ucfirst(strtolower($sectionMatch[1]));
    //                 $sections[$currentSection] = [];
    //             } elseif (preg_match('/^[\d\-\•]+[\.\)]?\s*/', $line) || $currentSection) {
    //                 // If it's a bullet/numbered item or already inside a section
    //                 if (!$currentSection) {
    //                     $currentSection = 'Workout'; // Default section if none is found
    //                     $sections[$currentSection] = [];
    //                 }
    //                 $sections[$currentSection][] = $line;
    //             } else {
    //                 // If it’s standalone info (like on Day 7)
    //                 $sections['Info'][] = $line;
    //             }
    //         }

    //         $parsedPlan["Day $dayNumber"] = $sections;
    //     }

    //     // Handle health tips from end of raw text
    //     $lines = preg_split("/\n/", $rawText);
    //     foreach ($lines as $line) {
    //         $line = trim($line);
    //         if (preg_match('/^(Tips|Remember|Stay|Adjust|Focus|Ensure|Listen|Consult|Engage|Hydrate|Rest|Aim)\b/i', $line)) {
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
            'workout_plan' => $plan,
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
        // dd($parsed['plan'][$dayKey] ?? []);
        return view('workout.edit-day', [
            'day' => $day,
            'meals' => $parsed['plan'][$dayKey] ?? [],
            'workoutId' => $id
        ]);
    }
    // function rebuildRawText($originalText, $dayTitle, $updatedWorkoutArray)
    // {
    //     // Split using regex to capture all day blocks
    //     $pattern = '/\*\*(Day\s\d+:.*)\*\*/';
    //     preg_match_all($pattern, $originalText, $matches, PREG_OFFSET_CAPTURE);

    //     $sections = [];
    //     $total = count($matches[0]);

    //     for ($i = 0; $i < $total; $i++) {
    //         $titleText = $matches[1][$i][0]; // example: Day 1: Full Body Dumbbell Workout
    //         $startPos = $matches[0][$i][1];
    //         $endPos = ($i + 1 < $total) ? $matches[0][$i + 1][1] : strlen($originalText);
    //         $content = substr($originalText, $startPos, $endPos - $startPos);

    //         $sections[] = [
    //             'title' => $titleText,
    //             'content' => $content,
    //         ];
    //     }

    //     // Rebuild content
    //     $rebuilt = '';
    //     foreach ($sections as $section) {
    //         if (trim($section['title']) === trim($dayTitle)) {
    //             $newContent = "**{$dayTitle}**\n";

    //             $counter = 1;
    //             $currentSection = '';

    //             foreach ($updatedWorkoutArray as $line) {
    //                 $trimmed = trim($line);

    //                 if (str_ends_with($trimmed, ':')) {
    //                     $currentSection = strtolower(rtrim($trimmed, ':'));
    //                     $newContent .= "- {$trimmed}\n";
    //                     $counter = 1;
    //                 } elseif ($currentSection === 'workout') {
    //                     $newContent .= "{$counter}. {$trimmed}\n";
    //                     $counter++;
    //                 } else {
    //                     $newContent .= "{$trimmed}\n";
    //                 }
    //             }

    //             $rebuilt .= $newContent . "\n\n";
    //         } else {
    //             $rebuilt .= $section['content'] . "\n\n";
    //         }
    //     }

    //     return trim($rebuilt);
    // }



    private function rebuildRawText(string $intro, array $plan, string $tips): string
    {
        $text = trim($intro) . "\n\n";

        foreach ($plan as $day => $sections) {
            // Day line: **Day 1: Title**
            $text .= "**{$day}:**\n\n";

            // If this day has only one string (like Day 4 with a single line of rest text)
            if (isset($sections['Info']) && is_array($sections['Info'])) {
                foreach ($sections['Info'] as $line) {
                    $text .= "{$line}\n";
                }
                $text .= "\n";
                continue;
            }

            foreach ($sections as $sectionTitle => $items) {
                // Example: 1. Warm-up:
                $text .= "{$sectionTitle}:\n";
                foreach ($items as $item) {
                    $text .= "{$item}\n";
                }
                $text .= "\n";
            }
        }

        // Append final reminder tips (if exists)
        if (!empty(trim($tips))) {
            $text .= trim($tips);
        }

        return trim($text);
    }

    public function updateDay(Request $request, $id, $day)
    {
        $workout = Workout::findOrFail($id);
        $rawText = $workout->workout_plan;

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

        $workout->workout_plan = $newRawText;
        // dd($newRawText);
        $workout->save();

        return redirect()->route('workout.show', $workout->id)->with('success', "Day $day updated successfully.");
    }
}
