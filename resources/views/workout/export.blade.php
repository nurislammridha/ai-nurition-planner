<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Workout Plan</title>
    <style>
        body { font-family: sans-serif; }
        h2 { text-align: center; }
        ul { margin-left: 20px; }
        .section-title { font-weight: bold; margin-top: 20px; }
    </style>
</head>
<body>

    <h2>{{ $workout->plan_duration }} days workout plan for {{ $workout->name }}</h2>

    <div>
        <p class="section-title">Personal Information</p>
        <p>Name: {{ $workout->name }}</p>
        <p>Age: {{ $workout->age }} years</p>
        <p>Gender: {{ ucfirst($workout->gender) }}</p>
        <p>Height: {{ $workout->height }} cm</p>
        <p>Weight: {{ $workout->weight }} kg</p>
        <p>Plan Duration: {{ $workout->plan_duration }} days</p>
        <p>Fitness Goal: {{ $workout->fitness_goals }}</p>
        <p>Training Level: {{ $workout->training_level }}</p>
        <p>Training Style: {{ $workout->preferred_training_style }}</p>
        <p>Training Days: {{ $workout->training_days_per_week }}</p>
        <p>Session Length: {{ $workout->preferred_session_length }}</p>
        <p>Lifestyle Activity: {{ $workout->lifestyle_activity_level }}</p>
        <p>Stress Level: {{ $workout->stress_level }}</p>
        <p>Sleep Quality: {{ $workout->sleep_quality }}</p>
    </div>

    <div>
        <p class="section-title">Injures Health Conditions</p>
        <ul>
            @foreach($workout->injuries_health_conditions as $condition)
                <li>{{ $condition }}</li>
            @endforeach
        </ul>
    </div>

    <div>
        <p class="section-title">Available Equipments</p>
        <ul>
            @foreach($workout->available_equipments as $available_equipment)
                <li>{{ $available_equipment }}</li>
            @endforeach
        </ul>
    </div>

   
<div class="mb-4">
    <p class="font-semibold text-lg">Personalized Workout Plan</p>
    <p><strong>Based on your input, here is a personalized workout plan:</strong></p>

    @foreach($workoutPlan as $day => $sections)
        <div class="flex items-center justify-between bg-blue-100 text-blue-800 py-2 px-4 mt-4 mb-2">
            <h2 class="text-lg font-semibold">{{ $day }}</h6>
           
        </div>

        <ul class="space-y-2">
            @foreach($sections as $sectionTitle => $items)
                <li class="bg-gray-100 p-2 rounded">
                    <strong>{{ $sectionTitle }}</strong>
                    <ul class="list-disc pl-6">
                        @foreach($items as $item)
                            <li>{{ $item }}</li>
                        @endforeach
                    </ul>
                </li>
            @endforeach
        </ul>
    @endforeach
</div>

        <!-- Health Tips -->
        <div class="bg-blue-100 p-4 rounded text-blue-800">
            <strong>💡 Health Tips:</strong> {{ $healthTips }}
        </div> 

</body>
</html>
