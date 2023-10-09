<?php

namespace InigoPascall\SpamGuard\Middleware;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use InigoPascall\SpamGuard\Models\FormSubmission;
use Carbon\Carbon;
use Closure;
use Illuminate\Support\Str;

class SpamGuardIntercept
{
    private $reasons2ban = [];
    private $secs2dispatch;
    private $payload;
    private $cover;

    /**
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // everything except the csrf token
        $this->payload = Arr::except(request()->input(), ['_token']);

        // use the truncated part of the configured field to form the 'quick view' cover for the message
        $this->cover = array_key_exists(config('spamguard.cover_field'), request()->input()) ? Str::limit(request()->input()[config('spamguard.cover_field')] ?? '', 40) : '';

        if($request->method() === 'POST')
        {
            if($then = session()->get('page_loaded_at'))
            {
                $this->secs2dispatch = Carbon::now()->timestamp - $then;

                if($this->secs2dispatch < config('spamguard.minimum_allowed_dispatch'))
                {
                    $this->addReason('Form was submitted too quickly');
                }

            // if session is somehow not set... yeah ban that
            }else {
                $this->addReason('Page load session was not set properly');
            }

            // is the honeytrap field filled in (if it's even part of the request payload then yes it is)
            if(config('spamguard.honeytrap_field') && array_key_exists(config('spamguard.honeytrap_field'), $this->payload) && $this->payload[config('spamguard.honeytrap_field')])
            {
                $this->addReason('Honeytrap field filled in');
            };

            foreach($this->payload as $key => $value)
            {
                // is this field one which we should scan for blacklist text?
                if(config('spamguard.scanned_fields') === '*' || in_array($key, config('spamguard.scanned_fields')))
                {
                    // does it contain HTML? and are we scanning for it?
                    if(!config('spamguard.allow_html'))
                    {
                        // firstly, does it contain an obvious link (most common, and quicker to search for). then try the regex
                        if(Str::contains($this->payload[$key], '<a href=') || preg_match('/<\w+(?:\s+[^>]+)?>/', $value) === 1){
                            $this->addReason("Field \"$key\" contains HTML");
                        }
                    }

                    // does it contain blacklisted text?
                    foreach(config('spamguard.blacklist') as $snippet)
                    {
                        if(Str::contains($value, $snippet))
                        {
                            $this->addReason("Field \"$key\" contains blacklisted text: \"$snippet\"");
                        }
                    }
                }
            }

            if($this->banHammer()){
                return redirect()->back();
            }

        }
        return $next($request);
    }

    private function addReason($reason)
    {
        $this->reasons2ban[] = $reason;

        if(config('spamguard.fail_on_first_transgression')){
            $this->banHammer();
        }
    }

    /**
     *
     * Ban them if appropriate, save to db if settings allow, and 403 if banned!
     *
     */
    private function banHammer()
    {
        $banned = !empty($this->reasons2ban);

        if(
            (config('spamguard.save_spam_submissions') && $banned) ||
            (config('spamguard.save_legit_submissions') && !$banned)
        ) {

            FormSubmission::create([
                'ip_address' => request()->ip(),
                'user_agent' => request()->server('HTTP_USER_AGENT'),
                'banned' => $banned,
                'banned_reason' => $this->reasons2ban,
                'cover' => $this->cover,
                'payload' => $this->payload,
                'dispatch_seconds' => $this->secs2dispatch,
                'visits' => $banned ? 0 : 1 // if banned, subsequent redirection will detect them as a spammer and increment the visits count therefore need to start from zero.
            ]);
        }

        return $banned;
    }

    public function terminate()
    {
        if(request()->method() === 'POST'){
            session()->forget('page_loaded_at');
        }
    }
}
