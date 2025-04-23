<?php

namespace App\Http\Controllers;

use App\Models\NutritionInput;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class NutritionController extends Controller
{
    public function index()
    {
        $posts = NutritionInput::latest()->get();
        return view('index', compact('posts'));
        // return view('nutrition');
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('create');
    }
    public function store(Request $request)
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
                        "⚠️ Again, double-check every meal to ensure it is 100% free from all allergens. If any meal is unsafe, it must be reworked. This is a health-critical instruction. Ensure plan duration is for {$request->plan_duration} days"
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
        $nutrition = NutritionInput::create([
            'name' => $request->name,
            'age' => $request->age,
            'height' => $request->height,
            'weight' => $request->weight,
            'gender' => $request->gender,
            'goal' => $request->goal,
            'plan_duration' => $request->plan_duration,
            'meals_per_day' => $request->meals_per_day,
            'diet_type' => $request->diet_type,
            'health_conditions' => $request->health_conditions,
            'allergies' => $request->allergies,
            'nutrition_plan' => $plan
        ]);
        // return view('nutrition', compact('plan'));
        return redirect()->route('nutrition.index')->with('success', 'Plan created!');
    }
    /**
     * Display the specified resource.
     */


    public function show(NutritionInput $nutrition)
    {
        $parsed = $this->parseNutritionPlan($nutrition->nutrition_plan);
        // @dd($parsed['plan']);
        return view('show', [
            'nutrition' => $nutrition,
            'nutritionPlan' => $parsed['plan'],
            'healthTips' => $parsed['tips']
        ]);
    }
    private function parseNutritionPlan($rawText)
    {
        $parsedPlan = [];
        $tips = '';
        $inTips = false;

        // Match individual day sections like "Day 1:", "Day 2:", etc.
        preg_match_all('/(?:\*\*)?Day\s*(\d+)(?:\*\*)?:([\s\S]*?)(?=\n(?:\*\*)?Day\s*\d+(?:\*\*)?:|\z)/i', $rawText, $dayMatches, PREG_SET_ORDER);

        foreach ($dayMatches as $match) {
            $dayNumber = (int)$match[1];
            $content = trim($match[2]);

            $meals = [];

            // Match lines like "Breakfast: ..." OR "- Breakfast: ..."
            preg_match_all('/(?:-)?\s*(Breakfast|Snack|Lunch|Dinner):\s*(.*)/i', $content, $mealMatches, PREG_SET_ORDER);

            foreach ($mealMatches as $mealMatch) {
                $mealType = ucfirst(strtolower(trim($mealMatch[1])));
                $mealDetails = trim($mealMatch[2]);

                $meals[$mealType][] = $mealDetails;
            }

            $parsedPlan["Day $dayNumber"] = $meals;
        }

        // Extract tips section
        $lines = preg_split("/\r\n|\n|\r/", $rawText);
        foreach ($lines as $line) {
            $line = trim($line);

            // Identify the start of the tips section by common phrases
            if (preg_match('/^(Remember to|Make sure|It\'s important|Repeat|Monitor|Consult|Tips:)/i', $line)) {
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

    // private function parseNutritionPlan($rawText)
    // {
    //     $parsedPlan = [];
    //     $tips = '';
    //     $inTips = false;

    //     // Match both "Day X:", "Day X-Y:" or "**Day X:**"
    //     preg_match_all('/(?:\*\*)?Day\s*(\d+)(?:-(\d+))?(?:\*\*)?:([\s\S]*?)(?=\n(?:\*\*)?Day\s*\d+(?:-\d+)?(?:\*\*)?:|\z)/i', $rawText, $dayMatches, PREG_SET_ORDER);

    //     foreach ($dayMatches as $match) {
    //         $startDay = (int)$match[1];
    //         $endDay = isset($match[2]) && $match[2] !== '' ? (int)$match[2] : $startDay;
    //         $content = trim($match[3]);

    //         // Extract meals from the day's content
    //         preg_match_all('/-\s*(\w+):\s*(.*)/i', $content, $mealsMatches, PREG_SET_ORDER);
    //         $meals = [];

    //         foreach ($mealsMatches as $mealMatch) {
    //             $mealType = ucfirst(strtolower(trim($mealMatch[1])));
    //             $mealDetails = trim($mealMatch[2]);
    //             $meals[$mealType][] = $mealDetails;
    //         }

    //         for ($d = $startDay; $d <= $endDay; $d++) {
    //             $parsedPlan["Day $d"] = $meals;
    //         }
    //     }

    //     // Extract tips — search for lines starting with "Remember to", "Make sure", "It's important", etc.
    //     $lines = preg_split("/\r\n|\n|\r/", $rawText);
    //     foreach ($lines as $line) {
    //         $line = trim($line);

    //         if (preg_match('/^(Remember to|Make sure|It\'s important|Consult|Repeat|Monitor)/i', $line)) {
    //             $inTips = true;
    //         }

    //         if ($inTips) {
    //             $tips .= $line . ' ';
    //         }
    //     }
    //     // dd($parsedPlan);
    //     return [
    //         'plan' => $parsedPlan,
    //         'tips' => trim($tips)
    //     ];
    // }

    // private function parseNutritionPlan($rawText)
    // {
    //     // $sections = preg_split('/\*\*Day (\d+)-(\d+):\*\*/', $rawText, -1, PREG_SPLIT_DELIM_CAPTURE);
    //     $sections = preg_split('/Day (\d+)-(\d+):/', $rawText, -1, PREG_SPLIT_DELIM_CAPTURE);

    //     $parsedPlan = [];
    //     $tips = '';
    //     $inTips = false;
    //     for ($i = 1; $i < count($sections); $i += 3) {
    //         $startDay = $sections[$i];
    //         $endDay = $sections[$i + 1];
    //         $content = $sections[$i + 2];

    //         // preg_match_all('/\*\*(.*?)\*\*\s*- ([^-]*)/', $content, $matches, PREG_SET_ORDER);
    //         preg_match_all('/-\s*(\w+):\s*(.*)/', $content, $matches, PREG_SET_ORDER);

    //         $meals = [];
    //         foreach ($matches as $match) {
    //             $mealType = trim($match[1]);
    //             $mealDetails = trim($match[2]);
    //             $meals[$mealType][] = $mealDetails;
    //         }

    //         for ($d = $startDay; $d <= $endDay; $d++) {
    //             $parsedPlan["Day $d"] = $meals;
    //         }
    //     }

    //     // Extract the tip text after the last meal block
    //     // $lastDayPattern = '/\*\*Day \d+-\d+:\*\*.*?\*\*Dinner:\*\*.*?(?:\n|$)/s';
    //     // if (preg_match($lastDayPattern, $rawText, $match)) {
    //     //     $tips = trim(str_replace($match[0], '', $rawText));
    //     // }
    //     $lines = preg_split("/\r\n|\n|\r/", $rawText);
    //     foreach ($lines as $line) {
    //         $line = trim($line);

    //         // If we've reached the tips section
    //         if (stripos($line, 'remember to') !== false && !$inTips) {
    //             $inTips = true;
    //             $tips .= $line . ' ';
    //             continue;
    //         }

    //         if ($inTips) {
    //             $tips .= $line . ' ';
    //             continue;
    //         }
    //     }
    //     dd($parsedPlan);
    //     return [
    //         'plan' => $parsedPlan,
    //         'tips' => $tips
    //     ];
    // }



    public function edit(NutritionInput $nutrition)
    {
        return view('edit', compact('nutrition'));
    }
    public function update(Request $request, NutritionInput $nutrition)
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
                        "⚠️ Again, double-check every meal to ensure it is 100% free from all allergens. If any meal is unsafe, it must be reworked. This is a health-critical instruction."
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
        $nutrition->update([
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
        return redirect()->route('nutrition.index')->with('success', 'Plan updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(NutritionInput $nutrition)
    {
        $nutrition->delete();
        return redirect()->route('nutrition.index')->with('success', 'Post deleted successfully');
    }

    public function exportPdf($id)
    {
        $nutrition = NutritionInput::findOrFail($id);
        $parsed = $this->parseNutritionPlan($nutrition->nutrition_plan);
        $nutritionPlan = $parsed['plan']; // Adjust according to your data
        $healthTips =  $parsed['tips']; // Adjust if needed

        $pdf = Pdf::loadView('nutrition.export', compact('nutrition', 'nutritionPlan', 'healthTips'));
        return $pdf->download('nutrition_plan.pdf');
    }
    public function exportDoc($id)
    {
        $nutrition = NutritionInput::findOrFail($id);
        $parsed = $this->parseNutritionPlan($nutrition->nutrition_plan);
        $nutritionPlan = $parsed['plan']; // Adjust according to your data
        $healthTips =  $parsed['tips']; // Adjust if needed

        $html = view('nutrition.export', compact('nutrition', 'nutritionPlan', 'healthTips'))->render();

        return response($html)
            ->header('Content-Type', 'application/msword')
            ->header('Content-Disposition', 'attachment; filename="nutrition_plan.doc"');
    }
    public function updateDayText(Request $request, $id)
    {
        $request->validate([
            'day' => 'required|string',
            'newText' => 'required|string',
        ]);

        $nutrition = NutritionInput::findOrFail($id);

        $pattern = '/(Day\s*' . preg_quote($request->day, '/') . '\:)([\s\S]*?)(?=\nDay\s*\d+\:|\z)/i';
        $replacement = "$1\n" . trim($request->newText) . "\n";
        $updatedText = preg_replace($pattern, $replacement, $nutrition->plain_text);

        $nutrition->nutrition_plan = $updatedText;
        $nutrition->save();

        return redirect()->back()->with('success', 'Day updated successfully!');
    }
}
