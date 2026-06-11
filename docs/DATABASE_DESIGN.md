# Database Design

Database: `jibika_db`

The database uses InnoDB tables with primary keys, foreign keys, unique constraints, status fields, and timestamps across the main entities.

## Main Entities

- `users`: common account table for job seekers, employers, and admins
- `job_seeker_profiles`: job seeker personal, location, education, and preference data
- `employer_profiles`: employer/company information
- `jobs`: job posts created by employers
- `applications`: job seeker applications to jobs
- `districts`, `upazilas`, `wards`: normalized Bangladesh area data
- `employment_status`: latest employment state per job seeker
- `employment_status_logs`: status change history
- `notifications`: user notifications
- `activity_logs`: admin/system activity
- `skills`: existing user skill table
- `dic_skills`: normalized skill dictionary
- `job_seeker_skills`: many-to-many job seeker skill table
- `job_required_skills`: required skills for jobs
- `dic_education_levels`, `dic_institutions`, `seeker_education`: normalized education support
- `interviews`, `interview_status_history`: interview scheduling and state tracking
- `area_unemployment_reports`, reporting views: analytics/report support

## Relationships

- `job_seeker_profiles.user_id` references `users.user_id`
- `employer_profiles.user_id` references `users.user_id`
- `jobs.employer_id` references `users.user_id`
- `applications.job_id` references `jobs.job_id`
- `applications.user_id` references `users.user_id`
- `upazilas.district_id` references `districts.district_id`
- `wards.upazila_id` references `upazilas.upazila_id`
- `employment_status.user_id` references `users.user_id`
- `employment_status_logs.user_id` references `users.user_id`
- `job_seeker_skills.user_id` references `users.user_id`
- `job_seeker_skills.skill_id` references `dic_skills.skill_id`
- `interviews.application_id` references `applications.application_id`

## Normalization

The project supports 3NF-style design by separating:

- User account data from job seeker/employer profile data
- Area names into district/upazila/ward tables
- Skill names into `dic_skills`
- Job seeker skills into `job_seeker_skills`
- Education levels into `dic_education_levels`
- Application and interview state history into log/history tables

Compatibility columns such as `job_seeker_profiles.skills` are kept so older working pages do not break, but normalized tables are now available for cleaner reports and matching.

## Important Views

- `vw_area_unemployment_summary`
- `vw_skill_supply`
- `vw_skill_demand`
- `vw_skill_gap`
- `vw_employer_activity`

These views help the dashboard, reports, and viva demonstration.
