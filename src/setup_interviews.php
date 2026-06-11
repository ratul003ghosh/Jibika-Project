<?php
include('assets/config/db.php');

$sql_interviews = "
CREATE TABLE IF NOT EXISTS interviews (
    interview_id INT AUTO_INCREMENT PRIMARY KEY,
    job_id INT NOT NULL,
    application_id INT NOT NULL,
    employer_id INT NOT NULL,
    candidate_id INT NOT NULL,
    interview_type ENUM('online', 'offline') NOT NULL,
    interview_title VARCHAR(255) NOT NULL,
    interview_datetime DATETIME NOT NULL,
    interview_location TEXT,
    meeting_link TEXT,
    notes TEXT,
    status ENUM('scheduled', 'completed', 'cancelled', 'selected', 'rejected') DEFAULT 'scheduled',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (job_id) REFERENCES jobs(job_id) ON DELETE CASCADE,
    FOREIGN KEY (application_id) REFERENCES applications(application_id) ON DELETE CASCADE,
    FOREIGN KEY (employer_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (candidate_id) REFERENCES users(user_id) ON DELETE CASCADE
);
";

if ($conn->query($sql_interviews)) {
    echo "Interviews table created successfully.\n";
} else {
    echo "Error creating interviews table: " . $conn->error . "\n";
}

// Add an index for quick lookup
$conn->query("CREATE INDEX idx_interview_employer ON interviews(employer_id)");
$conn->query("CREATE INDEX idx_interview_candidate ON interviews(candidate_id)");
$conn->query("CREATE INDEX idx_interview_application ON interviews(application_id)");

?>
