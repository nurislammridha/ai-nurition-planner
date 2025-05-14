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
/**
 * Clean and convert a ChatGPT JSON string (possibly wrapped in markdown) to a PHP array.
 *
 * @param string $stringifiedJson
 * @return array|null
 */
function parseChatGptNutritionPlan(string $stringifiedJson): ?array
{
    // Remove markdown code fences and language identifier like ```json
    $cleaned = preg_replace('/^```json|```$/m', '', $stringifiedJson);

    // Trim whitespace and extra quotes
    $cleaned = trim($cleaned, "\" \n\r\t\v\0");

    // Decode the cleaned JSON
    $data = json_decode($cleaned, true);

    // Return null on failure
    if (json_last_error() !== JSON_ERROR_NONE) {
        // Optional: Log the error
        // Log::error('JSON decode error: ' . json_last_error_msg());
        return null;
    }

    return $data;
}
