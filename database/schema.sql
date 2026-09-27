-- Esquema de la base de datos para MySQL / MariaDB (el sitio publicado).
-- Se puede ejecutar sobre una base de datos que ya existe: CREATE TABLE IF NOT EXISTS
-- solo crea las tablas que faltan y no toca las demás.
-- La versión para SQLite (desarrollo local) está en schema.sqlite.sql.
-- Explicación de cada tabla en docs/4-base-de-datos.md

-- Cuentas: el email y el hash de la contraseña (nunca la contraseña real).
CREATE TABLE IF NOT EXISTS users (
    user_id int NOT NULL AUTO_INCREMENT,
    email varchar(255) NOT NULL UNIQUE,
    password varchar(255) NOT NULL,
    PRIMARY KEY (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Datos personales de cada usuario. Por ahora solo se usan first_name y last_name.
CREATE TABLE IF NOT EXISTS user_profile (
    user_profile_id int NOT NULL AUTO_INCREMENT,
    user_id int,
    last_name varchar(50),
    first_name varchar(50),
    address varchar(100),
    quote varchar (255),
    city varchar(100),
    age tinyint,
    PRIMARY KEY (user_profile_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Los posts. created_at se guarda en UTC.
CREATE TABLE IF NOT EXISTS posts (
    post_id int NOT NULL AUTO_INCREMENT,
    user_id int NOT NULL,
    content varchar(280) NOT NULL,
    created_at datetime NOT NULL,
    PRIMARY KEY (post_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Una fila por cada like. La clave primaria (post_id, user_id) impide
-- que una persona le dé dos likes al mismo post.
CREATE TABLE IF NOT EXISTS likes (
    post_id int NOT NULL,
    user_id int NOT NULL,
    PRIMARY KEY (post_id, user_id),
    FOREIGN KEY (post_id) REFERENCES posts(post_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Los hashtags de cada post (en minúsculas y sin "#"), para las tendencias.
-- utf8mb4_bin compara exacto: sin esto MySQL trataría "fútbol" y "futbol" como iguales.
-- INDEX (tag) acelera la búsqueda de los posts de un hashtag.
CREATE TABLE IF NOT EXISTS post_hashtags (
    post_id int NOT NULL,
    tag varchar(100) COLLATE utf8mb4_bin NOT NULL,
    PRIMARY KEY (post_id, tag),
    INDEX (tag),
    FOREIGN KEY (post_id) REFERENCES posts(post_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
