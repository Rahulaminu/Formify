<?php

namespace App\Http\Controllers;

use App\Models\Form;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FormController extends Controller
{
    public function index()
    {
        $forms = auth()->user()->forms;
        return response()->json([
            'message' => 'Get all forms success',
            'forms' => $forms
        ], 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'limit_one_response' => 'boolean',
            'allowed_domains' => 'nullable|array'
        ]);

        $form = auth()->user()->forms()->create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'] ?? null,
            'limit_one_response' => $validated['limit_one_response'] ?? false,
            'allowed_domains' => $validated['allowed_domains'] ?? null,
        ]);

        return response()->json([
            'message' => 'Form created successfully',
            'form' => $form
        ], 201);
    }

    public function show($slug)
    {
        $form = Form::with('questions')->where('slug', $slug)->first();

        if (!$form) {
            return response()->json(['message' => 'Form not found'], 404);
        }

        $userEmail = auth()->user()->email;
        $allowedDomains = $form->allowed_domains;

        if (!$this->isEmailAllowed($userEmail, $allowedDomains)) {
            return response()->json(['message' => 'Forbidden access'], 403);
        }

        return response()->json([
            'message' => 'Get form success',
            'form' => $form
        ], 200);
    }

    private function isEmailAllowed($email, $allowedDomains)
    {
        if (empty($allowedDomains)) {
            return true;
        }

        $emailDomain = substr(strrchr($email, "@"), 1);
        return in_array($emailDomain, $allowedDomains);
    }
}
