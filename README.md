# 🌿 Jibika (জীবিকা) - Smart Job Matching & Unemployment Monitoring System

**Jibika** is a comprehensive, area-based unemployment monitoring and smart job matching portal built for Bangladesh. It connects job seekers, employers, and government administrators through a unified, highly professional platform to facilitate local employment and monitor economic trends.

![Jibika Banner](https://img.shields.io/badge/Status-Active-brightgreen?style=for-the-badge)
![Tech Stack](https://img.shields.io/badge/Tech_Stack-PHP_|_MySQL_|_Bootstrap_5-blue?style=for-the-badge)

---

## 🚀 Key Features

### For Job Seekers
* **Smart Job Matching:** Get recommended jobs based on your skills, experience, and specific geographic location (District/Upazila/Ward).
* **Targeted Opportunities:** Dedicated quick-access filters for highly sought-after demographics: **Student Part-time**, **Day Laborers (Daily/Weekly)**, and **Internships**.
* **Skill Mapping:** Build a robust professional profile that allows employers to find you based on your core competencies.
* **Jibika Resources:** Access high-fidelity professional development tools directly from the portal, including a CV Writing Guide, Interview Tips, and Career Counseling.

### For Employers
* **Premium Dashboard:** A modern administrative hub with interactive KPI stat cards and visual data tables to track active job posts and applicant volume.
* **Flexible Hiring:** Post standard corporate roles, or specifically hire for Day Labor, Remote Work, or Student Internships.
* **Streamlined Recruitment:** Review applicant profiles and track application statuses seamlessly.

### For Government / Administrators
* **Area-Based Monitoring:** Track unemployment rates and skill availability down to the specific Ward level.
* **Data-Driven Policy:** Utilize platform analytics to plan targeted training programs and distribute NGO resources effectively.
* **Entrepreneur Support:** Incubator programs and legal E-service redirects to encourage small business creation.

---

## 🛠️ Technology Stack

* **Frontend:** HTML5, CSS3, Bootstrap 5.3, JavaScript (DOM Manipulation & Modals), FontAwesome 6
* **Backend:** PHP (Session Management, Routing, CRUD Operations)
* **Database:** MySQL (`jibika_db.sql`)
* **Design Language:** Government-standard aesthetic (Primary Green `#006a4e`, Accent Red `#f42a41`)

---

## ⚙️ Installation & Setup

1. **Clone the repository:**
   ```bash
   git clone https://github.com/ratul003ghosh/Jibika-Project.git
   ```

2. **Database Configuration:**
   * Install a local server environment like XAMPP, WAMP, or MAMP.
   * Open phpMyAdmin and create a new database named `jibika`.
   * Import the provided `jibika_db.sql` file into the new database.

3. **Backend Connection:**
   * Navigate to `main/assets/config/db.php`.
   * Ensure the database credentials match your local setup:
     ```php
     $host = "localhost";
     $user = "root";
     $pass = "";
     $db   = "jibika";
     ```

4. **Run the Project:**
   * Move the project folder into your server's root directory (e.g., `htdocs` for XAMPP).
   * Open your browser and navigate to `http://localhost/Jibika-Project/main/`.

---

## 📂 Project Structure

* `/main/` - The core application codebase.
  * `/assets/` - CSS styles, images, and configuration files.
  * `/auth/` - Registration, login, and session handling.
  * `/includes/` - Reusable components (Header, Footer, Navbar).
  * `/jobseeker/` - Job seeker dashboards, profile management, and job search.
  * `/employer/` - Employer dashboards, job posting, and applicant tracking.
* `jibika_db.sql` - The complete database schema and structural dump.

---

*Developed and maintained to modernize local employment ecosystems.*
