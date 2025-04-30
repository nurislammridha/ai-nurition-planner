<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pageTitle ?? 'Nutrition Planner' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/heroicons@2.0.18/heroicons.min.js"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">
    <link rel="stylesheet" href="{{asset('css/style.css')}}">

</head>

<body class="bg-gray-100 min-h-screen flex flex-col">

   <!-- <div class="container mx-auto mt-6"> -->
    <div class="sticky top-0 z-10 shadow backdrop-blur-lg px-4 py-4 lg:px-8">
        <h2 class="text-center text-2xl md:text-3xl font-bold text-primary-500">
            {{ $pageTitle ?? 'Nutrition Planner' }}
        </h2>
    </div>