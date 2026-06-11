<?php
include('assets/config/db.php');

$conn->query("SET FOREIGN_KEY_CHECKS=0");

// 1. Skills Normalization
$conn->query("CREATE TABLE IF NOT EXISTS dic_skills (
    skill_id INT AUTO_INCREMENT PRIMARY KEY,
    skill_name VARCHAR(100) UNIQUE NOT NULL
)");

$conn->query("CREATE TABLE IF NOT EXISTS job_seeker_skills (
    user_id INT NOT NULL,
    skill_id INT NOT NULL,
    proficiency ENUM('Beginner', 'Intermediate', 'Expert') DEFAULT 'Intermediate',
    PRIMARY KEY (user_id, skill_id),
    FOREIGN KEY (user_id) REFERENCES job_seeker_profiles(user_id) ON DELETE CASCADE,
    FOREIGN KEY (skill_id) REFERENCES dic_skills(skill_id) ON DELETE CASCADE
)");

$conn->query("CREATE TABLE IF NOT EXISTS job_required_skills (
    job_id INT NOT NULL,
    skill_id INT NOT NULL,
    PRIMARY KEY (job_id, skill_id),
    FOREIGN KEY (job_id) REFERENCES jobs(job_id) ON DELETE CASCADE,
    FOREIGN KEY (skill_id) REFERENCES dic_skills(skill_id) ON DELETE CASCADE
)");

// Migrate Skills
$res = $conn->query("SELECT user_id, skills FROM job_seeker_profiles WHERE skills IS NOT NULL AND skills != ''");
if ($res) {
    while($row = $res->fetch_assoc()) {
        $skills_arr = explode(',', $row['skills']);
        foreach($skills_arr as $sk) {
            $sk = trim($sk);
            if(empty($sk)) continue;
            // Insert skill into dic_skills if not exists
            $sk_escaped = $conn->real_escape_string($sk);
            $conn->query("INSERT IGNORE INTO dic_skills (skill_name) VALUES ('$sk_escaped')");
            $sk_id_res = $conn->query("SELECT skill_id FROM dic_skills WHERE skill_name = '$sk_escaped'");
            if($sk_id_res && $sk_id_res->num_rows > 0) {
                $sk_id = $sk_id_res->fetch_assoc()['skill_id'];
                $conn->query("INSERT IGNORE INTO job_seeker_skills (user_id, skill_id) VALUES ({$row['user_id']}, $sk_id)");
            }
        }
    }
}

// 2. Education Normalization
$conn->query("CREATE TABLE IF NOT EXISTS dic_education_levels (
    level_id INT AUTO_INCREMENT PRIMARY KEY,
    level_name VARCHAR(50) UNIQUE NOT NULL
)");

$conn->query("CREATE TABLE IF NOT EXISTS dic_institutions (
    institution_id INT AUTO_INCREMENT PRIMARY KEY,
    institution_name VARCHAR(200) UNIQUE NOT NULL
)");

$conn->query("CREATE TABLE IF NOT EXISTS seeker_education (
    education_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    level_id INT NOT NULL,
    institution_id INT,
    passing_year YEAR,
    result VARCHAR(20),
    FOREIGN KEY (user_id) REFERENCES job_seeker_profiles(user_id) ON DELETE CASCADE,
    FOREIGN KEY (level_id) REFERENCES dic_education_levels(level_id) ON DELETE CASCADE,
    FOREIGN KEY (institution_id) REFERENCES dic_institutions(institution_id) ON DELETE SET NULL
)");

$conn->query("INSERT IGNORE INTO dic_education_levels (level_name) VALUES ('HSC'), ('Diploma'), ('Bachelor'), ('Masters'), ('PhD')");

// Migrate Education
$res = $conn->query("SELECT user_id, education FROM job_seeker_profiles WHERE education IS NOT NULL AND education != ''");
if ($res) {
    while($row = $res->fetch_assoc()) {
        $edu = trim($row['education']);
        $level_id = 3; // Default Bachelor
        $lres = $conn->query("SELECT level_id FROM dic_education_levels WHERE level_name = '{$conn->real_escape_string($edu)}'");
        if ($lres && $lres->num_rows > 0) {
            $level_id = $lres->fetch_assoc()['level_id'];
        }
        $conn->query("INSERT IGNORE INTO seeker_education (user_id, level_id) VALUES ({$row['user_id']}, $level_id)");
    }
}

// Drop old columns
$conn->query("ALTER TABLE job_seeker_profiles DROP COLUMN skills");
$conn->query("ALTER TABLE job_seeker_profiles DROP COLUMN education");

// 3. Application State History
$conn->query("CREATE TABLE IF NOT EXISTS application_status_history (
    history_id BIGINT AUTO_INCREMENT PRIMARY KEY,
    application_id INT NOT NULL,
    actor_user_id INT NULL,
    old_status VARCHAR(50),
    new_status VARCHAR(50) NOT NULL,
    changed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (application_id) REFERENCES applications(application_id) ON DELETE CASCADE,
    FOREIGN KEY (actor_user_id) REFERENCES users(user_id) ON DELETE SET NULL
)");

$conn->query("DROP TRIGGER IF EXISTS trg_applications_after_update");
$conn->query("CREATE TRIGGER trg_applications_after_update
AFTER UPDATE ON applications
FOR EACH ROW
BEGIN
    IF OLD.status != NEW.status THEN
        INSERT INTO application_status_history (application_id, old_status, new_status)
        VALUES (NEW.application_id, OLD.status, NEW.status);
    END IF;
END");

// 4. Interview State Machine
$conn->query("CREATE TABLE IF NOT EXISTS interview_status_history (
    history_id BIGINT AUTO_INCREMENT PRIMARY KEY,
    interview_id INT NOT NULL,
    old_status VARCHAR(50),
    new_status VARCHAR(50) NOT NULL,
    changed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (interview_id) REFERENCES interviews(interview_id) ON DELETE CASCADE
)");

$conn->query("DROP TRIGGER IF EXISTS trg_interviews_after_update");
$conn->query("CREATE TRIGGER trg_interviews_after_update
AFTER UPDATE ON interviews
FOR EACH ROW
BEGIN
    IF OLD.status != NEW.status THEN
        INSERT INTO interview_status_history (interview_id, old_status, new_status)
        VALUES (NEW.interview_id, OLD.status, NEW.status);
    END IF;
END");

// 6 & 7. Analytics Views
$conn->query("CREATE OR REPLACE VIEW vw_employer_dashboard AS
SELECT 
    j.employer_id,
    COUNT(DISTINCT j.job_id) as active_jobs,
    COUNT(a.application_id) as total_applications,
    SUM(CASE WHEN a.status = 'Pending' THEN 1 ELSE 0 END) as pending_review
FROM jobs j
LEFT JOIN applications a ON j.job_id = a.job_id
WHERE j.status = 'active'
GROUP BY j.employer_id");

$conn->query("CREATE OR REPLACE VIEW vw_demanded_skills AS
SELECT 
    ds.skill_name,
    COUNT(jrs.job_id) as current_demand_count
FROM dic_skills ds
JOIN job_required_skills jrs ON ds.skill_id = jrs.skill_id
JOIN jobs j ON jrs.job_id = j.job_id
WHERE j.status = 'active'
GROUP BY ds.skill_id
ORDER BY current_demand_count DESC");

$conn->query("SET FOREIGN_KEY_CHECKS=1");

echo "Database V2 Migration Complete.";
?>
