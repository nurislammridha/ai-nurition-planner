<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI-Powered Nutrition Planner</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">

    <h2 class="mb-4">Update Nutrition Planner</h2>

    <form 
    action="{{ route('nutrition.update',$nutrition) }}" 
    method="POST">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-md-6">
                <label>Age</label>
                <input type="number" name="age" class="form-control" value="{{ $nutrition->age }}" required>
            </div>
            <div class="col-md-6">
                <label>Height (cm)</label>
                <input type="number" name="height" class="form-control" value="{{ $nutrition->height }}" required>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-md-6">
                <label>Weight (kg)</label>
                <input type="number" name="weight" class="form-control" value="{{ $nutrition->weight }}" required>
            </div>
            <div class="col-md-6">
                <label>Gender</label>
                <select name="gender" class="form-control" required>
                    <option value="Male" {{ $nutrition->gender==='Male'?'selected':'' }}>Male</option>
                    <option value="Female"  {{ $nutrition->gender==='Female'?'selected':'' }}>Female</option>
                </select>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-md-6">
                <label>Goal</label>
                <select name="goal" class="form-control" required>
                    <option value="Weight Loss"  {{ $nutrition->goal==='Weight Loss'?'selected':'' }}>Weight Loss</option>
                    <option value="Muscle Gain"  {{ $nutrition->goal==='Muscle Gain'?'selected':'' }}>Muscle Gain</option>
                    <option value="Maintain Health"  {{ $nutrition->goal==='Maintain Health'?'selected':'' }}>Maintain Health</option>
                </select>
            </div>
            <div class="col-md-6">
                <label>Meals Per Day</label>
                <input type="number" name="meals_per_day" class="form-control" value="{{ $nutrition->meals_per_day }}" required>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-6">
                <label>Diet Type</label>
                <select name="diet_type" class="form-control" required>
                    <option value="Carnivore"  {{ $nutrition->diet_type==='Carnivore'?'selected':'' }}>Carnivore</option>
                    <option value="DASH"  {{ $nutrition->diet_type==='DASH'?'selected':'' }}>DASH</option>
                    <option value="Dairy-Free"  {{ $nutrition->diet_type==='Dairy-Free'?'selected':'' }}>Dairy-Free</option>
                    <option value="Gluten-Free"  {{ $nutrition->diet_type==='Gluten-Free'?'selected':'' }}>Gluten-Free</option>
                    <option value="Halal"  {{ $nutrition->diet_type==='Halal'?'selected':'' }}>Halal</option>
                    <option value="High-Protein"  {{ $nutrition->diet_type==='High-Protein'?'selected':'' }}>High-Protein</option>
                    <option value="Keto"  {{ $nutrition->diet_type==='Keto'?'selected':'' }}>Keto</option>
                    <option value="Kosher"  {{ $nutrition->diet_type==='Kosher'?'selected':'' }}>Kosher</option>
                    <option value="Low-Carb"  {{ $nutrition->diet_type==='Low-Carb'?'selected':'' }}>Low-Carb</option>
                    <option value="Mediterranean"  {{ $nutrition->diet_type==='Mediterranean'?'selected':'' }}>Mediterranean</option>
                    <option value="OMAD"  {{ $nutrition->diet_type==='OMAD'?'selected':'' }}>OMAD</option>
                    <option value="Paleo"  {{ $nutrition->diet_type==='Paleo'?'selected':'' }}>Paleo</option>
                    <option value="Pescatarian"  {{ $nutrition->diet_type==='Pescatarian'?'selected':'' }}>Pescatarian</option>
                    <option value="Plant-Based"  {{ $nutrition->diet_type==='Plant-Based'?'selected':'' }}>Plant-Based</option>
                    <option value="Vegan"  {{ $nutrition->diet_type==='Vegan'?'selected':'' }}>Vegan</option>
                    <option value="Vegetarian"  {{ $nutrition->diet_type==='Vegetarian'?'selected':'' }}>Vegetarian</option>
                </select>
            </div>
          
            
        </div>
        <div class="row mt-3">
            <div class="col-md-6">
                <label>Health Conditions</label>
                <select name="health_conditions[]" class="form-control" required multiple>
                    @php
            $selected_conditions = is_array($nutrition->health_conditions) 
                ? $nutrition->health_conditions 
                : json_decode($nutrition->health_conditions, true);
        @endphp
                    <option value="Anemia"  {{ in_array("Anemia", $selected_conditions)?'selected':'' }}>Anemia</option>
                    <option value="Asthma"  {{ in_array("Asthma", $selected_conditions)?'selected':'' }}>Asthma</option>
                    <option value="Diabetic"  {{ in_array("Diabetic", $selected_conditions)?'selected':'' }}>Diabetic</option>
                    <option value="High Cholesterol"  {{ in_array("High Cholesterol", $selected_conditions)?'selected':'' }}>High Cholesterol</option>
                    <option value="Hypertension"  {{ in_array("Hypertension", $selected_conditions)?'selected':'' }}>Hypertension</option>
                    <option value="Insulin Resistant"  {{in_array("Insulin Resistant", $selected_conditions)?'selected':'' }}>Insulin Resistant</option>
                    <option value="Kidney Disease"  {{in_array("Kidney Disease", $selected_conditions)?'selected':'' }}>Kidney Disease</option>
                    <option value="Obesity"  {{in_array("Obesity", $selected_conditions)?'selected':'' }}>Obesity</option>
                    <option value="Osteoporosis"  {{in_array("Osteoporosis", $selected_conditions)?'selected':'' }}>Osteoporosis</option>
                    <option value="Pre-Diabetes"  {{in_array("Pre-Diabetes", $selected_conditions)?'selected':'' }}>Pre-Diabetes</option>
                </select>
            </div>
            <div class="col-md-6">
                <label>Food Allergies</label>
                <select name="allergies[]" class="form-control" multiple>
                    @php
                    $selected_conditions = is_array($nutrition->allergies) 
                        ? $nutrition->allergies 
                        : json_decode($nutrition->allergies, true);
                @endphp
                    <option value="Dairy" {{ in_array("Dairy", $selected_conditions)?'selected':'' }}>Dairy</option>
                    <option value="Eggs" {{ in_array("Eggs", $selected_conditions)?'selected':'' }}>Eggs</option>
                    <option value="Fish" {{ in_array("Fish", $selected_conditions)?'selected':'' }}>Fish</option>
                    <option value="Gluten" {{ in_array("Gluten", $selected_conditions)?'selected':'' }}>Gluten</option>
                    <option value="Nuts" {{ in_array("Nuts", $selected_conditions)?'selected':'' }}>Nuts</option>
                    <option value="Peanuts" {{ in_array("Peanuts", $selected_conditions)?'selected':'' }}>Peanuts</option>
                    <option value="Sesame" {{ in_array("Sesame", $selected_conditions)?'selected':'' }}>Sesame</option>
                    <option value="Shellfish" {{ in_array("Shellfish", $selected_conditions)?'selected':'' }}>Shellfish</option>
                    <option value="Soy" {{ in_array("Soy", $selected_conditions)?'selected':'' }}>Soy</option>
                    <option value="Wheat" {{ in_array("Wheat", $selected_conditions)?'selected':'' }}>Wheat</option>
                </select>
            </div>
        </div>
        <div class="mt-4 mb-4">
            <button type="submit" class="btn btn-primary float-end">Update Nutrition Plan</button>
            <div class="clearfix"></div>
        </div>
    </form>

   

</body>
</html>
