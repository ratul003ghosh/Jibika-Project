<?php
session_start();
require_once('assets/config/db.php');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $lang = $_SESSION['lang'] ?? 'bn';

    if (!$email) {
        $msg = $lang == 'en' ? 'Please enter a valid email address.' : 'অনুগ্রহ করে একটি বৈধ ইমেইল ঠিকানা প্রদান করুন।';
        echo json_encode(['success' => false, 'message' => $msg]);
        exit;
    }

    try {
        // Ensure table exists
        $createTableQuery = "
            CREATE TABLE IF NOT EXISTS subscribers (
                id INT AUTO_INCREMENT PRIMARY KEY,
                email VARCHAR(255) NOT NULL UNIQUE,
                subscribed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ";
        $conn->query($createTableQuery);

        // Check if already subscribed
        $stmt = $conn->prepare("SELECT id FROM subscribers WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $msg = $lang == 'en' ? 'This email is already subscribed.' : 'এই ইমেইলটি ইতিমধ্যে সাবস্ক্রাইব করা আছে।';
            echo json_encode(['success' => false, 'message' => $msg]);
        } else {
            // Insert new subscriber
            $stmt = $conn->prepare("INSERT INTO subscribers (email) VALUES (?)");
            $stmt->bind_param("s", $email);
            if ($stmt->execute()) {
                $msg = $lang == 'en' ? 'Successfully subscribed to Jibika!' : 'জীবিকাতে সফলভাবে সাবস্ক্রাইব করেছেন!';
                echo json_encode(['success' => true, 'message' => $msg]);
            } else {
                $msg = $lang == 'en' ? 'Failed to subscribe. Please try again later.' : 'সাবস্ক্রাইব করতে ব্যর্থ হয়েছে। অনুগ্রহ করে পরে আবার চেষ্টা করুন।';
                echo json_encode(['success' => false, 'message' => $msg]);
            }
        }
    } catch (Exception $e) {
        $msg = $lang == 'en' ? 'An error occurred. Please try again.' : 'একটি ত্রুটি ঘটেছে। অনুগ্রহ করে আবার চেষ্টা করুন।';
        echo json_encode(['success' => false, 'message' => $msg]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
