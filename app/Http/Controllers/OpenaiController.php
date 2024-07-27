<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class OpenaiController extends Controller
{
    public function getResponse(Request $request)
    {
        $client = new Client();
        $key = env('OPENAI_API_KEY');
        $question = $request->get('question', 'Hello, how are you?');

        try {
            $response = $client->post('https://api.openai.com/v1/chat/completions', [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $key,
                ],
                'json' => [
                    'model' => 'gpt-3.5-turbo',
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => $question,
                        ],
                    ],
                ],
            ]);

            $body = $response->getBody();
            $responseBody = json_decode($body, true);

            return response()->json($responseBody);

        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $errorResponse = $e->getResponse();
                $errorBody = $errorResponse->getBody();
                $errorData = json_decode($errorBody, true);
                return response()->json(['error' => $errorData], $errorResponse->getStatusCode());
            }

            return response()->json(['error' => 'Something went wrong. Please try again.'], 500);
        }
    }
}
