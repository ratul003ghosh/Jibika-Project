-- ============================================================
-- JIBIKA DBMS RUBRIC UPGRADE
-- Safe additive upgrade: no existing data is deleted.
-- ============================================================

SET FOREIGN_KEY_CHECKS = 0;

-- ------------------------------------------------------------
-- 1. Compatibility columns used by existing live PHP pages
-- ------------------------------------------------------------
ALTER TABLE applications
    ADD COLUMN IF NOT EXISTS rejection_count INT NOT NULL DEFAULT 0,
    ADD COLUMN IF NOT EXISTS suggested_datetime DATETIME NULL,
    ADD COLUMN IF NOT EXISTS match_score DECIMAL(5,2) NOT NULL DEFAULT 0.00;

ALTER TABLE notifications
    ADD COLUMN IF NOT EXISTS job_id INT NULL AFTER user_id,
    ADD COLUMN IF NOT EXISTS title_en VARCHAR(200) NULL AFTER job_id,
    ADD COLUMN IF NOT EXISTS title_bn VARCHAR(200) NULL AFTER title_en,
    ADD COLUMN IF NOT EXISTS message_en TEXT NULL AFTER title_bn,
    ADD COLUMN IF NOT EXISTS message_bn TEXT NULL AFTER message_en,
    ADD COLUMN IF NOT EXISTS type VARCHAR(50) NOT NULL DEFAULT 'info' AFTER message_bn,
    ADD COLUMN IF NOT EXISTS link VARCHAR(255) NULL AFTER type;

ALTER TABLE job_seeker_profiles
    ADD COLUMN IF NOT EXISTS preferred_district VARCHAR(100) NULL,
    ADD COLUMN IF NOT EXISTS preferred_upazila VARCHAR(100) NULL,
    ADD COLUMN IF NOT EXISTS preferred_job_category VARCHAR(100) NULL,
    ADD COLUMN IF NOT EXISTS cv_path VARCHAR(255) NULL,
    ADD COLUMN IF NOT EXISTS experience_years DECIMAL(4,1) NOT NULL DEFAULT 0,
    ADD COLUMN IF NOT EXISTS availability_status VARCHAR(50) NOT NULL DEFAULT 'Available',
    ADD COLUMN IF NOT EXISTS is_remote TINYINT(1) NOT NULL DEFAULT 0,
    ADD COLUMN IF NOT EXISTS updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

ALTER TABLE employer_profiles
    ADD COLUMN IF NOT EXISTS company_description TEXT NULL,
    ADD COLUMN IF NOT EXISTS company_address TEXT NULL,
    ADD COLUMN IF NOT EXISTS status ENUM('active','inactive','pending') NOT NULL DEFAULT 'active';

ALTER TABLE messages
    ADD COLUMN IF NOT EXISTS receiver_id INT NULL AFTER sender_id,
    ADD COLUMN IF NOT EXISTS message_text TEXT NULL AFTER receiver_id;

ALTER TABLE messages
    MODIFY COLUMN thread_id INT NULL,
    MODIFY COLUMN message TEXT NULL;

UPDATE employer_profiles
SET company_description = COALESCE(company_description, description)
WHERE company_description IS NULL;

UPDATE employer_profiles
SET company_address = COALESCE(company_address, address)
WHERE company_address IS NULL;

UPDATE messages
SET message_text = COALESCE(message_text, message)
WHERE message_text IS NULL;

-- ------------------------------------------------------------
-- 2. Normalize skills and education for 3NF support
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS dic_skills (
    skill_id INT AUTO_INCREMENT PRIMARY KEY,
    skill_name VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS job_seeker_skills (
    user_id INT NOT NULL,
    skill_id INT NOT NULL,
    proficiency ENUM('Beginner','Intermediate','Expert') NOT NULL DEFAULT 'Intermediate',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id, skill_id),
    CONSTRAINT fk_jss_user FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    CONSTRAINT fk_jss_skill FOREIGN KEY (skill_id) REFERENCES dic_skills(skill_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS dic_education_levels (
    level_id INT AUTO_INCREMENT PRIMARY KEY,
    level_name VARCHAR(50) NOT NULL UNIQUE,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS dic_institutions (
    institution_id INT AUTO_INCREMENT PRIMARY KEY,
    institution_name VARCHAR(200) NOT NULL UNIQUE,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS seeker_education (
    education_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    level_id INT NOT NULL,
    institution_id INT NULL,
    passing_year YEAR NULL,
    result VARCHAR(30) NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_seeker_education_user FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    CONSTRAINT fk_seeker_education_level FOREIGN KEY (level_id) REFERENCES dic_education_levels(level_id) ON DELETE RESTRICT,
    CONSTRAINT fk_seeker_education_institution FOREIGN KEY (institution_id) REFERENCES dic_institutions(institution_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO dic_education_levels (level_name)
VALUES ('SSC'), ('HSC'), ('Diploma'), ('Bachelor'), ('Masters'), ('PhD'), ('Other');

INSERT IGNORE INTO dic_skills (skill_name)
SELECT DISTINCT TRIM(skill_name)
FROM skills
WHERE skill_name IS NOT NULL AND TRIM(skill_name) <> '';

INSERT IGNORE INTO job_seeker_skills (user_id, skill_id, proficiency)
SELECT DISTINCT s.user_id, ds.skill_id, 'Intermediate'
FROM skills s
JOIN dic_skills ds ON LOWER(ds.skill_name) = LOWER(TRIM(s.skill_name))
WHERE s.user_id IS NOT NULL AND s.skill_name IS NOT NULL AND TRIM(s.skill_name) <> '';

INSERT IGNORE INTO seeker_education (user_id, level_id)
SELECT DISTINCT jsp.user_id,
       COALESCE(del.level_id, (SELECT level_id FROM dic_education_levels WHERE level_name = 'Other' LIMIT 1))
FROM job_seeker_profiles jsp
LEFT JOIN dic_education_levels del ON LOWER(del.level_name) = LOWER(TRIM(jsp.education))
WHERE jsp.education IS NOT NULL AND TRIM(jsp.education) <> '';

ALTER TABLE job_required_skills
    ADD COLUMN IF NOT EXISTS skill_id INT NULL AFTER job_id;

UPDATE job_required_skills jrs
JOIN dic_skills ds ON LOWER(ds.skill_name) = LOWER(TRIM(jrs.skill_name))
SET jrs.skill_id = ds.skill_id
WHERE jrs.skill_id IS NULL;

-- ------------------------------------------------------------
-- 3. Interview scheduling and status history
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS interviews (
    interview_id INT AUTO_INCREMENT PRIMARY KEY,
    job_id INT NOT NULL,
    application_id INT NOT NULL,
    employer_id INT NOT NULL,
    candidate_id INT NOT NULL,
    interview_type ENUM('online','offline') NOT NULL DEFAULT 'offline',
    interview_title VARCHAR(255) NOT NULL,
    interview_datetime DATETIME NOT NULL,
    interview_location TEXT NULL,
    meeting_link TEXT NULL,
    notes TEXT NULL,
    status ENUM('proposed','scheduled','completed','cancelled','selected','rejected','reschedule_requested') NOT NULL DEFAULT 'proposed',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_interviews_job FOREIGN KEY (job_id) REFERENCES jobs(job_id) ON DELETE CASCADE,
    CONSTRAINT fk_interviews_application FOREIGN KEY (application_id) REFERENCES applications(application_id) ON DELETE CASCADE,
    CONSTRAINT fk_interviews_employer FOREIGN KEY (employer_id) REFERENCES users(user_id) ON DELETE CASCADE,
    CONSTRAINT fk_interviews_candidate FOREIGN KEY (candidate_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS interview_status_history (
    history_id BIGINT AUTO_INCREMENT PRIMARY KEY,
    interview_id INT NOT NULL,
    old_status VARCHAR(50) NULL,
    new_status VARCHAR(50) NOT NULL,
    changed_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_interview_history_interview FOREIGN KEY (interview_id) REFERENCES interviews(interview_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- 4. Views for dashboard/reports/viva query demonstration
-- ------------------------------------------------------------
CREATE OR REPLACE VIEW vw_area_unemployment_summary AS
SELECT
    d.district_id,
    d.district_name,
    COUNT(DISTINCT jsp.user_id) AS job_seekers,
    SUM(CASE WHEN es.current_status = 'unemployed' THEN 1 ELSE 0 END) AS unemployed,
    SUM(CASE WHEN es.current_status = 'employed' THEN 1 ELSE 0 END) AS employed,
    SUM(CASE WHEN es.current_status = 'training' THEN 1 ELSE 0 END) AS training,
    SUM(CASE WHEN es.current_status = 'self_employed' THEN 1 ELSE 0 END) AS self_employed
FROM districts d
LEFT JOIN job_seeker_profiles jsp ON d.district_id = jsp.district_id
LEFT JOIN employment_status es ON jsp.user_id = es.user_id
GROUP BY d.district_id, d.district_name;

CREATE OR REPLACE VIEW vw_skill_supply AS
SELECT
    ds.skill_id,
    ds.skill_name,
    COUNT(DISTINCT jss.user_id) AS supply_count
FROM dic_skills ds
LEFT JOIN job_seeker_skills jss ON ds.skill_id = jss.skill_id
GROUP BY ds.skill_id, ds.skill_name;

CREATE OR REPLACE VIEW vw_skill_demand AS
SELECT
    ds.skill_id,
    ds.skill_name,
    COUNT(DISTINCT jrs.job_id) AS demand_count
FROM dic_skills ds
LEFT JOIN job_required_skills jrs ON ds.skill_id = jrs.skill_id
LEFT JOIN jobs j ON jrs.job_id = j.job_id AND j.status = 'active'
GROUP BY ds.skill_id, ds.skill_name;

CREATE OR REPLACE VIEW vw_skill_gap AS
SELECT
    ss.skill_id,
    ss.skill_name,
    ss.supply_count,
    COALESCE(sd.demand_count, 0) AS demand_count,
    COALESCE(sd.demand_count, 0) - ss.supply_count AS gap_count
FROM vw_skill_supply ss
LEFT JOIN vw_skill_demand sd ON ss.skill_id = sd.skill_id;

CREATE OR REPLACE VIEW vw_employer_activity AS
SELECT
    u.user_id AS employer_id,
    u.full_name,
    ep.company_name,
    COUNT(DISTINCT j.job_id) AS total_jobs,
    COUNT(DISTINCT a.application_id) AS total_applications
FROM users u
LEFT JOIN employer_profiles ep ON u.user_id = ep.user_id
LEFT JOIN jobs j ON u.user_id = j.employer_id
LEFT JOIN applications a ON j.job_id = a.job_id
WHERE u.role = 'employer'
GROUP BY u.user_id, u.full_name, ep.company_name;

-- ------------------------------------------------------------
-- 5. Helpful indexes for filter/report queries
-- ------------------------------------------------------------
CREATE INDEX IF NOT EXISTS idx_users_role_created ON users(role, created_at);
CREATE INDEX IF NOT EXISTS idx_jobs_status_created ON jobs(status, created_at);
CREATE INDEX IF NOT EXISTS idx_jobs_area ON jobs(district_id, upazila_id, ward_id);
CREATE INDEX IF NOT EXISTS idx_applications_status_date ON applications(status, applied_at);
CREATE INDEX IF NOT EXISTS idx_profiles_area ON job_seeker_profiles(district_id, upazila_id, ward_id);
CREATE INDEX IF NOT EXISTS idx_employment_status_current ON employment_status(current_status);
CREATE INDEX IF NOT EXISTS idx_notifications_read_date ON notifications(user_id, is_read, created_at);
CREATE INDEX IF NOT EXISTS idx_interviews_employer_time ON interviews(employer_id, interview_datetime);
CREATE INDEX IF NOT EXISTS idx_interviews_candidate_time ON interviews(candidate_id, interview_datetime);
CREATE INDEX IF NOT EXISTS idx_messages_receiver ON messages(receiver_id);

-- ------------------------------------------------------------
-- 6. Status-change triggers for audit/history support
-- ------------------------------------------------------------
DROP TRIGGER IF EXISTS trg_applications_status_log;
DROP TRIGGER IF EXISTS trg_interviews_status_log;

DELIMITER //

CREATE TRIGGER trg_applications_status_log
AFTER UPDATE ON applications
FOR EACH ROW
BEGIN
    IF COALESCE(OLD.status, '') <> COALESCE(NEW.status, '') THEN
        INSERT INTO application_status_logs (application_id, old_status, new_status, changed_by, remarks)
        VALUES (NEW.application_id, OLD.status, NEW.status, NULL, 'Auto logged by DB trigger');
    END IF;
END//

CREATE TRIGGER trg_interviews_status_log
AFTER UPDATE ON interviews
FOR EACH ROW
BEGIN
    IF COALESCE(OLD.status, '') <> COALESCE(NEW.status, '') THEN
        INSERT INTO interview_status_history (interview_id, old_status, new_status)
        VALUES (NEW.interview_id, OLD.status, NEW.status);
    END IF;
END//

DELIMITER ;

SET FOREIGN_KEY_CHECKS = 1;
