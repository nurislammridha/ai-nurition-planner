@extends('layouts.master')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-100 px-4 py-12">
    <div class="bg-white shadow-md rounded-lg w-full max-w-2xl p-8">
        <h2 class="text-2xl font-bold text-center text-blue-700 mb-6">Edit Meals for Day {{ $day }}</h2>

        <form method="POST" action="{{ route('nutrition.updateDay', [$nutritionId, $day]) }}" class="space-y-6">
            @csrf

            @foreach($meals as $mealType => $items)
                <div>
                    <label class="block text-lg font-semibold text-gray-800 mb-2">{{ ucfirst($mealType) }}</label>
                    @foreach($items as $index => $item)
                        <textarea
                            name="meals[{{ $mealType }}][]"
                            rows="2"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400 mb-3 resize-none"
                        >{{ old("meals.$mealType.$index", $item) }}</textarea>
                    @endforeach
                </div>
            @endforeach

            <div class="text-center">
                <button
                    type="submit"
                    class="bg-primary-800 hover:bg-green-600 text-white font-semibold py-2 px-6 rounded-lg shadow-md transition-all duration-200"
                >
                     Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

