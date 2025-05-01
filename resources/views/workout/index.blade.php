@extends('layouts.master')
@section('title', 'Workout Planner')
@section('content')
<div class="container mx-auto mt-4">
  <a href="{{ route('workout.create') }}"
      class="inline-flex items-center bg-primary-800 text-white px-3 py-1.5 text-sm font-medium rounded-md shadow-sm hover:bg-blue-700 transition">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
          stroke="currentColor" class="w-4 h-4 mr-1">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
      </svg>
      Create Plan
  </a>
</div>


<div class="container mx-auto mt-4 flex gap-4">
  <input type="text" id="searchInput" placeholder="🔍 Search by name..." class="w-1/2 p-2 border rounded"
      onkeyup="filterTable()">
  <select id="planFilter" class="w-1/2 p-2 border rounded" onchange="filterTable()">
      <option value="">Preferred Training Style</option>
      <option>Strength Training</option>
      <option>HIIT</option>
      <option>Functional Fitness</option>
      <option>Bodyweight</option>
      <option>Yoga/Pilates</option>
      <option>CrossFit Style</option>
  </select>
</div>

<div class="container mx-auto mt-6  p-6 rounded-lg shadow-lg overflow-x-auto">
  <table class="table-auto w-full text-center border-collapse">
      <thead>
          <tr class="bg-primary-800 text-white">
              <th class="p-3">Name</th>
              <th class="p-3">Gender</th>
              <th class="p-3 cursor-pointer" onclick="sortTable(2)">Age <i class="fas fa-sort"></i></th>
              <th class="p-3">Fitness Goal</th>
              <th class="p-3">Created At</th>
              <th class="p-3">Actions</th>
          </tr>
      </thead>
      <tbody id="tableBody">
        @foreach ($workouts as $workout)
        <tr class="border-t">
            <td class="p-3">{{$workout->name}}</td>
            <td class="p-3">{{$workout->gender}}</td>
            <td class="p-3">{{$workout->age}}</td>
            <td class="p-3"><span class="{{ fitnessGoalColor($workout->fitness_goals) }} text-white px-2 py-1 rounded">{{ $workout->fitness_goals }}</span></td>
            <td>{{ $workout->created_at->format('Y-m-d') }}</td>
            <td class="p-3">
                <div class="flex items-center justify-center space-x-2">
                <!-- Edit Icon -->
                <a href="{{ route('workout.edit',$workout) }}" class="text-yellow-500">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                    stroke="currentColor" class="w-4 h-4 sm:w-5 sm:h-5 inline">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M16.862 3.487a1.5 1.5 0 012.121 2.12l-10.83 10.83a4 4 0 01-1.294.863l-4.363 1.775a1 1 0 01-1.283-1.283l1.775-4.363a4 4 0 01.863-1.294l10.83-10.83z" />
                </svg>
                </a>

                <!-- View Icon -->
                <a href="{{ route('workout.show',$workout) }}" class="text-blue-500">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                stroke="currentColor" class="w-4 h-4 sm:w-5 sm:h-5 inline">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 5c7 0 10 7 10 7s-3 7-10 7-10-7-10-7 3-7 10-7z" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 9c1.657 0 3 1.343 3 3s-1.343 3-3 3-3-1.343-3-3 1.343-3 3-3z" />
                            </svg>
                </a>

                <!-- Delete Icon t -->
                <form action="{{ route('workout.destroy', $workout) }}" method="POST" style="display: inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" href="#" class="text-red-500" >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                stroke="currentColor" class="w-4 h-4 sm:w-5 sm:h-5 inline">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                            </svg>
                        </button>
                </form>
               
            </td>

        </tr>
        @endforeach
      </tbody>
  </table>
</div>
@endsection
   
    
   