<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWorkoutPlannerRequest;
use App\Models\Workout;
use App\Services\OpenAiService;
use App\Services\WorkoutService;
use App\Traits\SystemTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

class WorkoutController extends Controller
{
    use SystemTrait;

    public function __construct(
        private readonly OpenAiService $openAi,
        private readonly WorkoutService $workoutService
    ) {}

    public function index()
    {
        $workouts = Workout::latest()->paginate(2000);
        return view('workout.index', compact('workouts'));
    }

    public function create()
    {
        return view('workout.create');
    }

    public function store(StoreWorkoutPlannerRequest $request)
    {
        try {
            $this->increaseTimeoutAndRequest();
            $plan = $this->workoutService->generatePlan($request);

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
                'workout_plan' => $this->openAi->parseJson($plan),
            ]);
            return redirect()->route('workout.index')->with('success', 'Workout plan created successfully!');
        } catch (\Throwable $th) {
            return redirect()->route('workout.index')->with('error', $th->getMessage());
        }
    }

    public function show(Workout $workout)
    {
        $workoutPlan = $workout->workout_plan['plan'] ?? [];
        $healthTips = $workout->workout_plan['tips'] ?? null;
        return view('workout.show', compact('workout', 'workoutPlan', 'healthTips'));
    }

    //edit
    public function edit(Workout $workout)
    {
        return view('workout.edit', compact('workout'));
    }

    public function update(StoreWorkoutPlannerRequest $request, Workout $workout)
    {
        try {
            $this->increaseTimeoutAndRequest();
            $plan = $this->workoutService->generatePlan($request);

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
                'workout_plan' => $this->openAi->parseJson($plan),
            ]);
            return redirect()->route('workout.index')->with('success', 'Workout Plan updated successfully!');
        } catch (\Throwable $th) {
            return redirect()->route('workout.index')->with('error', $th->getMessage());
        }
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
