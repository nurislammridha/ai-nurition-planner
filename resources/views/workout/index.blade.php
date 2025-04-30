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
              <th class="p-3">Actions</th>
          </tr>
      </thead>
      <tbody id="tableBody">
          <tr class="border-t">
              <td class="p-3">John Doe</td>
              <td class="p-3">Male</td>
              <td class="p-3">28</td>
              <td class="p-3"><span class="bg-base text-white px-2 py-1 rounded">Build Muscle</span></td>
              <td class="p-3">
                  <!-- Edit Icon -->
                  <a href="#" class="text-yellow-500">
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                          stroke="currentColor" class="w-5 h-5 inline">
                          <path stroke-linecap="round" stroke-linejoin="round"
                              d="M16.862 3.487a1.5 1.5 0 012.121 2.12l-10.83 10.83a4 4 0 01-1.294.863l-4.363 1.775a1 1 0 01-1.283-1.283l1.775-4.363a4 4 0 01.863-1.294l10.83-10.83z" />
                      </svg>
                  </a>

                  <!-- View Icon -->
                  <a href="" class="text-blue-500 ml-3">
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                          stroke="currentColor" class="w-5 h-5 inline">
                          <path stroke-linecap="round" stroke-linejoin="round"
                              d="M12 5c7 0 10 7 10 7s-3 7-10 7-10-7-10-7 3-7 10-7z" />
                          <path stroke-linecap="round" stroke-linejoin="round"
                              d="M12 9c1.657 0 3 1.343 3 3s-1.343 3-3 3-3-1.343-3-3 1.343-3 3-3z" />
                      </svg>
                  </a>

                  <!-- Delete Icon -->
                  <button class="text-red-500 ml-3" onclick="confirmDelete(this)">
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                          stroke="currentColor" class="w-5 h-5 inline">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                      </svg>
                  </button>
              </td>

          </tr>
          <tr class="border-t">
              <td class="p-3">John Doe</td>
              <td class="p-3">Male</td>
              <td class="p-3">28</td>
              <td class="p-3"><span class="bg-danger text-white px-2 py-1 rounded">Lose Fat</span>
              </td>
              <td class="p-3">
                  <!-- Edit Icon -->
                  <a href="#" class="text-yellow-500">
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                          stroke="currentColor" class="w-5 h-5 inline">
                          <path stroke-linecap="round" stroke-linejoin="round"
                              d="M16.862 3.487a1.5 1.5 0 012.121 2.12l-10.83 10.83a4 4 0 01-1.294.863l-4.363 1.775a1 1 0 01-1.283-1.283l1.775-4.363a4 4 0 01.863-1.294l10.83-10.83z" />
                      </svg>
                  </a>

                  <!-- View Icon -->
                  <a href="" class="text-blue-500 ml-3">
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                          stroke="currentColor" class="w-5 h-5 inline">
                          <path stroke-linecap="round" stroke-linejoin="round"
                              d="M12 5c7 0 10 7 10 7s-3 7-10 7-10-7-10-7 3-7 10-7z" />
                          <path stroke-linecap="round" stroke-linejoin="round"
                              d="M12 9c1.657 0 3 1.343 3 3s-1.343 3-3 3-3-1.343-3-3 1.343-3 3-3z" />
                      </svg>
                  </a>

                  <!-- Delete Icon -->
                  <button class="text-red-500 ml-3" onclick="confirmDelete(this)">
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                          stroke="currentColor" class="w-5 h-5 inline">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                      </svg>
                  </button>
              </td>

          </tr>
          <tr class="border-t">
              <td class="p-3">John Doe</td>
              <td class="p-3">Male</td>
              <td class="p-3">28</td>
              <td class="p-3"><span class="bg-base text-white px-2 py-1 rounded">Build Muscle</span></td>
              <td class="p-3">
                  <!-- Edit Icon -->
                  <a href="#" class="text-yellow-500">
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                          stroke="currentColor" class="w-5 h-5 inline">
                          <path stroke-linecap="round" stroke-linejoin="round"
                              d="M16.862 3.487a1.5 1.5 0 012.121 2.12l-10.83 10.83a4 4 0 01-1.294.863l-4.363 1.775a1 1 0 01-1.283-1.283l1.775-4.363a4 4 0 01.863-1.294l10.83-10.83z" />
                      </svg>
                  </a>

                  <!-- View Icon -->
                  <a href="" class="text-blue-500 ml-3">
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                          stroke="currentColor" class="w-5 h-5 inline">
                          <path stroke-linecap="round" stroke-linejoin="round"
                              d="M12 5c7 0 10 7 10 7s-3 7-10 7-10-7-10-7 3-7 10-7z" />
                          <path stroke-linecap="round" stroke-linejoin="round"
                              d="M12 9c1.657 0 3 1.343 3 3s-1.343 3-3 3-3-1.343-3-3 1.343-3 3-3z" />
                      </svg>
                  </a>

                  <!-- Delete Icon -->
                  <button class="text-red-500 ml-3" onclick="confirmDelete(this)">
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                          stroke="currentColor" class="w-5 h-5 inline">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                      </svg>
                  </button>
              </td>

          </tr>
          <tr class="border-t">
              <td class="p-3">John Doe</td>
              <td class="p-3">Male</td>
              <td class="p-3">28</td>
              <td class="p-3"><span class="bg-primary-800 text-white px-2 py-1 rounded">Lose Fat</span></td>
              <td class="p-3">
                  <!-- Edit Icon -->
                  <a href="#" class="text-yellow-500">
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                          stroke="currentColor" class="w-5 h-5 inline">
                          <path stroke-linecap="round" stroke-linejoin="round"
                              d="M16.862 3.487a1.5 1.5 0 012.121 2.12l-10.83 10.83a4 4 0 01-1.294.863l-4.363 1.775a1 1 0 01-1.283-1.283l1.775-4.363a4 4 0 01.863-1.294l10.83-10.83z" />
                      </svg>
                  </a>

                  <!-- View Icon -->
                  <a href="" class="text-blue-500 ml-3">
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                          stroke="currentColor" class="w-5 h-5 inline">
                          <path stroke-linecap="round" stroke-linejoin="round"
                              d="M12 5c7 0 10 7 10 7s-3 7-10 7-10-7-10-7 3-7 10-7z" />
                          <path stroke-linecap="round" stroke-linejoin="round"
                              d="M12 9c1.657 0 3 1.343 3 3s-1.343 3-3 3-3-1.343-3-3 1.343-3 3-3z" />
                      </svg>
                  </a>

                  <!-- Delete Icon -->
                  <button class="text-red-500 ml-3" onclick="confirmDelete(this)">
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                          stroke="currentColor" class="w-5 h-5 inline">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                      </svg>
                  </button>
              </td>

          </tr>
          <tr class="border-t">
              <td class="p-3">John Doe</td>
              <td class="p-3">Male</td>
              <td class="p-3">28</td>
              <td class="p-3"><span class="bg-base text-white px-2 py-1 rounded">Build Muscle</span></td>
              <td class="p-3">
                  <!-- Edit Icon -->
                  <a href="#" class="text-yellow-500">
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                          stroke="currentColor" class="w-5 h-5 inline">
                          <path stroke-linecap="round" stroke-linejoin="round"
                              d="M16.862 3.487a1.5 1.5 0 012.121 2.12l-10.83 10.83a4 4 0 01-1.294.863l-4.363 1.775a1 1 0 01-1.283-1.283l1.775-4.363a4 4 0 01.863-1.294l10.83-10.83z" />
                      </svg>
                  </a>

                  <!-- View Icon -->
                  <a href="" class="text-blue-500 ml-3">
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                          stroke="currentColor" class="w-5 h-5 inline">
                          <path stroke-linecap="round" stroke-linejoin="round"
                              d="M12 5c7 0 10 7 10 7s-3 7-10 7-10-7-10-7 3-7 10-7z" />
                          <path stroke-linecap="round" stroke-linejoin="round"
                              d="M12 9c1.657 0 3 1.343 3 3s-1.343 3-3 3-3-1.343-3-3 1.343-3 3-3z" />
                      </svg>
                  </a>

                  <!-- Delete Icon -->
                  <button class="text-red-500 ml-3" onclick="confirmDelete(this)">
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                          stroke="currentColor" class="w-5 h-5 inline">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                      </svg>
                  </button>
              </td>

          </tr>
          <tr class="border-t">
              <td class="p-3">John Doe</td>
              <td class="p-3">Male</td>
              <td class="p-3">28</td>
              <td class="p-3"><span class="bg-primary-800 text-white px-2 py-1 rounded">Build Muscle</span></td>
              <td class="p-3">
                  <!-- Edit Icon -->
                  <a href="#" class="text-yellow-500">
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                          stroke="currentColor" class="w-5 h-5 inline">
                          <path stroke-linecap="round" stroke-linejoin="round"
                              d="M16.862 3.487a1.5 1.5 0 012.121 2.12l-10.83 10.83a4 4 0 01-1.294.863l-4.363 1.775a1 1 0 01-1.283-1.283l1.775-4.363a4 4 0 01.863-1.294l10.83-10.83z" />
                      </svg>
                  </a>

                  <!-- View Icon -->
                  <a href="" class="text-blue-500 ml-3">
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                          stroke="currentColor" class="w-5 h-5 inline">
                          <path stroke-linecap="round" stroke-linejoin="round"
                              d="M12 5c7 0 10 7 10 7s-3 7-10 7-10-7-10-7 3-7 10-7z" />
                          <path stroke-linecap="round" stroke-linejoin="round"
                              d="M12 9c1.657 0 3 1.343 3 3s-1.343 3-3 3-3-1.343-3-3 1.343-3 3-3z" />
                      </svg>
                  </a>

                  <!-- Delete Icon -->
                  <button class="text-red-500 ml-3" onclick="confirmDelete(this)">
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                          stroke="currentColor" class="w-5 h-5 inline">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                      </svg>
                  </button>
              </td>

          </tr>
          <tr class="border-t">
              <td class="p-3">John Doe</td>
              <td class="p-3">Male</td>
              <td class="p-3">28</td>
              <td class="p-3"><span class="bg-base text-white px-2 py-1 rounded">Build Muscle</span></td>
              <td class="p-3">
                  <!-- Edit Icon -->
                  <a href="#" class="text-yellow-500">
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                          stroke="currentColor" class="w-5 h-5 inline">
                          <path stroke-linecap="round" stroke-linejoin="round"
                              d="M16.862 3.487a1.5 1.5 0 012.121 2.12l-10.83 10.83a4 4 0 01-1.294.863l-4.363 1.775a1 1 0 01-1.283-1.283l1.775-4.363a4 4 0 01.863-1.294l10.83-10.83z" />
                      </svg>
                  </a>

                  <!-- View Icon -->
                  <a href="" class="text-blue-500 ml-3">
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                          stroke="currentColor" class="w-5 h-5 inline">
                          <path stroke-linecap="round" stroke-linejoin="round"
                              d="M12 5c7 0 10 7 10 7s-3 7-10 7-10-7-10-7 3-7 10-7z" />
                          <path stroke-linecap="round" stroke-linejoin="round"
                              d="M12 9c1.657 0 3 1.343 3 3s-1.343 3-3 3-3-1.343-3-3 1.343-3 3-3z" />
                      </svg>
                  </a>

                  <!-- Delete Icon -->
                  <button class="text-red-500 ml-3" onclick="confirmDelete(this)">
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                          stroke="currentColor" class="w-5 h-5 inline">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                      </svg>
                  </button>
              </td>

          </tr>
      </tbody>
  </table>
</div>
@endsection
   
    
   