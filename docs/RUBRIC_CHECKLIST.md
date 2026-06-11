# DBMS Lab Rubric Checklist

## Database Design - 30 Marks

- [x] Main entities identified
- [x] Primary keys on important tables
- [x] Foreign keys for users, profiles, jobs, applications, locations, logs
- [x] Unique constraints such as user email and application uniqueness
- [x] Status fields for jobs, applications, employment status, verifications
- [x] Created/updated timestamps where useful
- [x] Normalized location tables
- [x] Normalized skill dictionary and junction table
- [x] Normalized education support tables
- [x] Activity/status log tables
- [x] ERD support through `DATABASE_DESIGN.md`

## SQL Query Correctness & Quality - 30 Marks

- [x] JOIN queries
- [x] Aggregation queries
- [x] GROUP BY reports
- [x] HAVING examples
- [x] Subqueries
- [x] District/upazila/ward/date/status filters
- [x] Skill demand vs supply view
- [x] Employer activity view
- [x] `database/sample_queries.sql` with 20+ examples

## Project Completeness - 20 Marks

- [x] User registration/login
- [x] Job seeker profile
- [x] Employer profile
- [x] Job posting and management
- [x] Job applications
- [x] Job seeker skills
- [x] Employment status update
- [x] Admin dashboard analytics
- [x] Reports page
- [x] Notifications
- [x] Interview scheduling schema support

## Jibika-Specific Strength

- [x] Area-wise unemployment monitoring
- [x] Bangladesh district/upazila/ward support
- [x] Skill-based job matching support
- [x] Skill gap analysis support
- [x] Training/government/NGO recommendation logic support
- [x] Real Bangladesh map asset

## Remaining Future Improvements

- Convert all older direct SQL writes to prepared statements
- Add richer admin CRUD pages for every lookup table
- Add PDF export if required by evaluator
