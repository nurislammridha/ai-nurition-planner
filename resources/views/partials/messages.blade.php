@if ($errors->any())
    <div class="text-red-500 py-2 shadow">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- SUccess message --}}
@if(Session::has('success'))
    <div class="text-green-500 py-2 shadow">
        {{ Session::get('success') }}
    </div>
@endif

{{-- Error message --}}
@if(Session::has('error'))
    <div class="text-red-500 py-2 shadow">
        {{ Session::get('error') }}
    </div>
@endif