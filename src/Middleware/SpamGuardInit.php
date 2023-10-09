<?php

namespace InigoPascall\SpamGuard\Middleware;

use App\Exceptions\SpamGuardUnauthorized;
use InigoPascall\SpamGuard\Models\FormSubmission;
use Carbon\Carbon;
use Closure;
use Illuminate\Support\Facades\Log;

class SpamGuardInit
{
    /**
     *
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(config('spamguard.save_spam_submissions'))
        {
            $previously_banned = FormSubmission::where('banned', true)->where('ip_address', $request->ip())->first();

            if($previously_banned)
            {
                $previously_banned->increment('visits');
                throw new SpamGuardUnauthorized('Unauthorized');
            }
        }

        if($request->method() === 'GET'){
            session()->put('page_loaded_at', Carbon::now()->timestamp);
        }

        return $next($request);
    }
}
