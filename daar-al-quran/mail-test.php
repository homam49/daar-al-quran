<?php
// Load the Laravel application
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Import Mail facade
use Illuminate\Support\Facades\Mail;

// Send a test email
echo "Sending test email...\n";
try {
    Mail::raw('This is a test email from Daar Al Quran application to verify email configuration.', function($message) {
        $message->to(config('mail.from.address'))
                ->subject('Daar Al Quran - Email Configuration Test');
    });
    echo "Test email sent successfully! Check your inbox.\n";
} catch (Exception $e) {
    echo "Error sending email: " . $e->getMessage() . "\n";
} 