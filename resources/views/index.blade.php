<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI-Powered Nutrition Planner</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body >
  
    <div class="container mt-5">
        <div class="d-flex justify-content-between mb-5">
            <h2>AI Nutrition Planner</h2>
            <a href="{{ route('nutrition.create') }}" class="btn btn-primary">Create post</a>
        </div>
        
        @if (session('success'))
            <div class="alert alert-success mt-2">{{ session('success') }}</div>
        @endif
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Age</th>
                    <th>Height</th>
                    <th>Weight</th>
                    <th>Goal</th>
                    <th>Plan</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            @foreach($posts as $post)
                <tr>
                    <td>{{ $post->age }}</td>
                    <td>{{ $post->height }}</td>
                    <td>{{ $post->weight }}</td>
                    <td>{{ $post->goal }}</td>
                    <td>{{ Str::limit($post->nutrition_plan, 20) }}</td>
                    <td>{{ $post->created_at->format('Y-m-d') }}</td>
                    <td>
                        <a href="{{ route('nutrition.show',$post) }}" class="btn btn-info btn-sm">View</a>
                        <a href="{{ route('nutrition.edit',$post) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('nutrition.destroy', $post) }}" method="POST" style="display: inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    
   

</body>
</html>
