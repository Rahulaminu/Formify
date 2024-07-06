<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class QuestionController extends Controller
{
    public function store(Request $request, Form $form)
    {
        // Check if the user owns the form
        if ($form->user_id !== auth()->id()) {
            return response()->json(['message' => 'Forbidden access'], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'choice_type' => 'required|in:short answer,paragraph,date,multiple choice,dropdown,checkboxes',
            'choices' => 'required_if:choice_type,multiple choice,dropdown,checkboxes|array',
            'is_required' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid field',
                'errors' => $validator->errors()
            ], 422);
        }

        $question = new Question($request->all());
        $question->form_id = $form->id;
        
        if (in_array($request->choice_type, ['multiple choice', 'dropdown', 'checkboxes'])) {
            $question->choices = implode(',', $request->choices);
        }

        $question->save();

        return response()->json([
            'message' => 'Add question success',
            'question' => $question
        ], 200);
    }

    public function destroy(Form $form, Question $question)
    {
        // Check if the user owns the form
        if ($form->user_id !== auth()->id()) {
            return response()->json(['message' => 'Forbidden access'], 403);
        }

        // Check if the question belongs to the form
        if ($question->form_id !== $form->id) {
            return response()->json(['message' => 'Question not found'], 404);
        }

        $question->delete();

        return response()->json(['message' => 'Remove question success'], 200);
    }
}
