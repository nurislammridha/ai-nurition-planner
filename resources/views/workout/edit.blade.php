@extends('layouts.master')
@section('title', 'Workout Planner')
@section('content')
<div class="container mx-auto mt-5 p-6  shadow-md rounded-lg max-w-3xl">
    <h2 class="text-center text-xl font-semibold">Create Workout Plan</h2>
    <hr class="my-4">

    <form action="{{ route('workout.store') }}" method="POST">
        @csrf
        <!-- Name & Email -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block font-medium">Your Name<span class="text-red-500 font-bold">*</span></label>
                <input type="text" name="name" class="w-full p-2 border rounded focus:ring focus:ring-blue-300"
                    placeholder="Enter your name" required>
            </div>
            <div>
                <label class="block font-medium">Your Age<span class="text-red-500 font-bold">*</span></label>
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
                <label class="block font-medium">Fitness Goals<span class="text-red-500 font-bold">*</span></label>
                <select name="fitness_goals" class="w-full p-2 border rounded focus:ring focus:ring-blue-300" required>

                    <option value="Lose Fat">Lose Fat</option>
                    <option value="Build Muscle">Build Muscle</option>
                    <option value="Increase Endurance">Increase Endurance</option>
                    <option value="Improve Flexibility">Improve Flexibility</option>
                    <option value="Sports Performance">Sports Performance</option>
                    <option value="Prepare for Event">Prepare for Event</option>
                    <option value="General Fitness">General Fitness</option>
                    <option value="Post-Injury Recovery">Post-Injury Recovery</option>
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
                <label class="block font-medium">Training Level<span class="text-red-500 font-bold">*</span></label>
                <select name="training_level" class="w-full p-2 border rounded focus:ring focus:ring-blue-300" required>

                    <option value="Beginner">Beginner</option>
                    <option value="Intermediate">Intermediate</option>
                    <option value="Advanced">Advanced</option>
                </select>
            </div>
            <div>
                <label class="block font-medium">Preferred Training Style<span class="text-red-500 font-bold">*</span></label>
                <select name="preferred_training_style" class="w-full p-2 border rounded focus:ring focus:ring-blue-300" required>

                    <option value="Strength Training">Strength Training</option>
                    <option value="HIIT">HIIT</option>
                    <option value="Functional Fitness">Functional Fitness</option>
                    <option value="Body Weight">Body Weight</option>
                    <option value="Yoga/Pilates">Yoga/Pilates</option>
                    <option value="Cross Fit Style">Cross Fit Style</option>
                </select>
            </div>
        </div>
        <!-- Goal & Meals Per Day -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
            <div>
                <label class="block font-medium">Training Days per Week<span class="text-red-500 font-bold">*</span></label>
                <select name="training_days_per_week" class="w-full p-2 border rounded focus:ring focus:ring-blue-300" required>

                    <option value="One Day">One Day</option>
                    <option value="Two Day">Two Day</option>
                    <option value="Three Day">Three Day</option>
                    <option value="Four Day">Four Day</option>
                </select>
            </div>
            <div>
                <label class="block font-medium">Preferred Session Length (minutes)<span class="text-red-500 font-bold">*</span></label>
                <input name="preferred_session_length" type="number" class="w-full p-2 border rounded focus:ring focus:ring-blue-300"
                    placeholder="Enter preferred session length" required>
            </div>
        </div>
        <!-- Goal & Meals Per Day -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
            <div>
                <label class="block font-medium">Lifestyle Activity Level<span class="text-red-500 font-bold">*</span></label>
                <select name="lifestyle_activity_level" class="w-full p-2 border rounded focus:ring focus:ring-blue-300" required>

                    <option value="Sedentary">Sedentary</option>
                    <option value="Moderately Active">Moderately Active</option>
                    <option value="Highly Active">Highly Active</option>
                </select>
            </div>
            <div>
                <label class="block font-medium">Stress Level<span class="text-red-500 font-bold">*</span></label>
                <select name="stress_level" class="w-full p-2 border rounded focus:ring focus:ring-blue-300" required>

                    <option value="Low">Low</option>
                    <option value="Medium">Medium</option>
                    <option value="High">High</option>
                </select>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
            <div>
                <label class="block font-medium">Sleep Quality<span class="text-red-500 font-bold">*</span></label>
                <select name="sleep_quality" class="w-full p-2 border rounded focus:ring focus:ring-blue-300" required>
    
                    <option value="Good">Good</option>
                    <option value="Average">Average</option>
                    <option value="Poor">Poor</option>
                </select>
            </div>
            <div>
                <label class="block font-medium">Plan Duration<span class="text-red-500 font-bold">*</span></label>
                <select name="plan_duration" class="w-full p-2 border rounded focus:ring focus:ring-blue-300" required>
                    <option value="">Select plan duration</option>
                    <option value="1">One day</option>
                    <option value="7">7 days</option>
                    <option value="15">15 days</option>
                    <option value="30">30 days</option>
                </select>
            </div>
        </div>
      

        <!-- Health Conditions & Food Allergies -->

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
            <div>
                <label class="block font-medium">Injures/Health Conditions<span class="text-red-500 font-bold">*</span></label>
                <select name="injuries_health_conditions[]" id="healthConditions" multiple
                    class="w-full p-2 border rounded focus:ring focus:ring-blue-300">
                    <option value="None">None</option>
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
                <label class="block font-medium">Available Equipment</label>
                <select name="available_equipments[]" id="allergies" multiple class="w-full p-2 border rounded focus:ring focus:ring-blue-300">
                    <option value="Bodyweight Only">Bodyweight Only</option>
                    <option value="Dumbbells">Dumbbells</option>
                    <option value="Barbells">Barbells</option>
                    <option value="Machines">Machines</option>
                    <option value="Resistance Bands">Resistance Bands</option>
                    <option value="Kettlebells">Kettlebells</option>
                    <option value="TRX/Suspension Trainer">TRX/Suspension Trainer</option>
                    <option value="Treadmill/Cardio Machines">Treadmill/Cardio Machines</option>
                </select>
            </div>
        </div>


        <!-- Submit Button -->
        <button type="submit"
            class="w-full bg-primary-800 text-white p-3 mt-5 rounded hover:bg-green-700 transition">
            Submit
        </button>
    </form>
</div>

@endsection
   
    
   