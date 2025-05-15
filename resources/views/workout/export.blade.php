<!-- workout/export.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Workout Plan</title>
    <style>
        body { font-family: sans-serif; }
        h2, h3, h4 { text-align: left; }
        ul { margin-left: 20px; padding-left: 15px; }
        .section-title { font-weight: bold; margin-top: 20px; }
    </style>
</head>
<body>

    <h2>{{ $workout->plan_duration }}-Day Workout Plan for {{ $workout->name }}</h2>

    <div>
        <p class="section-title">Personal Information</p>
        <p>Name: {{ $workout->name }}</p>
        <p>Age: {{ $workout->age }} years</p>
        <p>Gender: {{ ucfirst($workout->gender) }}</p>
        <p>Height: {{ $workout->height }} cm</p>
        <p>Weight: {{ $workout->weight }} kg</p>
        <p>Fitness Goal: {{ $workout->fitness_goals }}</p>
        <p>Training Level: {{ $workout->training_level }}</p>
        <p>Training Style: {{ $workout->preferred_training_style }}</p>
        <p>Training Days: {{ $workout->training_days_per_week }}</p>
        <p>Session Length: {{ $workout->preferred_session_length }}</p>
        <p>Lifestyle Activity: {{ $workout->lifestyle_activity_level }}</p>
        <p>Stress Level: {{ $workout->stress_level }}</p>
        <p>Sleep Quality: {{ $workout->sleep_quality }}</p>
    </div>

    @if (!empty($workout->injuries_health_conditions))
        <div>
            <p class="section-title">Injuries or Health Conditions</p>
            <ul>
                @foreach($workout->injuries_health_conditions as $condition)
                    <li>{{ $condition }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (!empty($workout->available_equipments))
        <div>
            <p class="section-title">Available Equipments</p>
            <ul>
                @foreach($workout->available_equipments as $available_equipment)
                    <li>{{ $available_equipment }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div>
        <p class="section-title">Personalized Workout Plan</p>
      @foreach($workoutPlan['plan'] as $dayPlan)
    <div style="background-color: #bfdbfe; padding: 10px; margin-top: 20px;">
        <h2>{{ $dayPlan['day'] }}</h2>
    </div>

    <ul>
        @php
            // The workout is a flat array of strings, some with section headers like "**Warm-Up (5 minutes):**"
            $currentSection = null;
            $sections = [];
        @endphp

        @foreach($dayPlan['workout'] as $line)
            @if(str_starts_with($line, '**') && str_ends_with($line, '**'))
                @php
                    // Extract section title without ** and ::: or : characters
                    $currentSection = trim(str_replace(['**', ':::','**:','**'], '', $line));
                    $sections[$currentSection] = [];
                @endphp
            @else
                @if($currentSection)
                    @php
                        $sections[$currentSection][] = $line;
                    @endphp
                @else
                    {{-- If no section, just put it under "General" --}}
                    @php
                        $sections['General'][] = $line;
                    @endphp
                @endif
            @endif
        @endforeach

        @foreach($sections as $sectionTitle => $items)
            <li>
                <strong>{{ $sectionTitle }}</strong>
                <ul>
                    @foreach($items as $item)
                        <li>{{ $item }}</li>
                    @endforeach
                </ul>
            </li>
        @endforeach
    </ul>
@endforeach

    </div>

    <div>
        <p class="section-title"> Health Tips</p>
        <p>{{ $workoutPlan['tips'] }}</p>
    </div>

</body>
</html>
