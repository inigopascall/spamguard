<?php

namespace InigoPascall\SpamGuard\Models;

use Illuminate\Database\Eloquent\Model;

class FormSubmission extends Model
{
    protected $table = 'form_submissions';

    protected $fillable = [
        'ip_address',
        'user_agent',
        'banned',
        'banned_reason',
        'cover',
        'payload',
        'dispatch_seconds',
        'visits'
    ];

    protected $casts = [
        'banned_reason' => 'array',
        'payload' => 'array'
    ];
}
