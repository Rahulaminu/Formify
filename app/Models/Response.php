<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Response extends Model
{
    use HasFactory;

    protected $fillable = ['form_id', 'user_id', 'created_at', 'updated_at'];

    protected $attributes = [
        'user_id' => 0, // Default value
    ];

    // App\Models\Form
    public function responses()
    {
        return $this->hasMany(Response::class);
    }

    // App\Models\Response
    public function form()
    {
        return $this->belongsTo(Form::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function answers()
    {
        return $this->hasMany(Answer::class);
    }

    // App\Models\Answer
    public function response()
    {
        return $this->belongsTo(Response::class);
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

}