# Visitime Tourism Website (PHP + MySQL)

A starter tourism directory website with public place listings, search/filtering, a one-day trip planner, contact form, and an admin area for managing places.

## Requirements
- PHP 8.0+ (PHP 8.1+ recommended)
- MySQL 5.7+ / MariaDB
- Apache (XAMPP is suitable)

## Setup with XAMPP
1. Extract `visitime_web_app.zip` into `C:\xampp\htdocs\visitime` (Windows) or your XAMPP `htdocs` folder.
2. Start **Apache** and **MySQL** from XAMPP.
3. Open phpMyAdmin and create/import the database:
   - Select **Import** and import `database.sql`, or run its SQL in the SQL tab.
   - The database name in the script is `visitime`.
4. Edit `config.php` if your MySQL username/password differs from the default:
   - default host: `localhost`
   - database: `visitime`
   - username: `root`
   - password: empty
5. Visit `http://localhost/visitime/setup_admin.php` once to create the initial admin account:
   - Username: `admin`
   - Password: `ChangeMe123!`
6. **Immediately change the default password** after logging in, and then delete `setup_admin.php` from the server.
7. Open `http://localhost/visitime/`.

## Admin
- Login: `http://localhost/visitime/login.php`
- Manage places: `http://localhost/visitime/admin.php`
- Add/edit place: use the Add Place or Edit buttons.
- Place fields: name, image, category, short description, distance, Google Maps location URL.
- Uploads are stored in `uploads/`. The directory must be writable by PHP.


