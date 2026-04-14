<?php

declare(strict_types=1);

namespace App\Services;

use PHPMailer\PHPMailer\Exception as MailerException;
use PHPMailer\PHPMailer\PHPMailer;

final class Mailer
{
    public static function sendOtp(string $toEmail, string $code): bool
    {
        $config = require dirname(__DIR__, 2) . '/config/mail.php';

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = (string) ($config['host'] ?? '');
            $mail->SMTPAuth = true;
            $mail->Username = (string) ($config['username'] ?? '');
            $mail->Password = (string) ($config['password'] ?? '');
            $mail->Port = (int) ($config['port'] ?? 465);
            $mail->CharSet = 'UTF-8';

            $encryption = strtolower((string) ($config['encryption'] ?? 'ssl'));
            if ($encryption === 'tls') {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            } else {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            }

            $mail->SMTPDebug = 2;
            $mail->Debugoutput = static function ($str, $level): void {
                Logger::error('SMTP debug [' . $level . '] ' . trim((string) $str));
            };

            $fromAddress = (string) ($config['from_address'] ?? '');
            $fromName = (string) ($config['from_name'] ?? 'CRM');
            if ($fromAddress === '') {
                Logger::error('Mailer config error: from_address vide');
                return false;
            }

            $mail->setFrom($fromAddress, $fromName);
            $mail->addAddress($toEmail);

            $mail->isHTML(true);
            $mail->Subject = 'Votre code de connexion';
            $mail->Body = self::htmlBody($code);
            $mail->AltBody = self::textBody($code);

            $ok = $mail->send();

            if ($ok !== true) {
                Logger::error('Mailer send failed without exception. ErrorInfo: ' . $mail->ErrorInfo);
                return false;
            }

            Logger::error('Mailer success: OTP envoyé à ' . $toEmail);
            return true;
        } catch (MailerException $e) {
            Logger::error('Mailer exception: ' . $e->getMessage());
            Logger::error('Mailer ErrorInfo: ' . $mail->ErrorInfo);
            return false;
        }
    }

    private static function htmlBody(string $code): string
    {
        return <<<HTML
<!DOCTYPE html>
<html lang="fr">
<head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;background-color:#f4f6f8;font-family:Arial,Helvetica,sans-serif;">
  <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#f4f6f8;">
    <tr>
      <td align="center" style="padding:24px 12px;">
        <table width="100%" cellpadding="0" cellspacing="0" border="0" style="max-width:520px;background-color:#ffffff;border:1px solid #e5e7eb;border-radius:12px;">
          <tr>
            <td style="padding:28px 24px 12px 24px;text-align:left;">
              <p style="margin:0;font-size:20px;line-height:28px;color:#111827;font-weight:700;">
                Votre code de connexion
              </p>
            </td>
          </tr>
          <tr>
            <td style="padding:0 24px 8px 24px;">
              <p style="margin:0;font-size:15px;line-height:24px;color:#374151;">
                Utilisez le code ci-dessous pour finaliser votre connexion.
              </p>
            </td>
          </tr>
          <tr>
            <td align="center" style="padding:16px 24px 16px 24px;">
              <table cellpadding="0" cellspacing="0" border="0" style="width:100%;max-width:340px;background-color:#f9fafb;border:1px solid #e5e7eb;border-radius:10px;">
                <tr>
                  <td align="center" style="padding:16px 8px;font-size:34px;line-height:40px;letter-spacing:10px;color:#111827;font-weight:700;font-family:'Courier New',Courier,monospace;">
                  {$code}
                  </td>
                </tr>
              </table>
            </td>
          </tr>
          <tr>
            <td style="padding:0 24px 12px 24px;">
              <p style="margin:0;font-size:14px;line-height:22px;color:#4b5563;">
                Ce code est temporaire et expire dans <strong>15 minutes</strong>.
              </p>
            </td>
          </tr>
          <tr>
            <td style="padding:0 24px 28px 24px;">
              <p style="margin:0;font-size:13px;line-height:20px;color:#6b7280;">
                Si vous n’êtes pas à l’origine de cette demande, ignorez simplement cet email.
              </p>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>
HTML;
    }

    private static function textBody(string $code): string
    {
        return "Votre code de connexion\n\n"
            . "Code OTP : {$code}\n\n"
            . "Ce code est temporaire et expire dans 15 minutes.\n"
            . "Si vous n’êtes pas à l’origine de cette demande, ignorez simplement cet email.";
    }
}
