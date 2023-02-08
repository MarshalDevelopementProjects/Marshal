<?php

require __DIR__ . '/../../vendor/autoload.php';

use Core\Mailer;
use Dotenv\Dotenv;

/**
 * @throws \PHPMailer\PHPMailer\Exception
 */
function testMailerSendEMail(): void
{
    $dotenv = Dotenv::createUnsafeImmutable(__DIR__ . '/../../');
    $dotenv->load();

    $mailer = new Mailer();
    $mailer->sendEMail(
        [$_ENV["USER_EMAIL_ADDRESS"]], // ["Your email address or the testers email address"], // or add your email to the environmental variables and use that as [$_ENV["USER_EMAIL_ADDRESS"]]
        "Testing the new Mailer class",
        "This is a test for the newly written mailer class\n"
    );
}

try {
    testMailerSendEMail();
} catch (\PHPMailer\PHPMailer\Exception $e) {
}
