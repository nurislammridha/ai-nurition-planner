@extends('layouts.master')
@section('title', 'Workout Planner')
@section('content')
{{-- @dd($workoutPlan) --}}
<div class="container mt-5">
    <div class="details-container">
        <h2 class="text-center text-xl font-semibold">{{ $workout->plan_duration }} days workout plan for {{ $workout->name }}</h2>
        <hr class="my-4">
        <!-- Personal Details -->
        <div class="mb-6">
            <p class="font-semibold text-xl text-gray-800 mb-4 flex items-center">
                <i class="fas fa-address-card text-blue-500 mr-2"></i> Personal Information
            </p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-gray-700">
                <!-- Column 1 -->
                <div class="space-y-3">
                    <div class="flex items-center"><i class="fas fa-user text-indigo-500 w-5 mr-2"></i><strong class="mr-1">Name:</strong> {{ $workout->name }}</div>
                    <div class="flex items-center"><i class="fas fa-birthday-cake text-pink-500 w-5 mr-2"></i><strong class="mr-1">Age:</strong> {{ $workout->age }} years</div>
                    <div class="flex items-center"><i class="fas fa-venus-mars text-purple-500 w-5 mr-2"></i><strong class="mr-1">Gender:</strong> {{ ucfirst($workout->gender) }}</div>
                    <div class="flex items-center"><i class="fas fa-ruler-vertical text-green-500 w-5 mr-2"></i><strong class="mr-1">Height:</strong> {{ $workout->height }} cm</div>
                    <div class="flex items-center"><i class="fas fa-weight-hanging text-orange-500 w-5 mr-2"></i><strong class="mr-1">Weight:</strong> {{ $workout->weight }} kg</div>
                    <div class="flex items-center"><i class="fas fa-hourglass-half text-yellow-500 w-5 mr-2"></i><strong class="mr-1">Plan Duration:</strong> {{ $workout->plan_duration }} days</div>
                    <div class="flex items-center"><i class="fas fa-bullseye text-red-500 w-5 mr-2"></i><strong class="mr-1">Fitness Goal:</strong> {{ $workout->fitness_goals }}</div>
                </div>
        
                <!-- Column 2 -->
                <div class="space-y-3">
                   
                    <div class="flex items-center"><i class="fas fa-chart-line text-blue-500 w-5 mr-2"></i><strong class="mr-1">Training Level:</strong> {{ $workout->training_level }}</div>
                    <div class="flex items-center"><i class="fas fa-dumbbell text-gray-600 w-5 mr-2"></i><strong class="mr-1">Training Style:</strong> {{ $workout->preferred_training_style }}</div>
                    <div class="flex items-center"><i class="fas fa-calendar-week text-indigo-400 w-5 mr-2"></i><strong class="mr-1">Training Days:</strong> {{ $workout->training_days_per_week }}</div>
                    <div class="flex items-center"><i class="fas fa-stopwatch text-pink-600 w-5 mr-2"></i><strong class="mr-1">Session Length:</strong> {{ $workout->preferred_session_length }}</div>
                    <div class="flex items-center"><i class="fas fa-walking text-green-600 w-5 mr-2"></i><strong class="mr-1">Lifestyle Activity:</strong> {{ $workout->lifestyle_activity_level }}</div>
                    <div class="flex items-center"><i class="fas fa-tired text-yellow-600 w-5 mr-2"></i><strong class="mr-1">Stress Level:</strong> {{ $workout->stress_level }}</div>
                    <div class="flex items-center"><i class="fas fa-bed text-purple-600 w-5 mr-2"></i><strong class="mr-1">Sleep Quality:</strong> {{ $workout->sleep_quality }}</div>
                </div>
            </div>
        </div>
        
        <!-- Health Conditions -->
        <div class="mb-4">
            <p class="font-semibold text-lg">Injures Health Conditions</p>
            <div class="space-x-2">
                @foreach($workout->injuries_health_conditions as $condition)
                <span class="bg-danger text-white py-1 px-3 rounded">{{ $condition }}</span>
            @endforeach
            </div>
        </div>

        <!-- Allergies -->
        <div class="mb-4">
            <p class="font-semibold text-lg">Available Equipments</p>
            <div class="space-x-2">
                @foreach($workout->available_equipments as $available_equipment)
                <span class="bg-primary-800 text-white py-1 px-3 rounded">{{ $available_equipment }}</span>
            @endforeach
            </div>
        </div>

<!-- workout Plan -->

<div class="mb-6">
    <p class="font-semibold text-2xl mb-2">Personalized Workout Plan</p>
    <p class="mb-4 text-gray-700"><strong>Based on your input, here is your customized workout schedule:</strong></p>

    @foreach($workoutPlan as $dayData)
        <div class="mb-6 border border-blue-200 rounded-lg overflow-hidden shadow-sm">
            <div class="flex items-center justify-between bg-blue-100 text-blue-800 px-4 py-2">
                <h6 class="text-lg font-semibold">{{ $dayData['day'] }}</h6>
                @if(Str::startsWith($dayData['day'], 'Day '))
                    <a href="{{ route('workout.editDay', ['id' => $workout->id, 'day' => Str::after($dayData['day'], 'Day ')]) }}"
                       class="text-sm bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600">
                        Edit
                    </a>
                @endif
            </div>

            <div class="p-4 bg-white space-y-4">
                @if(count($dayData['workout']) === 1 && strtolower($dayData['workout'][0]) === 'rest day')
                    <div class="bg-yellow-50 text-yellow-800 p-4 rounded text-center font-semibold">
                        🛌 Rest Day – Take time to recover and relax!
                    </div>
                @else
                    @php
                        $sections = [];
                        $currentSection = null;

                        foreach ($dayData['workout'] as $item) {
                            if (Str::startsWith($item, '**') && Str::endsWith($item, '**')) {
                                $currentSection = trim($item, '** ');
                                $sections[$currentSection] = [];
                            } elseif ($currentSection) {
                                $sections[$currentSection][] = $item;
                            }
                        }
                    @endphp

                    @foreach($sections as $sectionTitle => $items)
                        <div class="bg-gray-50 p-3 rounded border border-gray-200">
                            <h4 class="font-semibold text-blue-700 mb-2">{{ $sectionTitle }}</h4>
                            <ul class="list-disc list-inside space-y-1 text-gray-800">
                                @foreach($items as $item)
                                    <li>{{ $item }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    @endforeach
</div>


        <!-- Health Tips -->
        <div class="bg-blue-100 p-4 rounded text-blue-800">
            <strong>💡 Health Tips:</strong> {{ $healthTips }}
        </div> 
    

        <!-- Edit & Print Buttons -->
        <div class="mt-4 px-4 flex flex-col sm:flex-row sm:justify-center gap-3 text-center">
            <a href="{{ route('workout.edit',$workout) }}"  class="bg-danger text-white py-2 px-4 rounded hover:bg-yellow-600 flex items-center justify-center sm:w-auto w-full">
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
            <a href="{{ route('workout.exportPdf', $workout->id) }}"   class="bg-danger text-white py-2 px-4 rounded hover:bg-yellow-600 flex items-center justify-center sm:w-auto w-full">
                <i class="fas fa-file-pdf mr-2"></i> Export PDF
            </a>
            <a href="{{ route('workout.exportDoc', $workout->id) }}"
            class="bg-primary-800 text-white py-2 px-4 rounded hover:bg-green-800 flex items-center justify-center sm:w-auto w-full">
            <i class="fas fa-file-word mr-2"></i> Export DOC
            </a>
        </div>

        <!-- Back Button -->
        <div class="mt-5 px-4 text-center">
            <a href="{{ route('workout.index') }}"  class="bg-primary-800 text-white py-2 px-4 rounded hover:bg-blue-600 flex items-center justify-center w-full sm:w-auto w-full max-w-xs mx-auto">
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

