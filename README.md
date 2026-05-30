# 👋 Welcome to Jibika (জীবিকা)

Hey there! Thanks for dropping by the **Jibika** repository. 

We built Jibika because we realized something important: finding a job in Bangladesh shouldn't be so hard, especially for people outside the standard corporate bubble. Whether you're a university student looking for part-time work, a daily wage earner looking for jobs right in your own neighborhood, or a small business owner trying to find local talent—Jibika is designed to connect you.

It's essentially a smart, area-based job matching and unemployment monitoring system.

## ✨ What makes Jibika special?

Unlike standard job boards, we focus heavily on **local, community-driven employment**.

*   **For Job Seekers:** You don't just search for a job; the platform recommends jobs based on your skills and your exact District, Upazila, or Ward. We even built highly visible quick-filters specifically for **Student Jobs** and **Day Laborers** because we know how important it is to find flexible work fast.
*   **For Employers:** Whether you run a large garments factory or a small local shop, you get a clean, premium dashboard to manage applicants and post roles easily.
*   **For the Big Picture:** Government admins and NGOs can use the platform's data to actually see which specific areas are struggling with unemployment and what skills the local people have, helping them plan better training programs.

Oh, and we also included a bunch of free resources like an interactive CV writing guide, interview tips, and entrepreneur support right on the homepage!

## 🛠️ How we built it

We wanted to keep things solid, fast, and accessible:
*   **Frontend:** Standard HTML, CSS, and some JavaScript to make things feel alive (we used Bootstrap 5 to keep the design clean and responsive).
*   **Backend:** Good old PHP to handle the logic, routing, and user sessions.
*   **Database:** MySQL (you'll find the `jibika_db.sql` file right here in the repo).

## 🚀 Want to run it yourself?

It's super easy to get it running on your local machine.

1.  **Download the code:** Just clone this repo or download the ZIP file.
2.  **Set up your database:** Open up XAMPP, WAMP, or MAMP, go to phpMyAdmin, and create a new database called `jibika`. Then, import the `jibika_db.sql` file that is included in this repository.
3.  **Check the connection:** Open the `main/assets/config/db.php` file and just make sure your database username and password match your local setup (usually it's `root` with a blank password).
4.  **Run it!** Drop the project folder into your local server (like `htdocs`), open your browser, and go to `http://localhost/Jibika-Project/main/`.

That's it! Enjoy exploring the portal.

---
*Built with ❤️ to help modernize the local employment ecosystem in Bangladesh.*
