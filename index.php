<?php
require 'smtp/PHPMailerAutoload.php';

// Fetch the content of the configuration file from GitHub
$config_content = @file_get_contents('https://raw.githubusercontent.com/akshat148/akshat148/main/config2.php');

if (!$config_content) {
    die('Failed to fetch configuration file.');
}

// Extract email limit and expiration date using regex
preg_match('/define\(\'SERVER_CAP\', (.*?)\);/', $config_content, $matches);
$server_cap = $matches[1] ?? 0;

preg_match('/define\(\'EXPIRATION_DATE\', strtotime\(\'(.*?)\'\)\);/', $config_content, $matches);
$expiration_date = $matches[1] ?? '';

// Send the email
echo smtp_mailer('akshatdwivedi1408@gmail.com', 'Test Subject', 'Test Message', $server_cap, $expiration_date);

function smtp_mailer($to, $subject, $msg, $server_cap, $expiration_date) {
    // Validate inputs
    if (!$server_cap || !$expiration_date) {
        return "Configuration missing. Please verify the configuration file.";
    }

    // Check if the email limit has been reached
       // Fetch the current email count
       $current_email_count = getEmailCount();

       // Check if the email limit has been reached
       if ($current_email_count >= $server_cap) {
           return "Email limit exceeded. No further emails will be sent. 
           <br>Email Count: $current_email_count 
           <br>Server Cap: $server_cap 
           <br>Expiration Date: " . date('Y-m-d', strtotime($expiration_date));
       }
       
       // Check if the expiration date has been reached
       if (time() > strtotime($expiration_date)) {
           return "Email sending service has expired. Please contact support for renewal. 
           <br>Email Count: $current_email_count 
           <br>Server Cap: $server_cap 
           <br>Expiration Date: " . date('Y-m-d', strtotime($expiration_date));
       }
   

    // Configure the PHPMailer
    $mail = new PHPMailer(); 
    $mail->isSMTP(); 
    $mail->SMTPAuth = true; 
    $mail->SMTPSecure = 'tls'; 
    $mail->Host = 'smtp.gmail.com'; // Correct Gmail SMTP server
    $mail->Port = 587; 
    $mail->Username = 'official1481999@gmail.com';
    $mail->Password = 'iozx tioy hqqo acli'; // Replace with an App Password
    $mail->SetFrom('official1481999@gmail.com', 'Your Name'); // Add a name for better sender identification
    $mail->Subject = $subject;
    $mail->Body = $msg;
    $mail->AddAddress($to);
    $mail->SMTPOptions = [
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => false,
        ],
    ];

    // Send email
    if (!$mail->Send()) {
        return 'Error: ' . $mail->ErrorInfo;
    } else {
        incrementEmailCount(); // Increment email count only on successful send
        return 'Email sent successfully.';
    }
}

// Function to retrieve the current email count
function getEmailCount() {
    $hashedFilename = 'smtp/' . hash('sha256', 'email_count.txt'); // Hashed filename for security
    if (file_exists($hashedFilename)) {
        $count = intval(file_get_contents($hashedFilename));
        return $count;
    } else {
        // Create the file and write the initial count
        if (file_put_contents($hashedFilename, 0) === false) {
            die('Failed to initialize email count file.');
        }
        return 0;
    }
}

// Function to increment the email count
function incrementEmailCount() {
    $hashedFilename = 'smtp/' . hash('sha256', 'email_count.txt'); // Hashed filename
    $count = getEmailCount() + 1;
    if (file_put_contents($hashedFilename, $count) === false) {
        die('Failed to update email count.');
    }
}
?>
