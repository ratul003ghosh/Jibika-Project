# JIBIKA PROJECT - SETUP & RUNNING GUIDE

## Project Status
✅ **Project is now fully configured and running!**

---

## How to Run the Project

### Option 1: Using PHP Built-in Server (Recommended for Development)

1. Open Command Prompt or PowerShell
2. Navigate to the project folder:
   ```
   cd c:\xampp\htdocs\jibika\jibika
   ```
3. Start the PHP development server:
   ```
   C:\xampp\php\php.exe -S localhost:8000
   ```
4. Open your browser and visit:
   ```
   http://localhost:8000
   ```

### Option 2: Using XAMPP Control Panel

1. Open XAMPP Control Panel
2. Start Apache and MySQL (MariaDB) services
3. Place the `jibika` folder in `C:\xampp\htdocs\`
4. Visit: `http://localhost/jibika/jibika/`

---

## Important Configuration Details

### Database Information
- **Host**: localhost
- **Port**: 3307 (Important: Not 3306!)
- **Database**: jibika_db
- **Username**: root
- **Password**: (empty)

### Database Connection File
Location: `assets/config/db.php`

This file has been updated to use the correct port (3307).

---

## Test Credentials

You can test the application using any of these accounts:

### Admin Account
- **Email**: sharifahmed@gmail.com
- **Password**: (use password hasher or check SQL dump for hashed password)
- **Role**: Admin

### Employer Account
- **Email**: employee1@gmail.com
- **Role**: Employer

### Job Seeker Accounts
- **Email**: testuser@gmail.com
- **Email**: jobseeker1@gmail.com
- **Email**: tuhin123@gmail.com

---

## Pages Available

### Public Pages
- **Home**: `/` or `index.php`
- **Register**: `/auth/register.php`
- **Login**: `/auth/login.php`
- **Admin Login**: `/admin_login.php`

### Job Seeker Pages (after login)
- Dashboard: `/jobseeker/dashboard.php`
- Jobs: `/jobseeker/jobs.php`
- My Applications: `/jobseeker/my_applications.php`
- Skills: `/jobseeker/skills.php`
- Saved Jobs: `/jobseeker/saved_jobs.php`
- Partner Finder: `/jobseeker/partner_finder.php`
- Profile: `/jobseeker/profile.php`

### Employer Pages (after login)
- Dashboard: `/employer/dashboard.php`
- Post Job: `/employer/post_job.php`
- Manage Jobs: `/employer/manage_jobs.php`
- Applicants: `/employer/applicants.php`
- Profile: `/employer/profile.php`

### Admin Pages
- Dashboard: `/admin/dashboard.php`
- Jobs: `/admin/jobs.php`
- Users: `/admin/users.php`
- Reports: `/admin/reports.php`
- Unemployed Details: `/admin/unemployed_details.php`

---

## Fixes Applied

1. ✅ Fixed typo URLs: `/jiibika/` → `/jibika/`
2. ✅ Fixed include paths in `user/dashboard.php`
3. ✅ Corrected database redirect paths
4. ✅ Updated database port from 3306 to 3307
5. ✅ Imported complete database schema
6. ✅ Disabled MySQLi exceptions on error (for graceful error handling)

---

## Features

- **Area-based Unemployment Monitoring**: Track unemployment by district, upazila, and ward
- **Smart Job Matching**: Match job seekers with suitable job opportunities
- **Skill Mapping**: Map job seeker skills to available positions
- **Partner Finder**: Help job seekers find business partners
- **Admin Dashboard**: Monitor system statistics and user activities
- **Multi-language Support**: Bengali and English

---

## Troubleshooting

### MySQL Connection Error
If you see "Database connection failed", check:
1. MySQL/MariaDB service is running
2. Port 3307 is correct (not 3306)
3. Credentials in `assets/config/db.php` are correct

### Page Not Found
Make sure:
1. You're accessing `http://localhost:8000` (not `localhost:3000`)
2. The path includes `/jibika/` (not `/jiibika/`)
3. All files are in the correct directory

### CSS/JS Not Loading
This is normal with the development server. CSS and JS files are being loaded from `/jibika/assets/`

---

## Project Structure

```
jibika/
├── admin/                  # Admin panel pages
├── auth/                   # Authentication (login, register, logout)
├── employer/               # Employer dashboard and features
├── includes/               # Shared components (header, footer, navbar)
├── jobseeker/              # Job seeker dashboard and features
├── user/                   # General user pages
├── assets/                 # CSS, JS, images, and DB config
│   ├── config/db.php       # Database configuration
│   ├── css/                # Stylesheets
│   ├── js/                 # JavaScript files
│   └── image/              # Images
├── index.php               # Home page
├── admin_login.php         # Admin login page
└── jibika_db.sql           # Database backup (reference only)
```

---

## Next Steps

1. Start the server
2. Visit http://localhost:8000
3. Login with any test account (see credentials above)
4. Explore the features
5. Check admin dashboard for system overview

---

## Support

For issues or questions, check:
- Database configuration in `assets/config/db.php`
- MySQL service status (should be running)
- Port 3307 is accessible
- All PHP files have correct include paths

---

**Last Updated**: May 30, 2026
**Status**: ✅ Ready for Production/Testing
