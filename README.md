# Jibika - Job Portal (জীবিকা)

![Jibika Project Banner](https://via.placeholder.com/1000x300?text=Jibika+-+Smart+Employment+Platform)

**Jibika** is an area-based unemployment monitoring and smart employment platform built for Bangladesh. While it looks like a standard job portal, it is designed to serve as a powerful tool for government policymakers and NGOs to monitor unemployment data at a hyper-local level (District, Upazila, and Ward) to provide targeted training and support.

## 🚀 Key Features

* **Bilingual UI**: Fully localized in both English and Bengali (`$_SESSION['lang']`).
* **Smart Match**: Advanced job filtering and search matching skills to local opportunities.
* **Area-Based Monitoring**: Granular unemployment data tracking (district, upazila, ward).
* **Role-Based Access**: Specialized dashboards for Job Seekers, Employers, and Administrators.
* **Specialized Sectors**: Focus on inclusive opportunities like Day Labor, Part-time Students, and Agriculture.

## 🛠 Tech Stack

* **Frontend**: HTML5, Vanilla CSS3, JavaScript, Bootstrap 5
* **Backend**: PHP 8+ (Procedural)
* **Database**: MySQL
* **Environment**: XAMPP (Local Development)

## 📋 Prerequisites

To run this project locally, you will need:
* **XAMPP** (or any similar AMP stack like WAMP/MAMP)
* **PHP** version 8.0 or higher
* **MySQL** database server (runs on port 3307 in this setup)

## ⚙️ Installation / Setup

1. **Clone the repository**:
   Open terminal inside `C:\xampp\htdocs\` and run:
   ```bash
   git clone https://github.com/ratul003ghosh/Jibika-Project.git
   ```
2. **Setup Database**:
   * Open XAMPP Control Panel and start **Apache** and **MySQL**.
   * Go to `http://localhost/phpmyadmin`
   * Create a new database named `jibika_db`
   * Import the SQL file located at `database/jibika_db.sql` into this new database.
3. **Configure Database Connection**:
   * Open `src/assets/config/db.php`
   * Ensure the credentials match your local setup (e.g., username `root`, password ``, port `3307`).
4. **Run the Project**:
   * Open your browser and navigate to: `http://localhost/Jibika-Project/src/`

## 🔀 Branching Strategy & Team Workflow

To avoid breaking code and managing conflicts easily, we follow this branching strategy:

1. **`main`**: The Holy Grail. Only contains stable, working code. **Never commit directly to main**.
2. **`development`**: The central integration branch. All features merge here.
3. **Feature Branches**: Before coding, create a branch off of `development`. 
   * Example: `git checkout -b feature/login-system`
4. **Pull Requests (PR)**: When a feature is done, push your branch and open a PR into `development`. 

### Best Practices to Avoid Conflicts:
* Always `git pull origin development` before starting new work.
* Communicate with the team about who is editing which file (`index.php`, `navbar.php`, etc.).
* Write descriptive commit messages (e.g., `feat: added student jobs section`).

## 👥 Contributors

* [Ratul Ghosh](https://github.com/ratul003ghosh)
* *(Add other team members here)*
