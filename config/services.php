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
        'default_prompt_rules' => env(
            'RAG_DEFAULT_PROMPT_RULES',
            implode("\n", [
                '[ATURAN WAJIB SISTEM]',
                '1) Tampilkan dokumen utama (similarity tertinggi) sebagai acuan utama jawaban.',
                '2) Jika ada dokumen lain yang tetap relevan kuat terhadap pertanyaan, boleh disebutkan juga.',
                '3) Setelah blok identitas dokumen, tampilkan "Ringkasan Dokumen" minimal 5 poin ringkas.',
                '4) Ringkasan harus berisi detail penting dan konkret dari dokumen (misal nomor, tanggal, ketentuan, daftar poin utama).',
                '5) Jangan singkat berlebihan. Jika konteks cukup, berikan ringkasan yang komprehensif.',
                '6) Di akhir jawaban, WAJIB tampilkan blok saran pertanyaan lanjutan persis dengan format:',
                '[FOLLOW_UP_QUESTIONS]',
                '1. ...',
                '2. ...',
                '3. ...',
                '[/FOLLOW_UP_QUESTIONS]',
            ])
        ),
        'available_models' => array_values(array_filter(array_map(
            'trim',
            explode(',', env('RAG_AVAILABLE_MODELS', 'gemini-2.5-flash'))
        ))),
    ],

    'wablas' => [
        'token' => env('WABLAS_TOKEN', 'KXCwBNP19Q3L5O7AlNR3IXGMlZnYjUyCZRkg1uH916uRpIwKaXlNCXc2QvoeeuzH'),
        'url' => env('WABLAS_URL', 'https://pati.wablas.com/api/v2/send-template'),
    ],

];
