<?php

namespace InigoPascall\SpamGuard\Models;

use Illuminate\Database\Eloquent\Model;

class SpamGuardBlacklist extends Model {

    protected $table = 'spamguard_blacklist';

    protected $fillable = [
        'text'
    ];

    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;
}
