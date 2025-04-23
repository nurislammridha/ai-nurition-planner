@extends('layouts.master')
@section('content')
<div class="container mx-auto mt-5 p-6  shadow-md rounded-lg max-w-3xl">
    <h2 class="text-center text-xl font-semibold">Create Nutrition Plan</h2>
    <hr class="my-4">

    <form  action="{{ route('nutrition.update',$nutrition) }}" 
    method="POST">
        @csrf
        @method('PUT')
        <!-- Name & Age -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block font-medium">Your Name<span class="text-red-500 font-bold">*</span></label>
                <input type="text" name="name" value="{{ $nutrition->name }}" class="w-full p-2 border rounded focus:ring focus:ring-blue-300"
                    placeholder="Enter your name" required>
            </div>
            <div>
                <label class="block font-medium">Age<span class="text-red-500 font-bold">*</span></label>
                <input type="number" name="age" value="{{ $nutrition->age }}" class="w-full p-2 border rounded focus:ring focus:ring-blue-300"
                    placeholder="Enter your age" required>
            </div>
        </div>

        <!-- Height & Weight -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
            <div>
                <label class="block font-medium">Height (in cm)<span class="text-red-500 font-bold">*</span></label>
                <input type="number" name="height" value="{{ $nutrition->height }}" class="w-full p-2 border rounded focus:ring focus:ring-blue-300"
                    placeholder="Enter your height" required>
            </div>
            <div>
                <label class="block font-medium">Weight (in kg)<span class="text-red-500 font-bold">*</span></label>
                <input type="number" name="weight" value="{{ $nutrition->weight }}" class="w-full p-2 border rounded focus:ring focus:ring-blue-300"
                    placeholder="Enter your weight" required>
            </div>
        </div>

        <!-- Plan Duration & Gender -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
            <div>
                <label class="block font-medium">Plan Duration<span class="text-red-500 font-bold">*</span></label>
                <select name="plan_duration" class="w-full p-2 border rounded focus:ring focus:ring-blue-300" required>
                    <option value="">Select plan duration</option>
                    <option value="1" {{ $nutrition->plan_duration==='1'?'selected':'' }}>One day</option>
                    <option value="7" {{ $nutrition->plan_duration==='7'?'selected':'' }}>7 days</option>
                    <option value="15" {{ $nutrition->plan_duration==='15'?'selected':'' }}>15 days</option>
                    <option value="30" {{ $nutrition->plan_duration==='30'?'selected':'' }}>30 days</option>
                </select>
            </div>
            <div>
                <label class="block font-medium">Gender<span class="text-red-500 font-bold">*</span></label>
                <div class="flex space-x-4">
                    <label class="inline-flex items-center">
                        <input type="radio" name="gender" value="Male" {{ $nutrition->gender==='Male'?'selected':'' }} class="form-radio text-blue-600" checked>
                        <span class="ml-2">Male</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" name="gender" value="Female" {{ $nutrition->gender==='Female'?'selected':'' }} class="form-radio text-blue-600">
                        <span class="ml-2">Female</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Goal & Meals Per Day -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
            <div>
                <label class="block font-medium">Goal<span class="text-red-500 font-bold">*</span></label>
                <select name="goal" class="w-full p-2 border rounded focus:ring focus:ring-blue-300" required>
                    <option value="">Select goal</option>
                    <option value="Weight Loss" {{ $nutrition->goal==='Weight Loss'?'selected':'' }}>Weight Loss</option>
                    <option value="Muscle Gain" {{ $nutrition->goal==='Muscle Gain'?'selected':'' }}>Muscle Gain</option>
                    <option value="Maintain Health" {{ $nutrition->goal==='Maintain Health'?'selected':'' }}>Maintain Health</option>
                </select>
            </div>
            <div>
                <label class="block font-medium">Meals Per Day<span class="text-red-500 font-bold">*</span></label>
                <select name="meals_per_day" class="w-full p-2 border rounded focus:ring focus:ring-blue-300" required>
                    <option value="">Select meals per day</option>
                    <option value="2" {{ $nutrition->meals_per_day===2?'selected':'' }}>2 times</option>
                    <option value="3" {{ $nutrition->meals_per_day===3?'selected':'' }}>3 times</option>
                    <option value="4" {{ $nutrition->meals_per_day===4?'selected':'' }}>4 times</option>
                    <option value="5" {{ $nutrition->meals_per_day===5?'selected':'' }}>5 times</option>
                </select>
            </div>
        </div>

        <!-- Diet Type -->
        <div class="mt-4">
            <label class="block font-medium">Diet Type<span class="text-red-500 font-bold">*</span></label>
            <select name="diet_type" class="w-full p-2 border rounded focus:ring focus:ring-blue-300" required>
                <option value="">Select a diet type</option>
                <option value="Carnivore" {{ $nutrition->diet_type==='Carnivore'?'selected':'' }}>Carnivore</option>
                <option value="DASH" {{ $nutrition->diet_type==='DASH'?'selected':'' }}>DASH</option>
                <option value="Dairy-Free" {{ $nutrition->diet_type==='Dairy-Free'?'selected':'' }}>Dairy-Free</option>
                <option value="Gluten-Free" {{ $nutrition->diet_type==='Gluten-Free'?'selected':'' }}>Gluten-Free</option>
                <option value="Halal" {{ $nutrition->diet_type==='Halal'?'selected':'' }}>Halal</option>
                <option value="High-Protein" {{ $nutrition->diet_type==='High-Protein'?'selected':'' }}>High-Protein</option>
                <option value="Keto" {{ $nutrition->diet_type==='Keto'?'selected':'' }}>Keto</option>
                <option value="Kosher" {{ $nutrition->diet_type==='Kosher'?'selected':'' }}>Kosher</option>
                <option value="Low-Carb" {{ $nutrition->diet_type==='Low-Carb'?'selected':'' }}>Low-Carb</option>
                <option value="Mediterranean" {{ $nutrition->diet_type==='Mediterranean'?'selected':'' }}>Mediterranean</option>
                <option value="OMAD" {{ $nutrition->diet_type==='OMAD'?'selected':'' }}>OMAD</option>
                <option value="Paleo" {{ $nutrition->diet_type==='Paleo'?'selected':'' }}>Paleo</option>
                <option value="Pescatarian" {{ $nutrition->diet_type==='Pescatarian'?'selected':'' }}>Pescatarian</option>
                <option value="Plant-Based" {{ $nutrition->diet_type==='Plant-Based'?'selected':'' }}>Plant-Based</option>
                <option value="Vegan" {{ $nutrition->diet_type==='Vegan'?'selected':'' }}>Vegan</option>
                <option value="Vegetarian" {{ $nutrition->diet_type==='Vegetarian'?'selected':'' }}>Vegetarian</option>
            </select>
        </div>

        <!-- Health Conditions & Food Allergies -->
        {{-- function for selected value --}}
        @php
        function isSelected($value, $selectedValues) {
            return in_array($value, (array) $selectedValues) ? 'selected' : '';
        }
    
        $selectedConditions = old('health_conditions', $nutrition->health_conditions ?? []);
        @endphp
    
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
            <div>
                <label class="block font-medium">Health Conditions</label>
                <select name="health_conditions[]" id="healthConditions" multiple
                    class="w-full p-2 border rounded focus:ring focus:ring-blue-300">
                    <option value="None" {{ isSelected('None', $selectedConditions) }}>None</option>
                    <option value="Anemia" {{ isSelected('Anemia', $selectedConditions) }}>Anemia</option>
                    <option value="Asthma" {{ isSelected('Asthma', $selectedConditions) }}>Asthma</option>
                    <option value="Diabetic" {{ isSelected('Diabetic', $selectedConditions) }}>Diabetic</option>
                    <option value="High Cholesterol" {{ isSelected('High Cholesterol', $selectedConditions) }}>High Cholesterol</option>
                    <option value="Hypertension" {{ isSelected('Hypertension', $selectedConditions) }}>Hypertension</option>
                    <option value="Insulin Resistant" {{ isSelected('Insulin Resistant', $selectedConditions) }}>Insulin Resistant</option>
                    <option value="Kidney Disease" {{ isSelected('Kidney Disease', $selectedConditions) }}>Kidney Disease</option>
                    <option value="Obesity" {{ isSelected('Obesity', $selectedConditions) }}>Obesity</option>
                    <option value="Osteoporosis" {{ isSelected('Osteoporosis', $selectedConditions) }}>Osteoporosis</option>
                    <option value="Pre-Diabetes" {{ isSelected('Pre-Diabetes', $selectedConditions) }}>Pre-Diabetes</option>
                </select>
            </div>
              {{-- function for selected value --}}
            @php
            function isAllergy($value, $selectedValues) {
                return in_array($value, (array) $selectedValues) ? 'selected' : '';
            }
        
            $selectedAllergy = old('allergies', $nutrition->allergies ?? []);
            @endphp
            <div>
                <label class="block font-medium">Food Allergies</label>
                <select name="allergies[]" id="allergies" class="w-full p-2 border rounded focus:ring focus:ring-blue-300" multiple>
                    <option value="None" {{ isAllergy('None', $selectedAllergy) }}>None</option>
                    <option value="Dairy" {{ isAllergy('Dairy', $selectedAllergy) }}>Dairy</option>
                    <option value="Eggs" {{ isAllergy('Eggs', $selectedAllergy) }}>Eggs</option>
                    <option value="Fish" {{ isAllergy('Fish', $selectedAllergy) }}>Fish</option>
                    <option value="Gluten" {{ isAllergy('Gluten', $selectedAllergy) }}>Gluten</option>
                    <option value="Nuts" {{ isAllergy('Nuts', $selectedAllergy) }}>Nuts</option>
                    <option value="Peanuts" {{ isAllergy('Peanuts', $selectedAllergy) }}>Peanuts</option>
                    <option value="Sesame" {{ isAllergy('Sesame', $selectedAllergy) }}>Sesame</option>
                    <option value="Shellfish" {{ isAllergy('Shellfish', $selectedAllergy) }}>Shellfish</option>
                    <option value="Soy" {{ isAllergy('Soy', $selectedAllergy) }}>Soy</option>
                    <option value="Wheat" {{ isAllergy('Wheat', $selectedAllergy) }}>Wheat</option>
                </select>
            </div>
        </div>


        <!-- Submit Button -->
        <button type="submit"
            class="w-full bg-primary-800 text-white p-3 mt-5 rounded hover:bg-green-700 transition">
            Update Nutrition Plan
        </button>
    </form>
</div>
@endsection
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const dietType = document.querySelector('select[name="diet_type"]');
        const mealsPerDay = document.querySelector('select[name="meals_per_day"]');

        function toggleMealsField() {
            if (dietType.value === 'OMAD') {
                mealsPerDay.disabled = true;
                mealsPerDay.classList.add('bg-gray-200', 'cursor-not-allowed');
                mealsPerDay.value = ''; // optional: clear selection
            } else {
                mealsPerDay.disabled = false;
                mealsPerDay.classList.remove('bg-gray-200', 'cursor-not-allowed');
            }
        }

        // Run initially in case of pre-filled data
        toggleMealsField();

        // Trigger on change
        dietType.addEventListener('change', toggleMealsField);
    });
</script>