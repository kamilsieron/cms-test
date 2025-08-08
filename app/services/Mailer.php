<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../../vendor/autoload.php';

class Mailer
{
	
	public static function sendSetPasswordMail(string $toEmail, string $username, string $resetUrl, ?string &$err = null): bool
	{
		$mail = new PHPMailer(true);
		$debug = '';
	
		try {
			$mail->isSMTP();
			$mail->Host       = $_ENV['SMTP_HOST'] ?? '';
			$mail->SMTPAuth   = true;
			$mail->Username   = $_ENV['SMTP_USER'] ?? '';
			$mail->Password   = $_ENV['SMTP_PASS'] ?? '';
			$mail->Port       = (int)($_ENV['SMTP_PORT'] ?? 587);
			$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // dla 587; dla 465 -> ENCRYPTION_SMTPS
	
			// UTF-8
			$mail->CharSet  = 'UTF-8';
			$mail->Encoding = 'base64';
	
			// DEBUG -> do zmiennej
			$mail->SMTPDebug   = 3; // 2-3 zwykle wystarcza; 4 = bardzo gadatliwe
			$mail->Debugoutput = static function ($str, $level) use (&$debug) {
				$debug .= "[$level] $str\n";
			};
	
			$mail->setFrom($_ENV['SMTP_FROM'] ?? '', $_ENV['SMTP_FROM_NAME'] ?? '');
			$mail->addAddress($toEmail);
			$mail->Subject = 'Ustaw hasło do konta w CMS';
			$mail->isHTML(true);
			$mail->Body    = self::htmlTemplateForSetPassword($username, $resetUrl);
			$mail->AltBody = "Witaj {$username}!\nUstaw hasło: {$resetUrl}\nLink ważny 24h.";
	
			$ok = $mail->send();
			if (!$ok) {
				$err = ($mail->ErrorInfo ?: '') . "\n\n=== SMTP DEBUG ===\n" . $debug;
			}
			return $ok;
		} catch (Exception $e) {
			$err = ($mail->ErrorInfo ?: $e->getMessage()) . "\n\n=== SMTP DEBUG ===\n" . $debug;
			return false;
		}
	}
	
	public static function htmlTemplateForSetPassword(string $username, string $resetUrl): string
	{
		return <<<HTML
	<!DOCTYPE html>
	<html lang="pl">
	<head>
		<meta charset="UTF-8">
		<title>Ustaw hasło do konta</title>
		<style>
			body {
				font-family: Arial, sans-serif;
				background: #f9f9f9;
				color: #333;
				padding: 20px;
			}
			.container {
				background: #fff;
				border: 1px solid #ddd;
				padding: 30px;
				max-width: 600px;
				margin: auto;
				border-radius: 8px;
			}
			h1 {
				color: #007bff;
			}
			a.button {
				display: inline-block;
				padding: 12px 24px;
				margin-top: 20px;
				background: #007bff;
				color: white;
				text-decoration: none;
				border-radius: 5px;
			}
			p.signature {
				font-size: 13px;
				color: #666;
				margin-top: 40px;
			}
		</style>
	</head>
	<body>
		<div class="container">
			<h1>Cześć {$username}!</h1>
			<p>Twoje konto zostało właśnie utworzone w naszym systemie CMS.</p>
			<p>Aby ustawić swoje hasło, kliknij w przycisk poniżej:</p>
			<p><a href="{$resetUrl}" class="button">Ustaw hasło</a></p>
			<p>Link będzie aktywny przez 24 godziny.</p>
			<p class="signature">– Zespół CMS</p>
		</div>
	</body>
	</html>
	HTML;
	}
	
	
}
