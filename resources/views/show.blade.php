@extends('layouts.master')
@section('title', 'Nutrition Planner')
@section('content')
<div class="container mt-5">
    <div class="details-container">
        <h2 class="text-center text-xl font-semibold">{{ $nutrition->plan_duration }} days plan for {{ $nutrition->name }}</h2>
        <hr class="my-4">
        <!-- Personal Details -->
        <div class="mb-4">
            <p class="font-semibold text-lg">Personal Information</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <div class="flex items-center mb-2"><i class="fas fa-user mr-2"></i> <strong>Name: </strong>
                        {{ $nutrition->name }}</div>
                    <div class="flex items-center mb-2"><i class="fas fa-blind mr-2"></i> <strong>Age:</strong> 
                        {{ $nutrition->age }} years</div>
                    <div class="flex items-center mb-2"><i class="fas fa-mars mr-2"></i> <strong>Gender:</strong>
                        {{ ucfirst($nutrition->gender) }}</div>
                    <div class="flex items-center mb-2"><i class="fas fa-ruler mr-2"></i> <strong>Height:</strong>
                        {{ $nutrition->height }} cm</div>
                    <div class="flex items-center mb-2"><i class="fas fa-weight mr-2"></i> <strong>Weight:</strong>
                        {{ $nutrition->weight }} kg</div>
                </div>
                <div>
                    <div class="flex items-center mb-2"><i class="fas fa-calendar mr-2"></i>
                        <strong>Duration:</strong> {{ $nutrition->plan_duration }} days
                    </div>
                    <div class="flex items-center mb-2"><i class="fas fa-bullseye mr-2"></i> <strong>Goal:</strong>
                        {{ $nutrition->goal }}</div>
                    <div class="flex items-center mb-2"><i class="fas fa-seedling mr-2"></i> <strong>Diet
                            Type:</strong> {{ $nutrition->diet_type }}</div>
                    <div class="flex items-center mb-2"><i class="fas fa-utensils mr-2"></i> <strong>Meals Per
                            Day:</strong>  {{ $nutrition->meals_per_day }} Times</div>
                </div>
            </div>
        </div>

        <!-- Health Conditions -->
        <div class="mb-4">
            <p class="font-semibold text-lg">Health Conditions</p>
            <div class="space-x-2">
                @foreach($nutrition->health_conditions as $condition)
                <span class="bg-danger text-white py-1 px-3 rounded">{{ $condition }}</span>
            @endforeach
            </div>
        </div>

        <!-- Allergies -->
        <div class="mb-4">
            <p class="font-semibold text-lg">Allergies</p>
            <div class="space-x-2">
                @foreach($nutrition->allergies as $allergy)
                <span class="bg-primary-800 text-white py-1 px-3 rounded">{{ $allergy }}</span>
            @endforeach
            </div>
        </div>

<!-- Nutrition Plan -->
<div class="mb-4">
    <p class="font-semibold text-lg">Personalized Nutrition Plan</p>
    <p><strong>Based on your input, here is a personalized diet plan:</strong></p>

    @foreach($nutritionPlan as $day => $meals)
    <div class="flex items-center justify-between bg-blue-100 text-blue-800 py-2 px-4 mt-4 mb-2">
        <h6 class="text-lg font-semibold">{{ $day }}</h6>
        <a href="{{ route('nutrition.editDay', ['id' => $nutrition->id, 'day' => Str::after($day, 'Day ')]) }}"
           class="text-sm bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600">
            Edit
        </a>
    </div>
        <ul class="space-y-2">
            @foreach($meals as $mealType => $items)
                <li class="bg-gray-100 p-2 rounded">
                    <strong>{{ getMealEmoji($mealType) }} {{ $mealType }}</strong>
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
{{-- <div class="mb-4">
    <p class="font-semibold text-lg">Personalized Nutrition Plan</p>
    <p><strong>Based on your input, here is a personalized diet plan:</strong></p>

    @foreach($nutritionPlan as $day => $meals)
        <h6 class="text-center bg-blue-100 text-blue-800 py-2 mt-4 mb-2">{{ $day }}</h6>
        <ul class="space-y-2">
            @foreach($meals as $mealType => $items)
                <li class="bg-gray-100 p-2 rounded">
                    <strong>{{ getMealEmoji($mealType) }} {{ $mealType }}</strong>
                    <ul class="list-disc pl-6">
                        @foreach($items as $item)
                            <li>{{ $item }}</li>
                        @endforeach
                    </ul>
                </li>
            @endforeach
        </ul>
    @endforeach
</div> --}}

        <!-- Health Tips -->
        <div class="bg-blue-100 p-4 rounded text-blue-800">
            <strong>💡 Health Tips:</strong> {{ $healthTips }}
        </div> 
    

        <!-- Edit & Print Buttons -->
        <div class="mt-4 px-4 flex flex-col sm:flex-row sm:justify-center gap-3 text-center">
            <a href="{{ route('nutrition.edit',$nutrition) }}"  class="bg-danger text-white py-2 px-4 rounded hover:bg-yellow-600 flex items-center justify-center sm:w-auto w-full">
                <!-- Heroicon Edit Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 inline" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 3l5 5-9 9H7v-5l9-9z" />
                </svg>
                Edit Plan
            </a>
            <a href="" onclick="window.print()"
            class="bg-primary-800 text-white py-2 px-4 rounded hover:bg-green-800 flex items-center justify-center sm:w-auto w-full">
                <!-- Heroicon Print Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 inline" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 8h-4V4H9v4H5a2 2 0 00-2 2v8a2 2 0 002 2h14a2 2 0 002-2V10a2 2 0 00-2-2z" />
                </svg>
                Print Plan
            </a>
        </div>
        <!-- Export Buttons -->
        <div class="mt-4 px-4 flex flex-col sm:flex-row sm:justify-center gap-3 text-center">
            <a href="{{ route('nutrition.exportPdf', $nutrition->id) }}"   class="bg-danger text-white py-2 px-4 rounded hover:bg-yellow-600 flex items-center justify-center sm:w-auto w-full">
                <i class="fas fa-file-pdf mr-2"></i> Export PDF
            </a>
            <a href="{{ route('nutrition.exportDoc', $nutrition->id) }}"
            class="bg-primary-800 text-white py-2 px-4 rounded hover:bg-green-800 flex items-center justify-center sm:w-auto w-full">
            <i class="fas fa-file-word mr-2"></i> Export DOC
            </a>
        </div>

        <!-- Back Button -->
        <div class="mt-5 px-4 text-center">
            <a href="{{ route('nutrition.index') }}"  class="bg-primary-800 text-white py-2 px-4 rounded hover:bg-blue-600 flex items-center justify-center w-full sm:w-auto w-full max-w-xs mx-auto">
                <!-- Heroicon Arrow Left Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 inline" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
                Back to Plans
            </a>
        </div>
    </div>
</div>
@endsection

