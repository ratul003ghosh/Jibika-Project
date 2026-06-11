-- ============================================================
-- JIBIKA DBMS LAB SAMPLE QUERIES
-- Demonstrates CRUD, JOIN, GROUP BY, HAVING, subquery, filtering,
-- aggregation, ordering, and reporting logic.
-- ============================================================

-- 1. SELECT: active jobs in a district
SELECT j.job_id, j.title, d.district_name, j.created_at
FROM jobs j
LEFT JOIN districts d ON j.district_id = d.district_id
WHERE j.status = 'active' AND d.district_name = 'Dhaka'
ORDER BY j.created_at DESC;

-- 2. INSERT: new skill dictionary value
INSERT IGNORE INTO dic_skills (skill_name) VALUES ('Digital Marketing');

-- 3. UPDATE: close expired jobs
UPDATE jobs
SET status = 'closed'
WHERE application_deadline IS NOT NULL
  AND application_deadline < CURDATE();

-- 4. DELETE: remove a saved job link safely
DELETE FROM saved_jobs
WHERE user_id = 10 AND job_id = 25;

-- 5. JOIN: users with job seeker profile and location
SELECT u.user_id, u.full_name, u.email, d.district_name, up.upazila_name, w.ward_name
FROM users u
JOIN job_seeker_profiles jsp ON u.user_id = jsp.user_id
LEFT JOIN districts d ON jsp.district_id = d.district_id
LEFT JOIN upazilas up ON jsp.upazila_id = up.upazila_id
LEFT JOIN wards w ON jsp.ward_id = w.ward_id
WHERE u.role = 'job_seeker';

-- 6. JOIN: jobs with employer company
SELECT j.job_id, j.title, ep.company_name, d.district_name, j.status
FROM jobs j
LEFT JOIN employer_profiles ep ON j.employer_id = ep.user_id
LEFT JOIN districts d ON j.district_id = d.district_id
ORDER BY j.created_at DESC;

-- 7. JOIN: applications with jobs and applicants
SELECT a.application_id, u.full_name AS applicant, j.title, a.status, a.applied_at
FROM applications a
JOIN users u ON a.user_id = u.user_id
JOIN jobs j ON a.job_id = j.job_id
ORDER BY a.applied_at DESC;

-- 8. Aggregation: total users by role
SELECT role, COUNT(*) AS total_users
FROM users
GROUP BY role;

-- 9. GROUP BY: district-wise unemployment report
SELECT d.district_name,
       COUNT(DISTINCT jsp.user_id) AS job_seekers,
       SUM(CASE WHEN es.current_status = 'unemployed' THEN 1 ELSE 0 END) AS unemployed
FROM districts d
LEFT JOIN job_seeker_profiles jsp ON d.district_id = jsp.district_id
LEFT JOIN employment_status es ON jsp.user_id = es.user_id
GROUP BY d.district_id, d.district_name
ORDER BY unemployed DESC;

-- 10. GROUP BY: application status report
SELECT status, COUNT(*) AS total
FROM applications
GROUP BY status
ORDER BY total DESC;

-- 11. GROUP BY: top available skills
SELECT ds.skill_name, COUNT(jss.user_id) AS supply_count
FROM dic_skills ds
JOIN job_seeker_skills jss ON ds.skill_id = jss.skill_id
GROUP BY ds.skill_id, ds.skill_name
ORDER BY supply_count DESC
LIMIT 10;

-- 12. GROUP BY: top demanded skills
SELECT ds.skill_name, COUNT(jrs.job_id) AS demand_count
FROM dic_skills ds
JOIN job_required_skills jrs ON ds.skill_id = jrs.skill_id
JOIN jobs j ON jrs.job_id = j.job_id
WHERE j.status = 'active'
GROUP BY ds.skill_id, ds.skill_name
ORDER BY demand_count DESC
LIMIT 10;

-- 13. HAVING: areas with more than 100 unemployed users
SELECT d.district_name, COUNT(es.user_id) AS unemployed_count
FROM districts d
JOIN job_seeker_profiles jsp ON d.district_id = jsp.district_id
JOIN employment_status es ON jsp.user_id = es.user_id
WHERE es.current_status = 'unemployed'
GROUP BY d.district_id, d.district_name
HAVING unemployed_count > 100;

-- 14. Subquery: areas with unemployment above average
SELECT district_name, unemployed
FROM vw_area_unemployment_summary
WHERE unemployed > (
    SELECT AVG(unemployed) FROM vw_area_unemployment_summary
)
ORDER BY unemployed DESC;

-- 15. Subquery: employers with more than average job posts
SELECT employer_id, company_name, total_jobs
FROM vw_employer_activity
WHERE total_jobs > (
    SELECT AVG(total_jobs) FROM vw_employer_activity
)
ORDER BY total_jobs DESC;

-- 16. Skill demand vs supply gap
SELECT skill_name, demand_count, supply_count, gap_count
FROM vw_skill_gap
ORDER BY gap_count DESC, demand_count DESC
LIMIT 10;

-- 17. Date range filter: applications this month
SELECT a.application_id, u.full_name, j.title, a.status, a.applied_at
FROM applications a
JOIN users u ON a.user_id = u.user_id
JOIN jobs j ON a.job_id = j.job_id
WHERE DATE(a.applied_at) BETWEEN DATE_FORMAT(CURDATE(), '%Y-%m-01') AND CURDATE()
ORDER BY a.applied_at DESC;

-- 18. Conditional area filter by district/upazila/status
SELECT u.full_name, d.district_name, up.upazila_name, es.current_status
FROM users u
JOIN job_seeker_profiles jsp ON u.user_id = jsp.user_id
LEFT JOIN districts d ON jsp.district_id = d.district_id
LEFT JOIN upazilas up ON jsp.upazila_id = up.upazila_id
LEFT JOIN employment_status es ON u.user_id = es.user_id
WHERE d.district_name = 'Rangpur'
  AND es.current_status = 'unemployed';

-- 19. Employer activity count
SELECT ep.company_name, COUNT(DISTINCT j.job_id) AS jobs_posted,
       COUNT(DISTINCT a.application_id) AS applications_received
FROM employer_profiles ep
LEFT JOIN jobs j ON ep.user_id = j.employer_id
LEFT JOIN applications a ON j.job_id = a.job_id
GROUP BY ep.user_id, ep.company_name
ORDER BY applications_received DESC;

-- 20. Job seekers who match active jobs by skill and district
SELECT DISTINCT js.user_id, u.full_name, j.job_id, j.title, d.district_name
FROM job_seeker_skills js
JOIN job_required_skills jrs ON js.skill_id = jrs.skill_id
JOIN jobs j ON jrs.job_id = j.job_id
JOIN users u ON js.user_id = u.user_id
LEFT JOIN job_seeker_profiles jsp ON u.user_id = jsp.user_id
LEFT JOIN districts d ON jsp.district_id = d.district_id
WHERE j.status = 'active'
  AND (j.district_id = jsp.district_id OR j.district_id IS NULL)
ORDER BY j.created_at DESC;

-- 21. Training need score by district
SELECT district_name,
       unemployed,
       training,
       ROUND((unemployed * 1.0) / NULLIF(job_seekers, 0) * 100, 2) AS unemployment_rate
FROM vw_area_unemployment_summary
ORDER BY unemployment_rate DESC;
