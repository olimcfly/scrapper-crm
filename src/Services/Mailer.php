<?php

declare(strict_types=1);

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as MailerException;

final class Mailer
{
    public static function sendOtp(string $toEmail, string $code): bool
    {
        $config = require dirname(__DIR__, 2) . '/config/mail.php';

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = (string) $config['host'];
            $mail->SMTPAuth   = true;
            $mail->Username   = (string) $config['username'];
            $mail->Password   = (string) $config['password'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = (int) $config['port'];
            $mail->CharSet    = 'UTF-8';

            $mail->setFrom(
                (string) $config['from_address'],
                (string) $config['from_name']
            );
            $mail->addAddress($toEmail);

            $mail->isHTML(true);
            $mail->Subject = 'Votre code de connexion — CRM Coralie Montreuil';
            $mail->Body    = self::htmlBody($code);
            $mail->AltBody = "Votre code de connexion : {$code}\n\nCe code expire dans 15 minutes.\nNe le communiquez à personne.";

            $mail->send();
            return true;
        } catch (MailerException $e) {
            Logger::error('Mailer error: ' . $e->getMessage());
            return false;
        }
    }

    private static function htmlBody(string $code): string
    {
        return <<<HTML
<!DOCTYPE html>
<html lang="fr">
<head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;background:#f7f3eb;font-family:Georgia,serif;">
  <table width="100%" cellpadding="0" cellspacing="0">
    <tr>
      <td align="center" style="padding:48px 16px;">
        <table width="480" cellpadding="0" cellspacing="0" style="background:#fdfaf4;border-radius:16px;border:1px solid #ece5d9;box-shadow:0 8px 24px rgba(105,116,95,.10);">
          <tr>
            <td style="padding:36px 40px 8px;">
              <p style="margin:0;font-family:'Cormorant Garamond',Georgia,serif;font-size:1.7rem;color:#2e4036;font-weight:600;">
                Coralie Montreuil
              </p>
              <p style="margin:4px 0 0;color:#68776b;font-size:.9rem;letter-spacing:.5px;">
                Massage &amp; Bien-être
              </p>
            </td>
          </tr>
          <tr>
            <td style="padding:24px 40px 8px;">
              <p style="margin:0;color:#33483d;font-size:1rem;line-height:1.6;">
                Voici votre code de connexion à votre espace de gestion :
              </p>
            </td>
          </tr>
          <tr>
            <td style="padding:8px 40px 24px;">
              <div style="background:#f0ece3;border-radius:14px;padding:22px;text-align:center;">
                <span style="font-size:2.6rem;letter-spacing:14px;font-weight:700;color:#2e4036;font-family:monospace;">
                  {$code}
                </span>
              </div>
            </td>
          </tr>
          <tr>
            <td style="padding:0 40px 32px;">
              <p style="margin:0;color:#68776b;font-size:.88rem;line-height:1.6;">
                Ce code est valable <strong>15 minutes</strong>.<br>
                Ne le communiquez à personne.
              </p>
              <hr style="border:none;border-top:1px solid #e8e2d8;margin:24px 0 16px;">
              <p style="margin:0;color:#9aa39d;font-size:.8rem;">
                Espace réservé — Bien-être &amp; Sérénité
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
}
