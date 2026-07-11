<?php

/*
|--------------------------------------------------------------------------
| Artevo Platform Configuration
|--------------------------------------------------------------------------
| Settings specific to Artevo that don't belong in Laravel's own config
| files. Grows across phases — e.g. auction defaults in Phase 7, external
| museum API keys in Phase 9.
*/

return [

    /*
     * Inbox that receives a notification whenever the public contact
     * form is submitted (Phase 1).
     */
    'contact_notify_email' => env('CONTACT_NOTIFY_EMAIL', env('MAIL_FROM_ADDRESS', 'hello@artevo.test')),

];
