@extends('layouts.master')
@section('content')
<div class="container mx-auto mt-5 p-6  shadow-md rounded-lg max-w-3xl">
    <h2 class="text-center text-xl font-semibold">Create Nutrition Plan</h2>
    <hr class="my-4">

    <form action="{{ route('nutrition.index') }}" method="POST">
        @csrf
        <!-- Name & Age -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block font-medium">Your Name<span class="text-red-500 font-bold">*</span></label>
                <input type="text" name="name" class="w-full p-2 border rounded focus:ring focus:ring-blue-300"
                    placeholder="Enter your name" required>
            </div>
            <div>
                <label class="block font-medium">Age<span class="text-red-500 font-bold">*</span></label>
                <input type="number" name="age" class="w-full p-2 border rounded focus:ring focus:ring-blue-300"
                    placeholder="Enter your age" required>
            </div>
        </div>

        <!-- Height & Weight -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
            <div>
                <label class="block font-medium">Height (in cm)<span class="text-red-500 font-bold">*</span></label>
                <input type="number" name="height" class="w-full p-2 border rounded focus:ring focus:ring-blue-300"
                    placeholder="Enter your height" required>
            </div>
            <div>
                <label class="block font-medium">Weight (in kg)<span class="text-red-500 font-bold">*</span></label>
                <input type="number" name="weight" class="w-full p-2 border rounded focus:ring focus:ring-blue-300"
                    placeholder="Enter your weight" required>
            </div>
        </div>

        <!-- Plan Duration & Gender -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
            <div>
                <label class="block font-medium">Plan Duration<span class="text-red-500 font-bold">*</span></label>
                <select name="plan_duration" class="w-full p-2 border rounded focus:ring focus:ring-blue-300" required>
                    <option value="">Select plan duration</option>
                    <option value="1">One day</option>
                    <option value="15">15 days</option>
                    <option value="30">30 days</option>
                </select>
            </div>
            <div>
                <label class="block font-medium">Gender<span class="text-red-500 font-bold">*</span></label>
                <div class="flex space-x-4">
                    <label class="inline-flex items-center">
                        <input type="radio" name="gender" value="Male" class="form-radio text-blue-600" checked>
                        <span class="ml-2">Male</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" name="gender" value="Female" class="form-radio text-blue-600">
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
                    <option value="Weight Loss">Weight Loss</option>
                    <option value="Muscle Gain">Muscle Gain</option>
                    <option value="Maintain Health">Maintain Health</option>
                </select>
            </div>
            <div>
                <label class="block font-medium">Meals Per Day<span class="text-red-500 font-bold">*</span></label>
                <select name="meals_per_day" class="w-full p-2 border rounded focus:ring focus:ring-blue-300" required>
                    <option value="">Select meals per day</option>
                    <option value="2">2 times</option>
                    <option value="3">3 times</option>
                    <option value="4">4 times</option>
                    <option value="5">5 times</option>
                </select>
            </div>
        </div>

        <!-- Diet Type -->
        <div class="mt-4">
            <label class="block font-medium">Diet Type<span class="text-red-500 font-bold">*</span></label>
            <select name="diet_type" class="w-full p-2 border rounded focus:ring focus:ring-blue-300" required>
                <option value="">Select a diet type</option>
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

        <!-- Health Conditions & Food Allergies -->

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
            <div>
                <label class="block font-medium">Health Conditions</label>
                <select name="health_conditions[]" id="healthConditions" multiple
                    class="w-full p-2 border rounded focus:ring focus:ring-blue-300">
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
            <div>
                <label class="block font-medium">Food Allergies</label>
                <select name="allergies[]" id="allergies" class="w-full p-2 border rounded focus:ring focus:ring-blue-300" multiple>
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


        <!-- Submit Button -->
        <button type="submit"
            class="w-full bg-primary-800 text-white p-3 mt-5 rounded hover:bg-green-700 transition">
            Create Nutrition Plan
        </button>
    </form>
</div>
@endsection









    {{-- <form action="{{ route('nutrition.index') }}" method="POST">
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
    </form> --}}

