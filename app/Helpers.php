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
