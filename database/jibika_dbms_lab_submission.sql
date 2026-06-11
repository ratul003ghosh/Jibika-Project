-- ============================================================
-- JIBIKA - EMPLOYMENT & UNEMPLOYMENT MONITORING SYSTEM
-- DBMS LAB PROJECT SUBMISSION (STRICT 3NF NORMALIZED SCHEMA)
-- ============================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- ============================================================
-- 1. DATABASE DDL (SCHEMA DESIGN)
-- ============================================================

-- 1. REGIONS (To avoid repeating groups and ensure 3NF)
CREATE TABLE districts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE upazilas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    district_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    FOREIGN KEY (district_id) REFERENCES districts(id) ON DELETE CASCADE
);

-- 2. CORE USERS
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(20) UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('job_seeker', 'employer', 'admin') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 3. JOB SEEKER ENTITIES (1:1 with users)
CREATE TABLE job_seeker_profiles (
    user_id INT PRIMARY KEY,
    dob DATE,
    gender ENUM('Male', 'Female', 'Other'),
    address TEXT,
    district_id INT,
    upazila_id INT,
    preferred_category VARCHAR(100),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (district_id) REFERENCES districts(id) ON DELETE SET NULL,
    FOREIGN KEY (upazila_id) REFERENCES upazilas(id) ON DELETE SET NULL
);

-- 4. EMPLOYER ENTITIES (1:1 with users)
CREATE TABLE employer_profiles (
    user_id INT PRIMARY KEY,
    company_name VARCHAR(150) NOT NULL,
    company_type VARCHAR(100),
    trade_license VARCHAR(100) UNIQUE,
    district_id INT,
    company_address TEXT,
    company_description TEXT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (district_id) REFERENCES districts(id) ON DELETE SET NULL
);

-- 5. SKILLS MASTER TABLE (For M:N resolution)
CREATE TABLE skills (
    id INT AUTO_INCREMENT PRIMARY KEY,
    skill_name VARCHAR(100) NOT NULL UNIQUE
);

-- 6. JOB SEEKER SKILLS (M:N Junction Table)
CREATE TABLE job_seeker_skills (
    user_id INT,
    skill_id INT,
    PRIMARY KEY (user_id, skill_id),
    FOREIGN KEY (user_id) REFERENCES job_seeker_profiles(user_id) ON DELETE CASCADE,
    FOREIGN KEY (skill_id) REFERENCES skills(id) ON DELETE CASCADE
);

-- 7. EDUCATION (1:M with job seekers)
CREATE TABLE educations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    degree VARCHAR(100) NOT NULL,
    institution VARCHAR(200) NOT NULL,
    passing_year INT CHECK (passing_year > 1950 AND passing_year <= 2100),
    gpa DECIMAL(3,2),
    FOREIGN KEY (user_id) REFERENCES job_seeker_profiles(user_id) ON DELETE CASCADE
);

-- 8. EXPERIENCE (1:M with job seekers)
CREATE TABLE experiences (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    company_name VARCHAR(150) NOT NULL,
    job_position VARCHAR(100) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE,
    responsibilities TEXT,
    FOREIGN KEY (user_id) REFERENCES job_seeker_profiles(user_id) ON DELETE CASCADE
);

-- 9. JOBS (1:M with employers)
CREATE TABLE jobs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employer_id INT NOT NULL,
    title VARCHAR(150) NOT NULL,
    description TEXT NOT NULL,
    category VARCHAR(100) NOT NULL,
    job_type ENUM('Full-time', 'Part-time', 'Contract', 'Remote') NOT NULL,
    vacancy INT DEFAULT 1 CHECK (vacancy > 0),
    salary_min DECIMAL(10,2),
    salary_max DECIMAL(10,2),
    district_id INT,
    application_deadline DATE NOT NULL,
    status ENUM('active', 'closed') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (employer_id) REFERENCES employer_profiles(user_id) ON DELETE CASCADE,
    FOREIGN KEY (district_id) REFERENCES districts(id) ON DELETE SET NULL
);

-- 10. JOB REQUIRED SKILLS (M:N Junction Table)
CREATE TABLE job_required_skills (
    job_id INT,
    skill_id INT,
    PRIMARY KEY (job_id, skill_id),
    FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE CASCADE,
    FOREIGN KEY (skill_id) REFERENCES skills(id) ON DELETE CASCADE
);

-- 11. APPLICATIONS (M:N between job seekers and jobs)
CREATE TABLE applications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    job_id INT NOT NULL,
    user_id INT NOT NULL,
    status ENUM('pending', 'shortlisted', 'rejected', 'hired') DEFAULT 'pending',
    match_score DECIMAL(5,2) DEFAULT 0.00,
    applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE (job_id, user_id),
    FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES job_seeker_profiles(user_id) ON DELETE CASCADE
);

-- 12. NOTIFICATIONS
CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    job_id INT NULL,
    title_en VARCHAR(200),
    title_bn VARCHAR(200),
    message_en TEXT,
    message_bn TEXT,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE CASCADE
);

-- 13. SYSTEM LOGS
CREATE TABLE system_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    admin_id INT NOT NULL,
    action VARCHAR(255) NOT NULL,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 14. AUDIT LOGS (For Trigger)
CREATE TABLE job_deletion_logs (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    job_id INT,
    employer_id INT,
    title VARCHAR(150),
    deleted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================================
-- 2. DATABASE INDEXING (Performance Optimization)
-- ============================================================
CREATE INDEX idx_jobs_category ON jobs(category);
CREATE INDEX idx_jobs_district ON jobs(district_id);
CREATE INDEX idx_applications_job ON applications(job_id);
CREATE INDEX idx_users_role ON users(role);

COMMIT;


-- ============================================================
-- 2. SAMPLE SQL QUERIES FOR EVALUATION
-- ============================================================

/*
--------------------------------------------------------------
1. BASIC CRUD QUERIES
--------------------------------------------------------------
*/

-- INSERT Example
INSERT INTO users (name, email, phone, password, role) 
VALUES ('Rahim Uddin', 'rahim@gmail.com', '01711112222', 'hashed_pass', 'job_seeker');

-- SELECT Example
SELECT * FROM jobs WHERE status = 'active' AND vacancy >= 2;

-- UPDATE Example
UPDATE applications SET status = 'shortlisted' WHERE id = 101;

-- DELETE Example
DELETE FROM educations WHERE id = 5;


/*
--------------------------------------------------------------
2. JOIN QUERIES (Multi-table & INNER/LEFT)
--------------------------------------------------------------
*/

-- Inner Join: Fetch applicant names and emails for a specific job
SELECT a.id, u.name, u.email, a.status, a.match_score 
FROM applications a
INNER JOIN users u ON a.user_id = u.id
WHERE a.job_id = 45;

-- Left Join: Fetch all jobs and their total applicants (even if 0)
SELECT j.title, e.company_name, COUNT(a.id) as total_applicants
FROM jobs j
LEFT JOIN employer_profiles e ON j.employer_id = e.user_id
LEFT JOIN applications a ON j.id = a.job_id
GROUP BY j.id;

-- 3-Table Join: Fetch job seeker's district and upazila names
SELECT u.name, d.name AS district_name, up.name AS upazila_name 
FROM job_seeker_profiles jsp
JOIN users u ON jsp.user_id = u.id
JOIN districts d ON jsp.district_id = d.id
JOIN upazilas up ON jsp.upazila_id = up.id;


/*
--------------------------------------------------------------
3. SUBQUERIES
--------------------------------------------------------------
*/

-- Nested SELECT: Find job seekers who applied to a specific IT company
SELECT name, email FROM users 
WHERE id IN (
    SELECT user_id FROM applications WHERE job_id IN (
        SELECT id FROM jobs WHERE employer_id = (
            SELECT user_id FROM employer_profiles WHERE company_name = 'Tech Solutions BD'
        )
    )
);

-- Nested SELECT: Find users who have more than 3 skills
SELECT name FROM users 
WHERE id IN (
    SELECT user_id FROM job_seeker_skills 
    GROUP BY user_id 
    HAVING COUNT(skill_id) > 3
);


/*
--------------------------------------------------------------
4. AGGREGATE QUERIES (COUNT, SUM, AVG, GROUP BY, HAVING)
--------------------------------------------------------------
*/

-- Unemployment / Job Statistics: Count jobs by category that are still active
SELECT category, COUNT(id) as total_jobs, SUM(vacancy) as total_vacancies 
FROM jobs 
WHERE status = 'active'
GROUP BY category 
HAVING total_vacancies > 5
ORDER BY total_vacancies DESC;

-- Average match score of applicants for a specific employer's jobs
SELECT j.title, AVG(a.match_score) as avg_score
FROM jobs j
JOIN applications a ON j.id = a.job_id
WHERE j.employer_id = 10
GROUP BY j.id;


/*
--------------------------------------------------------------
5. ADVANCED QUERIES (Ranking & Skill Matching)
--------------------------------------------------------------
*/

-- TOP 5 Candidate Query (Job-wise applicant ranking)
SELECT u.name, u.email, a.match_score, a.status 
FROM applications a
JOIN users u ON a.user_id = u.id
WHERE a.job_id = 105
ORDER BY a.match_score DESC
LIMIT 5;

-- Skill Matching Query (Find job seekers who have ALL the required skills for Job ID = 20)
-- Using relational division (GROUP BY and COUNT matching)
SELECT u.id, u.name 
FROM users u
JOIN job_seeker_skills jss ON u.id = jss.user_id
WHERE jss.skill_id IN (SELECT skill_id FROM job_required_skills WHERE job_id = 20)
GROUP BY u.id, u.name
HAVING COUNT(jss.skill_id) = (SELECT COUNT(skill_id) FROM job_required_skills WHERE job_id = 20);

-- ============================================================
-- 3. ADVANCED DATABASE FEATURES (TRIGGERS & PROCEDURES)
-- ============================================================

-- TRIGGER: Automatically send a notification when an application status changes
DELIMITER //
CREATE TRIGGER after_application_update 
AFTER UPDATE ON applications
FOR EACH ROW 
BEGIN
    IF NEW.status = 'hired' AND OLD.status != 'hired' THEN
        INSERT INTO notifications (user_id, job_id, title_en, title_bn, message_en, message_bn)
        VALUES (NEW.user_id, NEW.job_id, 'Application Accepted', 'আবেদন গৃহীত', 'Congratulations! You have been hired.', 'অভিনন্দন! আপনাকে নিয়োগ দেওয়া হয়েছে।');
    END IF;
END;
//
DELIMITER ;

-- STORED PROCEDURE: Generate comprehensive statistics for an Employer
DELIMITER //
CREATE PROCEDURE GetEmployerStatistics(IN emp_id INT)
BEGIN
    SELECT 
        (SELECT COUNT(*) FROM jobs WHERE employer_id = emp_id) AS total_jobs,
        (SELECT COUNT(*) FROM applications a JOIN jobs j ON a.job_id = j.id WHERE j.employer_id = emp_id) AS total_applications,
        (SELECT COUNT(*) FROM applications a JOIN jobs j ON a.job_id = j.id WHERE j.employer_id = emp_id AND a.status = 'hired') AS hired_candidates;
END;
//
DELIMITER ;

-- TRIGGER 2: Audit Logging for Job Deletions
DELIMITER //
CREATE TRIGGER after_job_delete 
AFTER DELETE ON jobs
FOR EACH ROW 
BEGIN
    INSERT INTO job_deletion_logs (job_id, employer_id, title)
    VALUES (OLD.id, OLD.employer_id, OLD.title);
END;
//
DELIMITER ;

-- STORED PROCEDURE 2: Get Top Candidates for a Job
DELIMITER //
CREATE PROCEDURE GetTopCandidates(IN p_job_id INT)
BEGIN
    SELECT u.name, u.email, a.match_score, a.status 
    FROM applications a
    JOIN users u ON a.user_id = u.id
    WHERE a.job_id = p_job_id
    ORDER BY a.match_score DESC
    LIMIT 5;
END;
//
DELIMITER ;

-- ============================================================
-- 4. DATABASE VIEWS
-- ============================================================

-- VIEW 1: Active Jobs View (Security & Simplification)
CREATE VIEW active_jobs_view AS
SELECT 
    j.id AS job_id, 
    j.title, 
    j.category, 
    d.name AS district_name,
    j.vacancy
FROM jobs j
LEFT JOIN districts d ON j.district_id = d.id
WHERE j.status = 'active';

-- VIEW 2: Employer Application Summary View
CREATE VIEW application_summary_view AS
SELECT 
    j.employer_id,
    j.id AS job_id,
    j.title,
    COUNT(a.id) AS total_applicants,
    SUM(CASE WHEN a.status = 'hired' THEN 1 ELSE 0 END) AS total_hired
FROM jobs j
LEFT JOIN applications a ON j.id = a.job_id
GROUP BY j.employer_id, j.id, j.title;

-- ============================================================
-- 5. DATABASE LEVEL SECURITY (Role-Based Access Control)
-- ============================================================

-- Note: In a real MySQL instance, you would create users and grant permissions:
-- CREATE USER 'employer_user'@'localhost' IDENTIFIED BY 'emp_pass123';
-- GRANT SELECT, INSERT, UPDATE ON jibika.jobs TO 'employer_user'@'localhost';
-- GRANT SELECT ON jibika.active_jobs_view TO 'job_seeker_user'@'localhost';
-- REVOKE DELETE ON jibika.system_logs FROM 'admin_user'@'localhost';
