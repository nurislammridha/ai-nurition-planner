@extends('layouts.master')
@section('title', 'Nutrition Planner')
  @section('content')
  <div class="container mx-auto mt-4 px-4">
    <a href="{{ route('nutrition.create') }}"
        class="inline-flex items-center bg-primary-800 text-white px-4 py-2 text-sm md:text-base font-medium rounded-md shadow-sm hover:bg-blue-700 transition">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
            stroke="currentColor" class="w-4 h-4 mr-1">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
        </svg>
        Create Plan
    </a>
    @if (session('success'))
<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-md mt-2">{{ session('success') }}</div>
@endif
</div>

<div class="container mx-auto mt-4 px-4">
    <div class="flex flex-col gap-4 lg:flex-row">
        <input type="text" id="searchInput" placeholder="🔍 Search by name..."
            class="w-full lg:w-1/2 p-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-green-500"
            onkeyup="filterTable()">
        <select id="planFilter"
            class="w-full lg:w-1/2 p-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-green-500"
            onchange="filterTable()">
            <option value="">Select a Goal</option>
            <option value="Weight Loss">Weight Loss</option>
            <option value="Muscle Gain">Muscle Gain</option>
            <option value="Maintain Health">Maintain Health</option>
        </select>
    </div>
</div>


<div class="container mx-auto mt-6  px-4 rounded-lg shadow-lg overflow-x-auto">
    <table class="min-w-full border-collapse text-center">
        <thead>
            <tr class="bg-primary-800 text-white text-white text-sm md:text-base">
                <th class="p-3">Name</th>
                <th class="p-3">Gender</th>
                <th class="p-3 cursor-pointer" onclick="sortTable(2)">Age <i class="fas fa-sort"></i></th>
                <th class="p-3">Goal</th>
                <th class="p-3">Created At</th>
                <th class="p-3">Actions</th>
            </tr>
        </thead>
        <tbody id="tableBody" class="text-sm md:text-base">
            @foreach($posts as $post) 
            <tr class="border-t">
                <td class="p-3">{{ $post->name }}</td>
                <td class="p-3">{{ $post->gender }}</td>
                <td class="p-3">{{ $post->age }}</td>
                <td class="p-3"><span class="{{ goalColor($post->goal) }} text-white px-2 py-1 rounded">{{ $post->goal }}</span></td>
                <td>{{ $post->created_at->format('Y-m-d') }}</td>
                <td class="p-3">
                    <div class="flex items-center justify-center space-x-2">
                    <!-- Edit Icon -->
                    <a href="{{ route('nutrition.edit',$post) }}" class="text-yellow-500">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="w-4 h-4 sm:w-5 sm:h-5 inline">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M16.862 3.487a1.5 1.5 0 012.121 2.12l-10.83 10.83a4 4 0 01-1.294.863l-4.363 1.775a1 1 0 01-1.283-1.283l1.775-4.363a4 4 0 01.863-1.294l10.83-10.83z" />
                    </svg>
                    </a>

                    <!-- View Icon -->
                    <a href="{{ route('nutrition.show',$post) }}" class="text-blue-500">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                    stroke="currentColor" class="w-4 h-4 sm:w-5 sm:h-5 inline">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 5c7 0 10 7 10 7s-3 7-10 7-10-7-10-7 3-7 10-7z" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 9c1.657 0 3 1.343 3 3s-1.343 3-3 3-3-1.343-3-3 1.343-3 3-3z" />
                                </svg>
                    </a>

                    <!-- Delete Icon t -->
                    <form action="{{ route('nutrition.destroy', $post) }}" method="POST" style="display: inline">
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
   
    
   