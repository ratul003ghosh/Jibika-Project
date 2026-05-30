# 👋 Welcome to Jibika (জীবিকা)

Hey there! Thanks for dropping by the **Jibika** repository. 

While Jibika looks like a standard job portal on the surface, its true purpose is much bigger. We built this platform primarily as a **powerful tool for the Government and NGOs** to actively monitor and solve unemployment in Bangladesh at a hyper-local level.

Instead of just guessing where jobs are needed, Jibika provides real, area-based data (down to the specific District, Upazila, and Ward). It is designed to bridge the massive gap between policymakers, local employers, and the everyday workforce.

## ✨ Why Jibika exists

We wanted to create an ecosystem that actually helps the country grow from the grassroots up:

*   **For the Government & Policymakers:** This is the core of Jibika. Admins can log in and see exactly which areas are struggling with unemployment and what specific skills the local people have. This means the government can make actual data-driven decisions rather than shooting in the dark.
*   **For NGOs & Trainers:** If an NGO wants to launch a sewing or IT training program, they can use Jibika to see exactly which Ward or Upazila has a high concentration of unemployed youth who actually want those skills.
*   **For Local Employers:** Whether you run a garments factory, a local startup, or just need a daily wage worker for construction, Jibika makes it incredibly easy to find local people. We specifically built categories for **Day Laborers** and **Student Part-time** workers because they are the backbone of the local economy.
*   **For the People:** Job seekers don't just get a job board. They get a platform that recommends jobs based on their exact location. Plus, we provide them with free resources like CV guides, interview tips, and direct links to government E-Services and entrepreneurship support.

## 🛠️ How we built it

We wanted to keep the technology accessible, fast, and easy to deploy:
*   **Frontend:** Standard HTML, CSS, and vanilla JavaScript (powered by Bootstrap 5 for a clean, responsive, government-standard design).
*   **Backend:** Good old PHP to handle the heavy lifting, routing, and user session logic.
*   **Database:** MySQL. We structured the database heavily around location mapping and user roles (you'll find the `jibika_db.sql` file right here in the repo).

## 🚀 Want to run it yourself?

It's super easy to get it running on your local machine to test it out.

1.  **Download the code:** Just clone this repo or download the ZIP file.
2.  **Set up your database:** Open up XAMPP, WAMP, or MAMP, go to phpMyAdmin, and create a new database called `jibika`. Then, import the `jibika_db.sql` file that is included in this repository.
3.  **Check the connection:** Open the `main/assets/config/db.php` file and just make sure your database username and password match your local setup (usually it's `root` with a blank password).
4.  **Run it!** Drop the project folder into your local server (like `htdocs`), open your browser, and go to `http://localhost/Jibika-Project/main/`.

Thanks for checking out Jibika! 

---
*Built with ❤️ to empower the government, NGOs, and the people to modernize the local employment ecosystem in Bangladesh.*
