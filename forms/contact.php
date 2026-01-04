<?php
// SinaFast Portfolio - Contact Form Handler
// Last Updated: January 3, 2026

// Configuration
$recipient_emails = array(
    "ezeonyekasinachi@gmail.com",
    "ezeonyeksinachifranklin@gmail.com"
);
$website_name = "SinaFast - Sinachi Franklin Ezeonyeka";
$from_address = "noreply@yourdomain.com"; // Change to your domain

// Enable error reporting (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', dirname(__FILE__) . '/error.log');

// Security settings
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

// Function to sanitize input
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

// Function to validate email
function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Get and sanitize form data
    $name = isset($_POST['name']) ? sanitize_input($_POST['name']) : '';
    $email = isset($_POST['email']) ? sanitize_input($_POST['email']) : '';
    $subject = isset($_POST['subject']) ? sanitize_input($_POST['subject']) : '';
    $message = isset($_POST['message']) ? sanitize_input($_POST['message']) : '';
    
    // Validation
    $errors = array();
    
    if (empty($name)) {
        $errors[] = "Name is required";
    } elseif (strlen($name) < 2) {
        $errors[] = "Name must be at least 2 characters";
    }
    
    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!validate_email($email)) {
        $errors[] = "Invalid email format";
    }
    
    if (empty($subject)) {
        $errors[] = "Subject is required";
    } elseif (strlen($subject) < 3) {
        $errors[] = "Subject must be at least 3 characters";
    }
    
    if (empty($message)) {
        $errors[] = "Message is required";
    } elseif (strlen($message) < 10) {
        $errors[] = "Message must be at least 10 characters";
    }
    
    // Check for spam (optional - implement rate limiting)
    // You can add IP-based rate limiting here
    
    if (count($errors) > 0) {
        // Return validation errors
        http_response_code(400);
        echo json_encode(array(
            "status" => "error",
            "message" => "Validation failed",
            "errors" => $errors
        ));
        exit;
    }
    
    // Prepare email content
    $email_subject = "[" . $website_name . "] New Contact Form Submission: " . $subject;
    
    $email_body = "
    <html>
    <body style='font-family: Arial, sans-serif; color: #333;'>
        <div style='background-color: #f8f9fa; padding: 20px; border-radius: 8px;'>
            <h2 style='color: #149ddd;'>New Contact Form Message</h2>
            <hr style='border: 1px solid #e0e0e0;'>
            
            <p><strong>From:</strong> " . htmlspecialchars($name) . "</p>
            <p><strong>Email:</strong> <a href='mailto:" . htmlspecialchars($email) . "'>" . htmlspecialchars($email) . "</a></p>
            <p><strong>Subject:</strong> " . htmlspecialchars($subject) . "</p>
            
            <hr style='border: 1px solid #e0e0e0;'>
            
            <h3 style='color: #149ddd;'>Message:</h3>
            <p style='white-space: pre-wrap; word-wrap: break-word;'>" . nl2br(htmlspecialchars($message)) . "</p>
            
            <hr style='border: 1px solid #e0e0e0;'>
            <p style='color: #999; font-size: 12px;'>
                Submitted on: " . date('F d, Y \a\t H:i:s') . "<br>
                IP Address: " . $_SERVER['REMOTE_ADDR'] . "
            </p>
        </div>
    </body>
    </html>";
    
    // Prepare headers
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8\r\n";
    $headers .= "From: " . $from_address . "\r\n";
    $headers .= "Reply-To: " . htmlspecialchars($email) . "\r\n";
    $headers .= "X-Priority: 3\r\n";
    
        // Sending strategy: use SendGrid API if SENDGRID_API_KEY is provided, otherwise fallback to mail()
        $sendgrid_api_key = getenv('SENDGRID_API_KEY') ?: null;

        function send_via_sendgrid($api_key, $to, $subject, $htmlContent, $from_address, $reply_to = null) {
            $payload = array(
                'personalizations' => array(array('to' => array(array('email' => $to)))),
                'from' => array('email' => $from_address),
                'subject' => $subject,
                'content' => array(array('type' => 'text/html', 'value' => $htmlContent))
            );
            if ($reply_to) {
                $payload['reply_to'] = array('email' => $reply_to);
            }

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://api.sendgrid.com/v3/mail/send');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Authorization: Bearer ' . $api_key,
                'Content-Type: application/json'
            ));
            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            return ($http_code >= 200 && $http_code < 300);
        }

        // If PHPMailer is available and SMTP env vars are set, prefer SMTP via PHPMailer
        $phpmailer_autoload = __DIR__ . '/../vendor/phpmailer/phpmailer/src/PHPMailer.php';
        $smtp_host = getenv('SMTP_HOST') ?: null;
        $smtp_user = getenv('SMTP_USER') ?: null;
        $smtp_pass = getenv('SMTP_PASS') ?: null;
        $smtp_port = getenv('SMTP_PORT') ?: null;
        $smtp_secure = getenv('SMTP_SECURE') ?: null; // 'tls' or 'ssl'

        $mail_sent = false;

        if (class_exists('PHPMailer\\PHPMailer\\PHPMailer') || file_exists($phpmailer_autoload)) {
            // Try to load composer autoload if present
            if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
                require_once __DIR__ . '/../vendor/autoload.php';
            } else {
                // Attempt to include PHPMailer directly if installed in vendor/phpmailer/phpmailer/src
                if (file_exists($phpmailer_autoload)) {
                    require_once __DIR__ . '/../vendor/phpmailer/phpmailer/src/Exception.php';
                    require_once __DIR__ . '/../vendor/phpmailer/phpmailer/src/PHPMailer.php';
                    require_once __DIR__ . '/../vendor/phpmailer/phpmailer/src/SMTP.php';
                }
            }

            if (class_exists('PHPMailer\\PHPMailer\\PHPMailer')) {
                try {
                    $mail = new PHPMailer\\PHPMailer\\PHPMailer(true);
                    if ($smtp_host && $smtp_user && $smtp_pass) {
                        $mail->isSMTP();
                        $mail->Host = $smtp_host;
                        $mail->SMTPAuth = true;
                        $mail->Username = $smtp_user;
                        $mail->Password = $smtp_pass;
                        $mail->SMTPSecure = $smtp_secure ?: 'tls';
                        $mail->Port = $smtp_port ? (int)$smtp_port : 587;
                    }

                    // Send to each recipient email
                    foreach ($recipient_emails as $recipient) {
                        $mail->clearAddresses();
                        $mail->setFrom($from_address, $website_name);
                        $mail->addAddress($recipient);
                        $mail->addReplyTo($email, $name);
                        $mail->isHTML(true);
                        $mail->Subject = $email_subject;
                        $mail->Body = $email_body;
                        $mail->AltBody = strip_tags($message);

                        $mail_sent = $mail->send();
                        if ($mail_sent) break;
                    }
                } catch (Exception $e) {
                    $mail_sent = false;
                }
            }
        }

        // If PHPMailer wasn't used/successful, try SendGrid if API key present
        if (!$mail_sent && $sendgrid_api_key) {
        foreach ($recipient_emails as $recipient) {
            $mail_sent = send_via_sendgrid($sendgrid_api_key, $recipient, $email_subject, $email_body, $from_address, htmlspecialchars($email));
            if ($mail_sent) break;
        }
// Final fallback to PHP mail() - send to all recipient emails
    if (!$mail_sent) {
        foreach ($recipient_emails as $recipient) {
            $mail_sent = mail($recipient, $email_subject, $email_body, $headers);
            if ($mail_sent) break;
        }
        }
    
    if ($mail_sent) {
        // Send confirmation email to user (optional)
        $confirmation_subject = "We received your message - " . $website_name;
        $confirmation_body = "
        <html>
        <body style='font-family: Arial, sans-serif; color: #333;'>
            <div style='background-color: #f8f9fa; padding: 20px; border-radius: 8px;'>
                <h2 style='color: #149ddd;'>Thank You for Contacting Us!</h2>
                <p>Hi " . htmlspecialchars($name) . ",</p>
                <p>Thank you for reaching out to " . $website_name . ". I have received your message and will get back to you as soon as possible.</p>
                
                <h3 style='color: #149ddd;'>Your Message Details:</h3>
                <p><strong>Subject:</strong> " . htmlspecialchars($subject) . "</p>
                <p><strong>Message:</strong></p>
                <p style='white-space: pre-wrap; word-wrap: break-word;'>" . nl2br(htmlspecialchars($message)) . "</p>
                
                <hr style='border: 1px solid #e0e0e0;'>
                <p>Best regards,<br><strong>Sinachi Franklin Ezeonyeka</strong><br>Front-End Developer & Web Designer</p>
            </div>
        </body>
        </html>";
        
        $confirmation_headers = "MIME-Version: 1.0\r\n";
        $confirmation_headers .= "Content-type: text/html; charset=UTF-8\r\n";
        $confirmation_headers .= "From: " . $from_address . "\r\n";
        
        mail($email, $confirmation_subject, $confirmation_body, $confirmation_headers);
        
        // Return success response
        http_response_code(200);
        echo json_encode(array(
            "status" => "success",
            "message" => "Thank you for submitting, we will get back to you shortly."
        ));
    } else {
        // Return error response
        http_response_code(500);
        echo json_encode(array(
            "status" => "error",
            "message" => "There was an error sending your message. Please try again later."
        ));
    }
    
} else {
    // Not a POST request
    http_response_code(405);
    echo json_encode(array(
        "status" => "error",
        "message" => "Invalid request method"
    ));
}
?>
