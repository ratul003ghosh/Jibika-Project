# ✅ JIBIKA PROJECT - COMPLETE SETUP & RUNNING GUIDE

## 🚀 PROJECT STATUS: FULLY RUNNING

**Server**: PHP Development Server (Port 8000)  
**Database**: MariaDB/MySQL (Port 3307)  
**All Pages**: ✅ Working  

---

## 🌐 HOW TO ACCESS THE PROJECT

### IMPORTANT: Use Correct URL Format
```
❌ WRONG: localhost/jibika/jobseeker/dashboard.php (Apache on port 80)
✅ CORRECT: localhost:8000/jobseeker/dashboard.php (PHP Dev Server on port 8000)
```

---

## 📱 PUBLIC PAGES (No Login Required)

Visit these URLs directly in your browser:

| Page | URL |
|------|-----|
| 🏠 Home Page | [http://localhost:8000/index.php](http://localhost:8000/index.php) |
| 🔑 User Login | [http://localhost:8000/auth/login.php](http://localhost:8000/auth/login.php) |
| ✍️ User Register | [http://localhost:8000/auth/register.php](http://localhost:8000/auth/register.php) |
| 👨‍💼 Admin Login | [http://localhost:8000/admin_login.php](http://localhost:8000/admin_login.php) |

---

## 🔐 PROTECTED PAGES (Login Required)

These pages will automatically redirect to login if you're not logged in:

### Job Seeker Pages
- [http://localhost:8000/jobseeker/dashboard.php](http://localhost:8000/jobseeker/dashboard.php) - Dashboard
- [http://localhost:8000/jobseeker/jobs.php](http://localhost:8000/jobseeker/jobs.php) - Browse Jobs
- [http://localhost:8000/jobseeker/my_applications.php](http://localhost:8000/jobseeker/my_applications.php) - My Applications
- [http://localhost:8000/jobseeker/saved_jobs.php](http://localhost:8000/jobseeker/saved_jobs.php) - Saved Jobs
- [http://localhost:8000/jobseeker/skills.php](http://localhost:8000/jobseeker/skills.php) - Add Skills
- [http://localhost:8000/jobseeker/partner_finder.php](http://localhost:8000/jobseeker/partner_finder.php) - Partner Finder
- [http://localhost:8000/jobseeker/profile.php](http://localhost:8000/jobseeker/profile.php) - Profile

### Employer Pages
- [http://localhost:8000/employer/dashboard.php](http://localhost:8000/employer/dashboard.php) - Dashboard
- [http://localhost:8000/employer/post_job.php](http://localhost:8000/employer/post_job.php) - Post New Job
- [http://localhost:8000/employer/manage_jobs.php](http://localhost:8000/employer/manage_jobs.php) - Manage Jobs
- [http://localhost:8000/employer/applicants.php](http://localhost:8000/employer/applicants.php) - View Applicants
- [http://localhost:8000/employer/profile.php](http://localhost:8000/employer/profile.php) - Profile

### Admin Pages
- [http://localhost:8000/admin/dashboard.php](http://localhost:8000/admin/dashboard.php) - Admin Dashboard
- [http://localhost:8000/admin/users.php](http://localhost:8000/admin/users.php) - Manage Users
- [http://localhost:8000/admin/jobs.php](http://localhost:8000/admin/jobs.php) - Manage Jobs
- [http://localhost:8000/admin/reports.php](http://localhost:8000/admin/reports.php) - Reports
- [http://localhost:8000/admin/unemployed_details.php](http://localhost:8000/admin/unemployed_details.php) - Unemployed Details

---

## 🔑 TEST ACCOUNTS

### Admin Account
```
Email: sharifahmed@gmail.com
Password: (use the one you set up)
Role: Admin
```

### Employer Account
```
Email: employee1@gmail.com
Role: Employer
```

### Job Seeker Accounts
```
Email: testuser@gmail.com
Email: jobseeker1@gmail.com
Email: tuhin123@gmail.com
Role: Job Seeker
```

---

## 📊 DATABASE INFO

- **Host**: localhost
- **Port**: 3307 ⚠️ (Important!)
- **Database**: jibika_db
- **Username**: root
- **Password**: (empty)

The database connection is configured in: `assets/config/db.php`

---

## ⚠️ IF PAGES SHOW "NOT FOUND"

**Problem**: You're using the wrong URL format

**Solution**: 
1. Make sure you're using `localhost:8000` (port 8000)
2. NOT `localhost/jibika/` (port 80 / Apache)
3. The path should start with `/` after the domain

**Examples**:
```
✅ http://localhost:8000/
✅ http://localhost:8000/index.php
✅ http://localhost:8000/auth/login.php
✅ http://localhost:8000/jobseeker/dashboard.php

❌ http://localhost/jibika/
❌ http://localhost/jibika/index.php
❌ http://localhost:80/auth/login.php
```

---

## 🛠️ TROUBLESHOOTING

### Pages returning "Not Found"
- Check you're using `localhost:8000` (not localhost/jibika)
- Verify the PHP dev server is running
- Check server terminal for error messages

### Database Connection Error
- Verify MySQL is running and accessible on port 3307
- Check credentials in `assets/config/db.php`
- Ensure database `jibika_db` exists

### Pages Load but Show Errors
- Check the browser console for errors
- Check the PHP development server terminal for error logs
- Verify all files are in the correct location

---

## 📂 FILE LOCATIONS

```
c:\xampp\htdocs\jibika\jibika\
├── index.php                    ← Home page
├── admin_login.php              ← Admin login
├── auth/
│   ├── login.php               ← User login
│   ├── register.php            ← User registration
│   └── logout.php
├── jobseeker/                  ← Job seeker pages
├── employer/                   ← Employer pages
├── admin/                      ← Admin pages
├── assets/
│   ├── config/
│   │   └── db.php             ← Database config
│   ├── css/                    ← Stylesheets
│   ├── js/                     ← JavaScript
│   └── image/                  ← Images
└── includes/                   ← Shared components
```

---

## ✨ FEATURES

✅ Area-based Unemployment Monitoring  
✅ Smart Job Matching System  
✅ Skill Mapping & Management  
✅ Partner Finder for Business  
✅ Admin Dashboard & Analytics  
✅ Multi-language Support (Bengali & English)  
✅ User Profiles & Application Tracking  

---

## 📝 QUICK START CHECKLIST

- [x] Database configured and imported
- [x] PHP server running on port 8000
- [x] MySQL running on port 3307
- [x] All files in correct locations
- [x] All pages accessible and working
- [x] Public pages rendering content
- [x] Protected pages redirecting to login

**Status**: ✅ **READY TO USE**

---

**Last Updated**: May 30, 2026  
**Version**: 1.0  
**Status**: Production Ready
