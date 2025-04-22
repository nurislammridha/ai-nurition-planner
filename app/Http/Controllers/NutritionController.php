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


        // Call OpenAI API for AI-Powered Nutrition Plan
        $openaiResponse = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
            'Content-Type' => 'application/json',
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                ['role' => 'system', 'content' => 'You are a nutritionist providing diet plans based on user input.'],
                ['role' => 'user', 'content' => "I am {$request->age} years old, {$request->height} cm tall, weighing {$request->weight} kg. My goal is {$request->goal}. I have health conditions: " . json_encode($request->health_conditions) . " and allergies: " . json_encode($request->allergies) . ". I take {$request->meals_per_day} times meals per day. Suggest a personalized diet plan for {$request->plan_duration} days."]
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
        return redirect()->route('nutrition.index')->with('success', 'Plan saved!');
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
        // $sections = preg_split('/\*\*Day (\d+)-(\d+):\*\*/', $rawText, -1, PREG_SPLIT_DELIM_CAPTURE);
        $sections = preg_split('/Day (\d+)-(\d+):/', $rawText, -1, PREG_SPLIT_DELIM_CAPTURE);

        $parsedPlan = [];
        $tips = '';
        $inTips = false;
        for ($i = 1; $i < count($sections); $i += 3) {
            $startDay = $sections[$i];
            $endDay = $sections[$i + 1];
            $content = $sections[$i + 2];

            // preg_match_all('/\*\*(.*?)\*\*\s*- ([^-]*)/', $content, $matches, PREG_SET_ORDER);
            preg_match_all('/-\s*(\w+):\s*(.*)/', $content, $matches, PREG_SET_ORDER);

            $meals = [];
            foreach ($matches as $match) {
                $mealType = trim($match[1]);
                $mealDetails = trim($match[2]);
                $meals[$mealType][] = $mealDetails;
            }

            for ($d = $startDay; $d <= $endDay; $d++) {
                $parsedPlan["Day $d"] = $meals;
            }
        }

        // Extract the tip text after the last meal block
        // $lastDayPattern = '/\*\*Day \d+-\d+:\*\*.*?\*\*Dinner:\*\*.*?(?:\n|$)/s';
        // if (preg_match($lastDayPattern, $rawText, $match)) {
        //     $tips = trim(str_replace($match[0], '', $rawText));
        // }
        $lines = preg_split("/\r\n|\n|\r/", $rawText);
        foreach ($lines as $line) {
            $line = trim($line);

            // If we've reached the tips section
            if (stripos($line, 'remember to') !== false && !$inTips) {
                $inTips = true;
                $tips .= $line . ' ';
                continue;
            }

            if ($inTips) {
                $tips .= $line . ' ';
                continue;
            }
        }

        return [
            'plan' => $parsedPlan,
            'tips' => $tips
        ];
    }



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
                ['role' => 'system', 'content' => 'You are a nutritionist providing diet plans based on user input.'],
                ['role' => 'user', 'content' => "I am {$request->age} years old, {$request->height} cm tall, weighing {$request->weight} kg. My goal is {$request->goal}. I have health conditions: " . json_encode($request->health_conditions) . " and allergies: " . json_encode($request->allergies) . ". I take {$request->meals_per_day} times meals per day. Suggest a personalized diet plan for {$request->plan_duration} days."]
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
}
