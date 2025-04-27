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

        // Clean the text
        $rawText = preg_replace('/(\*\*+)(\s*\n)?/', '', $rawText); // Remove stray ** and bold markers
        $rawText = preg_replace('/---+/', '', $rawText);            // Remove ---
        $rawText = preg_replace('/\r\n|\r/', "\n", $rawText);        // Normalize newlines

        // Match day blocks
        preg_match_all('/Day\s*(\d+):([\s\S]*?)(?=Day\s*\d+:|\z)/i', $rawText, $dayMatches, PREG_SET_ORDER);

        foreach ($dayMatches as $match) {
            $dayNumber = (int)$match[1];
            $content = trim($match[2]);

            $meals = [];

            // Split content into lines
            $lines = preg_split("/\n/", $content);

            $currentMealType = null;
            $waitingForMealContent = false;

            foreach ($lines as $line) {
                $line = trim($line);

                if (empty($line)) {
                    continue;
                }

                // Check if this is a meal type header
                if (preg_match('/^(?:-)?\s*(Breakfast|Mid-Morning Snack|Afternoon Snack|Snack|Lunch|Dinner):\s*(.*)$/i', $line, $mealHeaderMatch)) {
                    $currentMealType = ucfirst(strtolower(trim($mealHeaderMatch[1])));
                    $mealContent = trim($mealHeaderMatch[2]);

                    if (!empty($mealContent)) {
                        // Meal type + content on same line
                        $meals[$currentMealType][] = $mealContent;
                        $currentMealType = null;
                        $waitingForMealContent = false;
                    } else {
                        // Meal type only, wait for next line
                        $waitingForMealContent = true;
                    }
                } elseif ($waitingForMealContent && $currentMealType) {
                    // This line is the food content for the previous meal type
                    $meals[$currentMealType][] = $line;
                    $currentMealType = null;
                    $waitingForMealContent = false;
                }
            }

            $parsedPlan["Day $dayNumber"] = $meals;
        }

        // Extract Tips
        $lines = preg_split("/\n/", $rawText);
        foreach ($lines as $line) {
            $line = trim($line);

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

    //     // Match individual day sections like "Day 1:", "Day 2:", etc.
    //     preg_match_all('/(?:\*\*)?Day\s*(\d+)(?:\*\*)?:([\s\S]*?)(?=\n(?:\*\*)?Day\s*\d+(?:\*\*)?:|\z)/i', $rawText, $dayMatches, PREG_SET_ORDER);

    //     foreach ($dayMatches as $match) {
    //         $dayNumber = (int)$match[1];
    //         $content = trim($match[2]);

    //         $meals = [];

    //         // Match lines like "Breakfast: ..." OR "- Breakfast: ..."
    //         preg_match_all('/(?:-)?\s*(Breakfast|Snack|Mid-Morning Snack|Afternoon Snack|Lunch|Dinner):\s*(.*)/i', $content, $mealMatches, PREG_SET_ORDER);

    //         foreach ($mealMatches as $mealMatch) {
    //             $mealType = ucfirst(strtolower(trim($mealMatch[1])));
    //             $mealDetails = trim($mealMatch[2]);

    //             $meals[$mealType][] = $mealDetails;
    //         }

    //         $parsedPlan["Day $dayNumber"] = $meals;
    //     }

    //     // Extract tips section
    //     $lines = preg_split("/\r\n|\n|\r/", $rawText);
    //     foreach ($lines as $line) {
    //         $line = trim($line);

    //         // Identify the start of the tips section by common phrases
    //         if (preg_match('/^(Remember to|Make sure|It\'s important|Repeat|Monitor|Consult|Tips:)/i', $line)) {
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
    //for editing plain data
    public function editDay($id, $day)
    {
        $nutrition = NutritionInput::findOrFail($id);
        $parsed = $this->parseNutritionPlan($nutrition->nutrition_plan);
        $dayKey = "Day $day";
        // dd($meals);
        return view('nutrition.edit-day', [
            'day' => $day,
            'meals' => $parsed['plan'][$dayKey] ?? [],
            'nutritionId' => $id
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
        $nutrition = NutritionInput::findOrFail($id);
        $rawText = $nutrition->nutrition_plan;

        $parsed = $this->parseNutritionPlan($rawText);
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

        return redirect()->route('nutrition.show', $nutrition->id)->with('success', "Day $day updated successfully.");
    }
}
