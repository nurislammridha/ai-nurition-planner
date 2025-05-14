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
        return view('nutrition.index', compact('posts'));
        // return view('nutrition');
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('nutrition.create');
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
            'model' => 'gpt-4o-mini',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are a certified nutritionist and expert diet planner. Always prioritize user safety and strictly follow dietary restrictions. Never suggest any ingredient the user is allergic to. Even indirect suggestions, alternatives, or substitutions that include allergens must be avoided.'
                ],
                [
                    'role' => 'user',
                    'content' =>
                    "Please create a personalized diet plan based on the following information. ⚠️ I have a serious allergy to the following foods: " . implode(', ', $request->allergies) . ".\n\n" .
                        "🚫 DO NOT include these allergens in any meal. Not as main ingredients, not as condiments, and not as alternatives.\n\n" .
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

                        "⚠️ IMPORTANT: You must generate a complete, unique meal plan for exactly {$request->plan_duration} days. Each day must contain exactly {$request->meals_per_day} meals. Do not repeat or reuse meals or days.\n\n" .

                        "✅ FORMAT REQUIREMENT: Respond only in raw JSON (inside triple backticks), using the following format:\n\n" .
                        "```json\n" .
                        "{\n" .
                        "  \"plan\": [\n" .
                        "    {\n" .
                        "      \"day\": 1,\n" .
                        "      \"meals\": [\n" .
                        "        { \"Breakfast\": [\"Item 1\", \"Item 2\"] },\n" .
                        "        { \"Snack\": [\"Item 1\", \"Item 2\"] },\n" .
                        "        ... (up to {$request->meals_per_day} meals per day, meal names can be Breakfast, Snack, Lunch, Dinner, Supper, etc.)\n" .
                        "      ]\n" .
                        "    },\n" .
                        "    ... (up to {$request->plan_duration} days)\n" .
                        "  ],\n" .
                        "  \"health_tips\": \"Short tip.\"\n" .
                        "}\n" .
                        "```\n\n" .
                        "⛔ Do NOT include any explanation, comment, or text before or after the JSON. Just the triple-backtick-wrapped JSON block."
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
        NutritionInput::create([
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
            'nutrition_plan' => $planJson
        ]);
        return redirect()->route('nutrition.index')->with('success', 'Plan created!');
    }
    /**
     * Display the specified resource.
     */


    public function show(NutritionInput $nutrition)
    {
        $nutritionPlan = $nutrition->nutrition_plan['plan'] ?? [];
        $healthTips = $nutrition->nutrition_plan['health_tips'] ?? null;

        return view('nutrition.show', compact('nutrition', 'nutritionPlan', 'healthTips'));
    }

    public function edit(NutritionInput $nutrition)
    {
        return view('nutrition.edit', compact('nutrition'));
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
            'model' => 'gpt-4o-mini',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are a certified nutritionist and expert diet planner. Always prioritize user safety and strictly follow dietary restrictions. Never suggest any ingredient the user is allergic to. Even indirect suggestions, alternatives, or substitutions that include allergens must be avoided.'
                ],
                [
                    'role' => 'user',
                    'content' =>
                    "Please create a personalized diet plan based on the following information. ⚠️ I have a serious allergy to the following foods: " . implode(', ', $request->allergies) . ".\n\n" .
                        "🚫 DO NOT include these allergens in any meal. Not as main ingredients, not as condiments, and not as alternatives.\n\n" .
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

                        "⚠️ IMPORTANT: You must generate a complete, unique meal plan for exactly {$request->plan_duration} days. Each day must contain exactly {$request->meals_per_day} meals. Do not repeat or reuse meals or days.\n\n" .

                        "✅ FORMAT REQUIREMENT: Respond only in raw JSON (inside triple backticks), using the following format:\n\n" .
                        "```json\n" .
                        "{\n" .
                        "  \"plan\": [\n" .
                        "    {\n" .
                        "      \"day\": 1,\n" .
                        "      \"meals\": [\n" .
                        "        { \"Breakfast\": [\"Item 1\", \"Item 2\"] },\n" .
                        "        { \"Snack\": [\"Item 1\", \"Item 2\"] },\n" .
                        "        ... (up to {$request->meals_per_day} meals per day, meal names can be Breakfast, Snack, Lunch, Dinner, Supper, etc.)\n" .
                        "      ]\n" .
                        "    },\n" .
                        "    ... (up to {$request->plan_duration} days)\n" .
                        "  ],\n" .
                        "  \"health_tips\": \"Short tip.\"\n" .
                        "}\n" .
                        "```\n\n" .
                        "⛔ Do NOT include any explanation, comment, or text before or after the JSON. Just the triple-backtick-wrapped JSON block."
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
        $planJson = parseChatGptNutritionPlan($plan);
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
            'nutrition_plan' => $planJson
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
        $nutritionPlan = $nutrition->nutrition_plan['plan'] ?? [];
        $healthTips = $nutrition->nutrition_plan['tips'] ?? '';

        $pdf = Pdf::loadView('nutrition.export', compact('nutrition', 'nutritionPlan', 'healthTips'));
        return $pdf->download('nutrition_plan.pdf');
    }

    public function exportDoc($id)
    {
        $nutrition = NutritionInput::findOrFail($id);
        $nutritionPlan = $nutrition->nutrition_plan['plan'] ?? [];
        $healthTips = $nutrition->nutrition_plan['tips'] ?? '';

        $html = view('nutrition.export', compact('nutrition', 'nutritionPlan', 'healthTips'))->render();

        return response($html)
            ->header('Content-Type', 'application/msword')
            ->header('Content-Disposition', 'attachment; filename="nutrition_plan.doc"');
    }

    //foset data in day wise edit field
    public function editDay($id, $day)
    {
        $nutrition = NutritionInput::findOrFail($id);
        $nutritionPlan = $nutrition->nutrition_plan;

        $meals = [];

        // Find the specific day's data from the JSON structure
        foreach ($nutritionPlan['plan'] as $entry) {
            if ($entry['day'] == $day) {
                foreach ($entry['meals'] as $mealEntry) {
                    foreach ($mealEntry as $mealType => $items) {
                        $meals[$mealType] = $items;
                    }
                }
                break;
            }
        }

        return view('nutrition.edit-day', [
            'day' => $day,
            'meals' => $meals,
            'nutritionId' => $id,
        ]);
    }
    //update day wise data
    public function updateDay(Request $request, $id, $day)
    {
        $nutrition = NutritionInput::findOrFail($id);
        $nutritionPlan = $nutrition->nutrition_plan;

        $updatedMeals = [];
        foreach ($request->input('meals') as $mealType => $items) {
            $cleanedItems = array_filter(array_map('trim', $items));
            if (!empty($cleanedItems)) {
                $updatedMeals[] = [
                    $mealType => $cleanedItems
                ];
            }
        }

        // Update only the selected day in the plan array
        foreach ($nutritionPlan['plan'] as &$entry) {
            if ($entry['day'] == $day) {
                $entry['meals'] = $updatedMeals;
                break;
            }
        }

        $nutrition->nutrition_plan = $nutritionPlan;
        $nutrition->save();

        return redirect()->route('nutrition.show', $nutrition->id)
            ->with('success', "Day $day updated successfully.");
    }
}
