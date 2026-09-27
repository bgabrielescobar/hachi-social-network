-- Esquema de la base de datos para SQLite (desarrollo local, DB_DRIVER=sqlite).
-- Se ejecuta solo en cada conexión (PDOClass::connectSqlite): CREATE TABLE IF NOT EXISTS
-- solo crea las tablas que faltan, así que no borra datos.
-- Son las mismas tablas que schema.sql (MySQL), escritas con los tipos de SQLite.

CREATE TABLE IF NOT EXISTS users (
    user_id INTEGER PRIMARY KEY AUTOINCREMENT,
    email TEXT NOT NULL UNIQUE,
    password TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS user_profile (
    user_profile_id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER REFERENCES users(user_id) ON DELETE CASCADE,
    last_name TEXT,
    first_name TEXT,
    address TEXT,
    quote TEXT,
    city TEXT,
    age INTEGER
);

CREATE TABLE IF NOT EXISTS posts (
    post_id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL REFERENCES users(user_id) ON DELETE CASCADE,
    content TEXT NOT NULL,
    created_at TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS likes (
    post_id INTEGER NOT NULL REFERENCES posts(post_id) ON DELETE CASCADE,
    user_id INTEGER NOT NULL REFERENCES users(user_id) ON DELETE CASCADE,
    PRIMARY KEY (post_id, user_id)
);

CREATE TABLE IF NOT EXISTS post_hashtags (
    post_id INTEGER NOT NULL REFERENCES posts(post_id) ON DELETE CASCADE,
    tag TEXT NOT NULL,
    PRIMARY KEY (post_id, tag)
);

CREATE INDEX IF NOT EXISTS post_hashtags_tag ON post_hashtags (tag);
