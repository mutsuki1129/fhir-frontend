<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'fhir' => [
        'phase1_enabled' => env('FHIR_PHASE1_ENABLED', true),
        'base_url' => env('FHIR_BASE_URL', 'http://localhost:8091/fhir'),
        'timeout' => (int) env('FHIR_TIMEOUT_SECONDS', 10),
        'identifier_system' => env('FHIR_PATIENT_IDENTIFIER_SYSTEM', 'urn:app:patient'),
    ],

];
