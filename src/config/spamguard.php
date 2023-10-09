<?php

return [

    // minimum allowed time in seconds between page load > form submission. Any submission occurring quicker than this will result in a ban.
    'minimum_allowed_dispatch' => 6,

    // List of red-flag text snippets to scan for. Set use_db to `true` to load this array from the db instead
    'load_blacklist_from_db' => true,

    // save form submissions to the database?
    'save_spam_submissions' => true, // need this if you want to ban IPs
    'save_legit_submissions' => true,

    // input names that will be scanned for banned text containing any word or phrase in the blacklist array, as well as html, or any other relevant option that is switched on here.
    'scanned_fields' => [
        'name',
        'message'
    ],

    'honeytrap_field' => 'definitelynotahoneytrap',

    // if saving submissions to the db, a truncated version of whatever is submitted in this field will be saved in its own column (e.g. for displaying in a CMS so you can quickly scan through a list of submitted spammy messages)
    'cover_field' => 'message',

    // continue checking for other transgressions if one already found
    'fail_on_first_transgression' => false,

    // allow html in any input or text field
    'allow_html' => false,

    // text snippet blacklist. Any scanned field containing any of these snippets will result in a ban. Overriden if `load_blacklist_from_db` set to true
    'blacklist' => [],

];
