# CRUD Features

## Users

- Create: registration
- Read: admin users page
- Update: profile pages and status flows
- Delete/deactivate: admin users page supports safe delete for non-admin users

## Job Seekers

- Create/update: `jobseeker/profile.php`
- Read: admin users and area monitor pages
- Skills CRUD: `jobseeker/skills.php`
- Employment status update: `admin/update_status.php`

## Employers

- Create/update: `employer/profile.php`
- Read: admin users/employer filters
- Jobs and applicants managed by employer pages

## Jobs

- Create: `employer/post_job.php`
- Read: public/jobseeker jobs, employer dashboard, admin jobs
- Update/deactivate: employer manage jobs
- Delete: employer/admin job management

## Applications

- Create: job seeker apply flow
- Read: employer applicants, job seeker application tracking, admin reports
- Update: employer accept/reject and interview tracking
- Delete: not hard-deleted in normal workflow; status changes preserve history

## Skills & Training

- User skills are managed in `jobseeker/skills.php`
- Normalized skill tables support analytics and matching
- Training courses exist in `training_courses`

## Area Data

- `districts`, `upazilas`, and `wards` store manageable area data
- Area reports use these tables with profiles, jobs, and employment status

## Reports

- `admin/reports.php`
- `admin/dashboard.php`
- reporting views added in the database upgrade
