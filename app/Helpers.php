<?php

if (!function_exists('goalColor')) {
    function goalColor($goal)
    {
        return match ($goal) {
            'Muscle Gain' => 'bg-base',   // Use Tailwind classes
            'Weight Loss' => 'bg-danger',
            'Maintain Health' => 'bg-primary-800',
            default => 'bg-gray-500',
        };
    }
}
function fitnessGoalColor($goal)
{
    return match ($goal) {
        'Lose Fat' => 'bg-base',   // Use Tailwind classes
        'Build Muscle' => 'bg-danger',
        'Increase Endurance' => 'bg-primary-800',
        'Improve Flexibility' => 'bg-base',   // Use Tailwind classes
        'Sports Performance' => 'bg-danger',
        'Prepare for Event' => 'bg-primary-800',
        'General Fitness' => 'bg-base',   // Use Tailwind classes
        'Post-Injury Recovery' => 'bg-danger',
        default => 'bg-gray-500',
    };
}
function getMealEmoji($type)
{
    return match (strtolower($type)) {
        'breakfast' => '🍽️',
        'lunch' => '🥗',
        'dinner' => '🐟',
        'snack' => '🍓',
        default => '🍴'
    };
}
