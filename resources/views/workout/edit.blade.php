@extends('layouts.master')
@section('title', 'Workout Planner')
@section('content')
<div class="container mx-auto mt-5 p-6  shadow-md rounded-lg max-w-3xl">
    <h2 class="text-center text-xl font-semibold">Create Workout Plan</h2>
    <hr class="my-4">
@include('partials.messages')
    <form action="{{ route('workout.update',$workout) }}" method="POST">
        @csrf
        @method('PUT')
        <!-- Name & Email -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block font-medium">Your Name<span class="text-red-500 font-bold">*</span></label>
                <input type="text" name="name" value="{{ $workout->name }}" class="w-full p-2 border rounded focus:ring focus:ring-blue-300"
                    placeholder="Enter your name" required>
            </div>
            <div>
                <label class="block font-medium">Your Age<span class="text-red-500 font-bold">*</span></label>
                <input type="number" name="age" value="{{ $workout->age }}" class="w-full p-2 border rounded focus:ring focus:ring-blue-300"
                    placeholder="Enter your age" required>
            </div>
        </div>
     

        <!-- Height & Weight -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
            <div>
                <label class="block font-medium">Height (in cm)<span class="text-red-500 font-bold">*</span></label>
                <input type="number" name="height" value="{{ $workout->height }}" class="w-full p-2 border rounded focus:ring focus:ring-blue-300"
                    placeholder="Enter your height" required>
            </div>
            <div>
                <label class="block font-medium">Weight (in kg)<span class="text-red-500 font-bold">*</span></label>
                <input type="number" name="weight" value="{{ $workout->weight }}" class="w-full p-2 border rounded focus:ring focus:ring-blue-300"
                    placeholder="Enter your weight" required>
            </div>
        </div>

        <!-- Plan Duration & Gender -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
            <div>
                <label class="block font-medium">Fitness Goals<span class="text-red-500 font-bold">*</span></label>
                <select name="fitness_goals" class="w-full p-2 border rounded focus:ring focus:ring-blue-300" required>
 <option value="Lose Fat">Lose Fat</option>
                    <option value="Build Muscle" {{ $workout->fitness_goals==='Build Muscle'?'selected':'' }}>Build Muscle</option>
                    <option value="Increase Endurance" {{ $workout->fitness_goals==='Increase Endurance'?'selected':'' }}>Increase Endurance</option>
                    <option value="Improve Flexibility" {{ $workout->fitness_goals==='Improve Flexibility'?'selected':'' }}>Improve Flexibility</option>
                    <option value="Sports Performance" {{ $workout->fitness_goals==='Sports Performance'?'selected':'' }}>Sports Performance</option>
                    <option value="Prepare for Event" {{ $workout->fitness_goals==='Prepare for Event'?'selected':'' }}>Prepare for Event</option>
                    <option value="General Fitness" {{ $workout->fitness_goals==='General Fitness'?'selected':'' }}>General Fitness</option>
                    <option value="Post-Injury Recovery" {{ $workout->fitness_goals==='Post-Injury Recovery'?'selected':'' }}>Post-Injury Recovery</option>
                </select>
            </div>
            <div>
                <label class="block font-medium">Gender<span class="text-red-500 font-bold">*</span></label>
                <div class="flex space-x-4">
                    <label class="inline-flex items-center">
                        <input type="radio" name="gender" value="Male" {{ $workout->gender==='Male'?'selected':'' }} class="form-radio text-blue-600" checked>
                        <span class="ml-2">Male</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" name="gender" value="Female" {{ $workout->gender==='Female'?'selected':'' }} class="form-radio text-blue-600">
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

                    <option value="Beginner" {{ $workout->training_level==='Beginner'?'selected':'' }}>Beginner</option>
                    <option value="Intermediate" {{ $workout->training_level==='Intermediate'?'selected':'' }}>Intermediate</option>
                    <option value="Advanced" {{ $workout->training_level==='Advanced'?'selected':'' }}>Advanced</option>
                </select>
            </div>
            <div>
                <label class="block font-medium">Preferred Training Style<span class="text-red-500 font-bold">*</span></label>
                <select name="preferred_training_style" class="w-full p-2 border rounded focus:ring focus:ring-blue-300" required>
                    <option value="Strength Training" {{ $workout->preferred_training_style==='Strength Training'?'selected':'' }}>Strength Training</option>
                    <option value="HIIT" {{ $workout->preferred_training_style==='HIIT'?'selected':'' }}>HIIT</option>
                    <option value="Functional Fitness" {{ $workout->preferred_training_style==='Functional Fitness'?'selected':'' }}>Functional Fitness</option>
                    <option value="Body Weight" {{ $workout->preferred_training_style==='Body Weight'?'selected':'' }}>Body Weight</option>
                    <option value="Yoga/Pilates" {{ $workout->preferred_training_style==='Yoga/Pilates'?'selected':'' }}>Yoga/Pilates</option>
                    <option value="Cross Fit Style" {{ $workout->preferred_training_style==='Cross Fit Style'?'selected':'' }}>Cross Fit Style</option>
                </select>
            </div>
        </div>
        <!-- Goal & Meals Per Day -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
            <div>
                <label class="block font-medium">Training Days per Week<span class="text-red-500 font-bold">*</span></label>
                <select name="training_days_per_week" class="w-full p-2 border rounded focus:ring focus:ring-blue-300" required>

                    <option value="One Day" {{ $workout->training_days_per_week==='One Day'?'selected':'' }}>One Day</option>
                    <option value="Two Day" {{ $workout->training_days_per_week==='Two Day'?'selected':'' }}>Two Day</option>
                    <option value="Three Day" {{ $workout->training_days_per_week==='Three Day'?'selected':'' }}>Three Day</option>
                    <option value="Four Day" {{ $workout->training_days_per_week==='Four Day'?'selected':'' }}>Four Day</option>
                </select>
            </div>
            <div>
                <label class="block font-medium">Preferred Session Length (minutes)<span class="text-red-500 font-bold">*</span></label>
                <input name="preferred_session_length" value="{{ $workout->preferred_session_length }}"  type="number" class="w-full p-2 border rounded focus:ring focus:ring-blue-300"
                    placeholder="Enter preferred session length" required>
            </div>
        </div>
        <!-- Goal & Meals Per Day -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
            <div>
                <label class="block font-medium">Lifestyle Activity Level<span class="text-red-500 font-bold">*</span></label>
                <select name="lifestyle_activity_level" class="w-full p-2 border rounded focus:ring focus:ring-blue-300" required>

                    <option value="Sedentary" {{ $workout->lifestyle_activity_level==='Sedentary'?'selected':'' }}>Sedentary</option>
                    <option value="Moderately Active" {{ $workout->lifestyle_activity_level==='Moderately Active'?'selected':'' }}>Moderately Active</option>
                    <option value="Highly Active" {{ $workout->lifestyle_activity_level==='Highly Active'?'selected':'' }}>Highly Active</option>
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
    
                    <option value="Good" {{ $workout->sleep_quality==='Good'?'selected':'' }}>Good</option>
                    <option value="Average" {{ $workout->sleep_quality==='Average'?'selected':'' }}>Average</option>
                    <option value="Poor" {{ $workout->sleep_quality==='Poor'?'selected':'' }}>Poor</option>
                </select>
            </div>
            <div>
                <label class="block font-medium">Plan Duration<span class="text-red-500 font-bold">*</span></label>
                <select name="plan_duration" class="w-full p-2 border rounded focus:ring focus:ring-blue-300" required>
                    <option value="1" {{ $workout->plan_duration==='1'?'selected':'' }}>One day</option>
                    <option value="7" {{ $workout->plan_duration==='7'?'selected':'' }}>7 days</option>
                    <option value="15" {{ $workout->plan_duration==='15'?'selected':'' }}>15 days</option>
                    <option value="30" {{ $workout->plan_duration==='30'?'selected':'' }}>30 days</option>
                </select>
            </div>
        </div>
      

        <!-- Health Conditions & Food Allergies -->
  {{-- function for selected value --}}
  @php
  function isSelected($value, $selectedValues) {
      return in_array($value, (array) $selectedValues) ? 'selected' : '';
  }
  $selectedConditions = old('injuries_health_conditions', $workout->injuries_health_conditions ?? []);
  @endphp
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
            <div>
                <label class="block font-medium">Injures/Health Conditions<span class="text-red-500 font-bold">*</span></label>
                <select name="injuries_health_conditions[]" id="healthConditions" multiple
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
            @php
            function isEquipment($value, $selectedValues) {
                return in_array($value, (array) $selectedValues) ? 'selected' : '';
            }
        
            $selectedEquipment = old('available_equipments', $workout->available_equipments ?? []);
            @endphp
            <div>
                <label class="block font-medium">Available Equipment</label>
                <select name="available_equipments[]" id="allergies" multiple class="w-full p-2 border rounded focus:ring focus:ring-blue-300">
                    <option value="Bodyweight Only" {{ isEquipment('Bodyweight Only', $selectedEquipment) }}>Bodyweight Only</option>
                    <option value="Dumbbells" {{ isEquipment('Dumbbells', $selectedEquipment) }}>Dumbbells</option>
                    <option value="Barbells" {{ isEquipment('Barbells', $selectedEquipment) }}>Barbells</option>
                    <option value="Machines" {{ isEquipment('Machines', $selectedEquipment) }}>Machines</option>
                    <option value="Resistance Bands" {{ isEquipment('Resistance Bands', $selectedEquipment) }}>Resistance Bands</option>
                    <option value="Kettlebells" {{ isEquipment('Kettlebells', $selectedEquipment) }}>Kettlebells</option>
                    <option value="TRX/Suspension Trainer" {{ isEquipment('TRX/Suspension Trainer', $selectedEquipment) }}>TRX/Suspension Trainer</option>
                    <option value="Treadmill/Cardio Machines" {{ isEquipment('Treadmill/Cardio Machines', $selectedEquipment) }}>Treadmill/Cardio Machines</option>
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
   
    
   