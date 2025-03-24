<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI-Powered Nutrition Planner</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">

    <h2 class="mb-4">Add Nutrition Planner</h2>

    <form action="{{ route('nutrition.index') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-md-6">
                <label>Age</label>
                <input type="number" name="age" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label>Height (cm)</label>
                <input type="number" name="height" class="form-control" required>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-md-6">
                <label>Weight (kg)</label>
                <input type="number" name="weight" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label>Gender</label>
                <select name="gender" class="form-control" required>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                </select>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-md-6">
                <label>Goal</label>
                <select name="goal" class="form-control" required>
                    <option value="Weight Loss">Weight Loss</option>
                    <option value="Muscle Gain">Muscle Gain</option>
                    <option value="Maintain Health">Maintain Health</option>
                </select>
            </div>
            <div class="col-md-6">
                <label>Meals Per Day</label>
                <input type="number" name="meals_per_day" class="form-control" required>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-6">
                <label>Diet Type</label>
                <select name="diet_type" class="form-control" required>
                    <option value="Carnivore">Carnivore</option>
                    <option value="DASH">DASH</option>
                    <option value="Dairy-Free">Dairy-Free</option>
                    <option value="Gluten-Free">Gluten-Free</option>
                    <option value="Halal">Halal</option>
                    <option value="High-Protein">High-Protein</option>
                    <option value="Keto">Keto</option>
                    <option value="Kosher">Kosher</option>
                    <option value="Low-Carb">Low-Carb</option>
                    <option value="Mediterranean">Mediterranean</option>
                    <option value="OMAD">OMAD</option>
                    <option value="Paleo">Paleo</option>
                    <option value="Pescatarian">Pescatarian</option>
                    <option value="Plant-Based">Plant-Based</option>
                    <option value="Vegan">Vegan</option>
                    <option value="Vegetarian">Vegetarian</option>
                </select>
            </div>
          
            
        </div>
        <div class="row mt-3">
            <div class="col-md-6">
                <label>Health Conditions</label>
                <select name="health_conditions[]" class="form-control" required multiple>
                    <option value="Anemia">Anemia</option>
                    <option value="Asthma">Asthma</option>
                    <option value="Diabetic">Diabetic</option>
                    <option value="High Cholesterol">High Cholesterol</option>
                    <option value="Hypertension">Hypertension</option>
                    <option value="Insulin Resistant">Insulin Resistant</option>
                    <option value="Kidney Disease">Kidney Disease</option>
                    <option value="Obesity">Obesity</option>
                    <option value="Osteoporosis">Osteoporosis</option>
                    <option value="Pre-Diabetes">Pre-Diabetes</option>
                </select>
            </div>
            <div class="col-md-6">
                <label>Food Allergies</label>
                <select name="allergies[]" class="form-control" multiple>
                    <option value="Dairy">Dairy</option>
                    <option value="Eggs">Eggs</option>
                    <option value="Fish">Fish</option>
                    <option value="Gluten">Gluten</option>
                    <option value="Nuts">Nuts</option>
                    <option value="Peanuts">Peanuts</option>
                    <option value="Sesame">Sesame</option>
                    <option value="Shellfish">Shellfish</option>
                    <option value="Soy">Soy</option>
                    <option value="Wheat">Wheat</option>
                </select>
            </div>
        </div>
        <div class="mt-4 mb-4">
            <button type="submit" class="btn btn-primary float-end">Create Nutrition Plan</button>
            <div class="clearfix"></div>
        </div>
    </form>

    {{-- @if(isset($plan))
        <h4 class="mt-5">Generated Nutrition Plan</h4>
        <p>{{ $plan }}</p>
    @endif --}}

</body>
</html>
