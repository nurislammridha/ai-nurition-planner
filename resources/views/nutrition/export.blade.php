<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Nutrition Plan</title>
    <style>
        body { font-family: sans-serif; line-height: 1.6; }
        h2, h4 { text-align: center; }
        ul { margin-left: 20px; }
        .section-title { font-weight: bold; margin-top: 20px; font-size: 18px; }
        .day-heading { margin-top: 15px; font-weight: bold; }
    </style>
</head>
<body>

    <h2>{{ $nutrition->plan_duration }} Days Nutrition Plan for {{ $nutrition->name }}</h2>

    <div>
        <p class="section-title">Personal Information</p>
        <p>Name: {{ $nutrition->name }}</p>
        <p>Age: {{ $nutrition->age }} years</p>
        <p>Gender: {{ ucfirst($nutrition->gender) }}</p>
        <p>Height: {{ $nutrition->height }} cm</p>
        <p>Weight: {{ $nutrition->weight }} kg</p>
        <p>Duration: {{ $nutrition->plan_duration }} days</p>
        <p>Goal: {{ $nutrition->goal }}</p>
        <p>Diet Type: {{ $nutrition->diet_type }}</p>
        <p>Meals Per Day: {{ $nutrition->meals_per_day }} times</p>
    </div>

    <div>
        <p class="section-title">Health Conditions</p>
        <ul>
            @foreach($nutrition->health_conditions as $condition)
                <li>{{ $condition }}</li>
            @endforeach
        </ul>
    </div>

    <div>
        <p class="section-title">Allergies</p>
        <ul>
            @foreach($nutrition->allergies as $allergy)
                <li>{{ $allergy }}</li>
            @endforeach
        </ul>
    </div>

    <div>
        <p class="section-title">Personalized Nutrition Plan</p>
        @foreach($nutritionPlan as $dayEntry)
            <p class="day-heading">Day {{ $dayEntry['day'] }}</p>
            <ul>
                @foreach($dayEntry['meals'] as $meal)
                    @foreach($meal as $mealType => $items)
                        <li>
                            <strong>{{ ucfirst($mealType) }}</strong>
                            <ul>
                                @foreach($items as $item)
                                    <li>{{ $item }}</li>
                                @endforeach
                            </ul>
                        </li>
                    @endforeach
                @endforeach
            </ul>
        @endforeach
    </div>

    <div>
        <p class="section-title">Health Tips</p>
        <p>{{ $healthTips }}</p>
    </div>

</body>
</html>
