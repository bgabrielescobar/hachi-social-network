Hachi social network (Small twitter type)

Overview: 
 
Small social network application to practice PHP/HTML/CSS/SQL.

Users can create an account, log in, publish short posts (up to 280 characters), like posts, see the posts of every user in a shared timeline and follow the trending hashtags of the week.


Features

  * Sign up / log in / log out (passwords stored with `password_hash`, "Remember me" keeps the session for 30 days).
  * Timeline with the latest 50 posts, newest first.
  * Write posts of up to 280 characters with a live character counter (Ctrl + Enter publishes).
  * Like / unlike posts.
  * Delete your own posts.
  * User page (`home.php?user=ID`) with the posts of one user, open it clicking a name.
  * Hashtags: `#words` in a post become links to `home.php?tag=word` with every post that uses it.
  * Trends this week: the 5 hashtags used in more posts during the last 7 days.


Use case :
  * Like user i need account to made login on the app.
  * Like user i need post personal states.
  * Like user i need watch other users post and interactive with her/his (Likes).
  * To do: follow users, upload photos, comments.


Run it locally

No MySQL needed, it can use SQLite:

  1. Copy `.env.example` to `.env` and set `DB_DRIVER=sqlite` (and `APP_DEBUG=1` to see errors).
  2. Run `php -S localhost:8000` in the project folder.
  3. Open http://localhost:8000

The database file (`database/hachi.sqlite`) and its tables are created automatically.


Deploy (MySQL)

  1. Run `database/schema.sql` in your MySQL database. It only creates the tables that are missing, so it is safe to run on an existing database.
  2. Copy `.env.example` to `.env`, fill `HOST`, `DB_NAME`, `USER_NAME` and `PASSWORD` and keep `DB_DRIVER=mysql`.

`.env` is not saved in git, so your passwords stay out of the repository.

The `.htaccess` file blocks public access to `.env`, the `App` code and the database files on Apache servers.


Arquitecture

  * `index.php`, `home.php`, `login.php`, `register.php`, `post.php`, `logout.php`: entry points, each one runs `App/Bootstrap/Bootstrap.php`.
  * The Bootstrap loads `.env`, checks the session and runs the controller with the same name as the file (`home.php` -> `App/Controller/HomeController.php`).
  * Pages: the controller loads the data, the module (`App/Module`) prepares it and renders the views (`App/View`).
  * JSON endpoints (`login.php`, `register.php`, `post.php`) are called with `fetch` from `public/js-min`.
  * Database access lives in `App/Helpers/Database/Tables`.


Data model

  * `users`: email and password hash.
  * `user_profile`: first and last name of each user.
  * `posts`: text of each post, its author and creation date (UTC).
  * `likes`: one row per user that liked a post.
  * `post_hashtags`: hashtags of each post (lowercase, without `#`), used for the trends.


Prices:
  Free
  
Host: https://hachi-sn.000webhostapp.com/index.php
