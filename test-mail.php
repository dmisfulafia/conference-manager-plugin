<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Mail;

try {
    echo "Sending a real Laravel transactional verification email...\n";
    Mail::raw("If you receive this, the FULafia Google Apps Script Mailer is 100% working!", function ($message) {
        $message->to('tasiukwaplong@gmail.com')
                ->subject('FULafia Mailer - Verification Test Success');
    });
    echo "Mail dispatch processed. Check Laravel logs / target inbox.\n";
} catch (\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}
