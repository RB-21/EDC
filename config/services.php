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

    'rag' => [
        'base_url' => env('RAG_SERVICE_URL', 'http://localhost:8100'),
        'default_model' => env('RAG_DEFAULT_MODEL', 'gemini-2.5-flash'),
        'default_prompt_template' => env(
            'RAG_DEFAULT_PROMPT_TEMPLATE',
            implode("\n", [
                'Kamu adalah asisten AI yang membantu menjawab pertanyaan berdasarkan dokumen yang tersedia.',
                'Jawab pertanyaan berikut HANYA berdasarkan konteks yang diberikan.',
                'Jika jawabannya tidak ada dalam konteks, katakan "Maaf, informasi tersebut tidak ditemukan dalam dokumen yang tersedia."',
                '',
                'PENTING: Jika kamu menemukan jawabannya dari konteks, kamu WAJIB mengawali jawabanmu dengan menyebutkan identitas dokumen utama yang menjadi acuanmu dengan format persis seperti ini (Gunakan Bold):',
                '',
                '**Dokumen**',
                '**No Dokumen:** [Nomor]',
                '**Judul Dokumen:** [Judul]',
                '**Jenis Dokumen:** [Jenis]',
                '',
                'Setelah itu, WAJIB tampilkan bagian ringkasan dokumen utama dengan format:',
                '**Ringkasan Dokumen:**',
                '- Poin ringkas 1',
                '- Poin ringkas 2',
                '- Poin ringkas 3 (opsional)',
                '',
                'Lalu berikan jawabanmu di bawahnya dengan jelas dan terstruktur.',
                '',
                'KONTEKS DOKUMEN:',
                '{{CONTEXT_BLOCK}}',
                '',
                'PERTANYAAN:',
                '{{QUESTION}}',
                '',
                'JAWABAN:',
            ])
        ),
        'available_models' => array_values(array_filter(array_map(
            'trim',
            explode(',', env('RAG_AVAILABLE_MODELS', 'gemini-2.5-flash'))
        ))),
    ],

];
