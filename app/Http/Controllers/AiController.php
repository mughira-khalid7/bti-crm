<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AiController extends Controller
{
    public function assist(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:job_description,notes',
            'context' => 'nullable|string',
            'title' => 'nullable|string',
            'url' => 'nullable|string',
        ]);

        $apiKey = env('OPENAI_API_KEY');
        if (!$apiKey) {
            return response()->json([
                'success' => false,
                'message' => 'AI key not configured',
            ], 422);
        }

        $goal = $validated['type'] === 'job_description'
            ? 'Rewrite a concise, clear job description (120-180 words).'
            : 'Write concise, actionable notes (3-6 bullet points).';

        $ack = "\n\n— Written with AI assistance";

        $prompt = trim(
            ($validated['title'] ? "Title: {$validated['title']}\n" : '') .
            ($validated['url'] ? "URL: {$validated['url']}\n" : '') .
            ($validated['context'] ? "Context:\n{$validated['context']}\n" : '') .
            "\nTask: {$goal} Use neutral tone, no fluff, no greetings."
        );

        try {
            $response = Http::withToken($apiKey)
                ->timeout(20)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => env('OPENAI_MODEL', 'gpt-3.5-turbo'),
                    'messages' => [
                        ['role' => 'system', 'content' => 'You are a helpful writing assistant for a CRM app.'],
                        ['role' => 'user', 'content' => $prompt],
                    ],
                    'temperature' => 0.4,
                    'max_tokens' => 400,
                ]);

            if ($response->failed()) {
                return response()->json([
                    'success' => false,
                    'message' => 'AI request failed',
                ], 500);
            }

            $text = data_get($response->json(), 'choices.0.message.content');
            if (!$text) {
                return response()->json([
                    'success' => false,
                    'message' => 'AI response empty',
                ], 500);
            }

            return response()->json([
                'success' => true,
                'text' => trim($text) . $ack,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'AI error: ' . $e->getMessage(),
            ], 500);
        }
    }
}


