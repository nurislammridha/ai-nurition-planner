<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class OpenAiService
{
    public function sendRequest($messages = [], string $openAiKey = '', int $timeout = 120)
    {
        return Http::withHeaders([
            'Authorization' => 'Bearer ' . $openAiKey,
            'Content-Type' => 'application/json',
        ])
            ->timeout($timeout)
            ->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4o-mini',
                'messages' => $messages,
                'temperature' => 0.7
            ]);
    }

    public function parseJson(string $stringifiedJson): ?array
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
}
