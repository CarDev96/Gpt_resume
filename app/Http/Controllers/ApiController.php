<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;

class ApiController extends Controller
{
    public function getToken()
    {
        $token = Crypt::encrypt(csrf_token());

        return response()->json(['token' => $token], Response::HTTP_OK);

    }
    public function index(request $request): JsonResponse
    {
        $text = $request->text;

        $jsonPath = storage_path('../public/json/cv.json');

        $jsonContent = file_get_contents($jsonPath);

        $data = json_decode($jsonContent, true);

        $test = "De estos curriculums, $text" . json_encode($data);

        $search = "¿Cuantos años tiene?";

        $data = Http::withHeaders([
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer '.env('OPENAI_API_KEY'),
                ])
                ->post("https://api.openai.com/v1/chat/completions", [
                    "model" => "gpt-3.5-turbo",
                    'messages' => [
                        [
                        "role" => "user",
                        "content" => $test
                    ]
                    ],
                ])
                ->json();

        return response()->json($data['choices'][0]['message']['content'], 200, array(), JSON_PRETTY_PRINT);
    }
}
