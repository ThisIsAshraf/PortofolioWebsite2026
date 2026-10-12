# Shihab Portfolio CMS — PHP + MySQL (InfinityFree-ready)

This version converts the Flask portfolio to plain PHP and MySQL so it can run on a shared host that supports PHP/MySQL, including InfinityFree. It does not require Python, Composer, Node.js, or a background server process.

## Included features

- Responsive public portfolio: profile, photo, skills, work experience, projects, education, and contact form.
- Admin login with password hashing, session regeneration, CSRF tokens, and protected admin pages.
- One-time admin account setup page.
- Admin dashboard with counts and recent messages.
- Edit profile text and upload a profile photo.
- Create, edit, hide/show, and delete skills, experience, projects, and education.
- View contact form submissions, update message status, and open a reply in the user's email app.
- MySQL database schema and starter content in `sql/schema.sql`.
- Contact submissions are saved in `contact_messages`. They do **not** automatically email you.

## Deploy to InfinityFree

1. Create an InfinityFree hosting account and hosting website/subdomain.
2. In the control panel, create a MySQL database. Copy its exact **hostname, database name, username, and password**. InfinityFree database names and usernames are often prefixed; use the full values shown in the panel.
3. Open phpMyAdmin from the hosting panel. Select the database you created and import `sql/schema.sql`.
4. Edit `config.php` locally or through File Manager. Replace `YOUR_MYSQL_HOST`, `YOUR_DATABASE_NAME`, `YOUR_DATABASE_USERNAME`, and `YOUR_DATABASE_PASSWORD` with the values shown by InfinityFree. Keep `SITE_BASE_PATH` blank if the site is directly inside `htdocs`. If you install into a subfolder, set it to that folder path, for example `/portfolio`.
5. Upload the **contents** of this project folder into `htdocs` using the InfinityFree File Manager or FTP. The main `index.php` must be directly inside `htdocs` for a root installation. Upload folders such as `admin`, `assets`, `sql`, and `uploads` too.
6. Visit `https://YOUR-SITE/setup.php`. Create a unique admin username and a strong password of at least 12 characters.
7. **Immediately delete `setup.php`** using File Manager after creating your account.
8. Visit `https://YOUR-SITE/admin/login.php` and sign in. Open **Profile & Photo** first to update your information and upload a photo.
9. Test the public contact form, then open **Admin → Messages** to verify that the message is saved.
10. Enable HTTPS/SSL for the domain if it is available in your hosting dashboard. Always use HTTPS when signing into the admin panel.

## Important notes

- The website is editable through the admin panel; you should not need to edit PHP to update your profile content.
- The contact form stores messages in the MySQL table `contact_messages`. It does not send email notifications. The admin inbox is the place to read submissions.
- Do not share `config.php` or your database credentials publicly.
- Keep a backup of your database before making major changes.
- The included `.htaccess` files disable directory listing and block direct access to PHP files in `uploads`. If your host reports an `.htaccess` directive error, remove only the unsupported directive and retest; do not remove application authentication or database protections.
- The provided starter content reflects the information available for the portfolio draft. Review all dates, wording, and claims in the admin panel before publishing.

## Database tables

- `site_settings`: public profile fields and uploaded photo filename.
- `admin_users`: hashed admin credentials.
- `skills`, `experience`, `projects`, `education`: dynamic portfolio content with visibility controls.
- `contact_messages`: submitted contact form messages and their statuses.

## Local testing (optional)

Requires PHP 8+, PDO MySQL, and a MySQL/MariaDB server. Configure `config.php`, import `sql/schema.sql`, then use PHP's built-in development server from the project directory:

```bash
php -S 127.0.0.1:8000
```

Open `http://127.0.0.1:8000/setup.php` once, create an admin, then delete/disable the setup page before exposing the site to others. The built-in server is for development, not production hosting.
