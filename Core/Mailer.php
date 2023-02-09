<?php

namespace Core;

require __DIR__ . "/../vendor/autoload.php";

use Core\Validator\Validator;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use Dotenv\Dotenv;

class Mailer
{
    private PHPMailer $mailer;
    private Validator $validator;
    private Dotenv $dotenv;

    public function __construct()
    {
        $this->dotenv = Dotenv::createImmutable(__DIR__ . '/../');
        $this->dotenv->load();
        $this->mailer = new PHPMailer(true);
        try {
            $this->mailer->isSMTP();
            $this->mailer->Host       = $_ENV["SMTP_HOST"];
            $this->mailer->SMTPAuth   = true;
            $this->mailer->Username   = $_ENV["SMTP_USERNAME"];
            $this->mailer->Password   = $_ENV["SMTP_PASSWORD"];
            $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $this->mailer->Port       = 587;

            $this->validator = new Validator();
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function sendEMail(
        array $recipients, // email of the recipient
        string $subject, // subject of the email
        string $body, // body of the email(in plain text)
        array $ccs = array(), // cc to
        array $bccs = array() // bcc to
    ): bool
    {
        if (!empty($recipients)) {
            if ($subject !== "") {
                if ($body !== "") {
                    $this->validateUserEmails($recipients, $ccs, $bccs);
                    $this->mailer->Subject = $subject;
                    $this->mailer->Body = $body;
                    return $this->mailer->send();
                } else {
                    throw new \Exception("Body of the email is needed for the email to be sent");
                }
            } else {
                throw new \Exception("Subject is needed for the email to be sent");
            }
        } else {
            throw new \Exception("Need at least one recipient to send an email");
        }
    }

    /**
     * use this to send html email
     * @throws Exception
     * @throws \Exception
     */
    public function sendHTMLEmail(
        array $recipients, // email of the recipient
        string $subject, // subject of the email
        string $body, // body of the email(in plain text)
        array $ccs = array(), // cc to
        array $bccs = array() // bcc to
    ): bool
    {
        if (!empty($recipients)) {
            if ($subject !== "") {
                if ($body !== "") {
                    $this->validateUserEmails($recipients, $ccs, $bccs);
                    $this->mailer->isHTML(true);
                    $this->mailer->Subject = $subject;
                    $this->mailer->Body = $body;
                    return $this->mailer->send();
                } else {
                    throw new \Exception("Body of the email is needed for the email to be sent");
                }
            } else {
                throw new \Exception("Subject is needed for the email to be sent");
            }
        } else {
            throw new \Exception("Need at least one recipient to send an email");
        }
    }

    /**
     * @param array $recipients
     * @param array $ccs
     * @param array $bccs
     * @return void
     * @throws Exception
     * @throws \Exception
     */
    private function validateUserEmails(array $recipients, array $ccs, array $bccs): void
    {
        foreach ($recipients as $recipient) {
            $this->validator->validate(["email_address" => $recipient], "email_validation");
            if ($this->validator->getPassed()) $this->mailer->addAddress($recipient);
            else throw new \Exception("This $recipient is not a valid email format");
        }
        if (!empty($cc)) {
            foreach ($ccs as $cc) {
                $this->validator->validate(["email_address" => $cc], "email_validation");
                if ($this->validator->getPassed()) $this->mailer->addCC($cc);
                else throw new \Exception("This $cc is not a valid email format");
            }
        }
        if (!empty($bccs)) {
            foreach ($bccs as $bcc) {
                $this->validator->validate(["email_address" => $bcc], "email_validation");
                if ($this->validator->getPassed()) $this->mailer->addCC($bcc);
                else throw new \Exception("This $bcc is not a valid email format");
            }
        }
        $this->mailer->setFrom('marshalprojectmanagementco@gmail.com', 'MarshalTeam', true);
    }
}
