<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI-Powered Nutrition Planner</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Nutrition Plan Details</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Age:</strong> {{ $nutrition->age }}</p>
                        <p><strong>Height:</strong> {{ $nutrition->height }} cm</p>
                        <p><strong>Weight:</strong> {{ $nutrition->weight }} kg</p>
                        <p><strong>Gender:</strong> {{ ucfirst($nutrition->gender) }}</p>
                        <p><strong>Goal:</strong> {{ $nutrition->goal }}</p>
                        <p><strong>Diet Type:</strong> {{ $nutrition->diet_type }}</p>
                        <p><strong>Meals Per Day:</strong> {{ $nutrition->meals_per_day }}</p>
    
                        {{-- Decode JSON data --}}
                        <p><strong>Health Conditions:</strong></p>
                        <ul>
                            @foreach($nutrition->health_conditions as $condition)
                                <li>{{ $condition }}</li>
                            @endforeach
                        </ul>
                        <p><strong>Allergies:</strong> 
                            <ul>
                                @foreach($nutrition->allergies as $allergy)
                                    <li>{{ $allergy }}</li>
                                @endforeach
                            </ul>
                        </p>
    
                        <p><strong>Nutrition Plan:</strong></p>
                        <div class="alert alert-secondary">
                            {{ $nutrition->nutrition_plan }}
                        </div>
                    </div>
                    <div class="card-footer text-end">
                        <a href="{{ route('nutrition.index') }}" class="btn btn-secondary btn-sm">Back</a>
                        {{-- <a href="{{ route('nutrition.edit', $post) }}" class="btn btn-warning btn-sm">Edit</a> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
    
   

</body>
</html>
