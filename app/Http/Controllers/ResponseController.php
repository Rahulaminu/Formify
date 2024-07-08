<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Models\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ResponseController extends Controller
{
    public function store(Request $request, Form $form)
    {
        // Check if the user's email domain is allowed
        if (!$this->isAllowedDomain($form, Auth::user()->email)) {
            return response()->json(['message' => 'Forbidden access'], 403);
        }

        // Check if the form is limited to one response per user
        if ($form->limit_one_response && $form->responses()->where('user_id', Auth::id())->exists()) {
            return response()->json(['message' => 'You can not submit form twice'], 422);
        }

        // Validate the request
        $request->validate([
            'answers' => 'required|array',
            'answers.*.question_id' => 'required|exists:questions,id',
            'answers.*.value' => 'required',
        ]);

        // Create the response with valid user_id
        $response = $form->responses()->create([
            'form_id' => $request->input('form_id'),
            'user_id' => $request->input('user_id', Auth::id()), // Use Auth::id() if user_id is not provided
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Save the answers
        foreach ($request->answers as $answer) {
            $response->answers()->create([
                'question_id' => $answer['question_id'],
                'value' => $answer['value'],
            ]);
        }

        return response()->json(['message' => 'Submit response success'], 200);
    }

    private function isAllowedDomain(Form $form, string $email)
    {
        if (empty($form->allowed_domains)) {
            return true; // If no domains are specified, allow all
        }

        $userDomain = substr(strrchr($email, "@"), 1);
        $allowedDomains = is_array($form->allowed_domains) ? implode(',', $form->allowed_domains) : $form->allowed_domains;
        $allowedDomainsArray = explode(',', $allowedDomains);

        return in_array($userDomain, $allowedDomainsArray);
    }

    public function index(Form $form)
    {
        // Check if the user is the creator of the form
        if ($form->creator_id !== Auth::id()) {
            return response()->json(['message' => 'Forbidden access'], 403);
        }

        $responses = $form->responses()->with('user', 'answers.question')->get();

        $formattedResponses = $responses->map(function ($response) {
            return [
                'date' => $response->created_at->format('Y-m-d H:i:s'),
                'user' => $response->user,
                'answers' => $response->answers->mapWithKeys(function ($answer) {
                    return [$answer->question->title => $answer->value];
                }),
            ];
        });

        return response()->json([
            'message' => 'Get responses success',
            'responses' => $formattedResponses,
        ], 200);
    }

}