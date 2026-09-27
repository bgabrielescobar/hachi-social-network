-- MySQL schema. Safe to run on an existing database: tables that already exist are skipped.

CREATE TABLE IF NOT EXISTS users (
    user_id int NOT NULL AUTO_INCREMENT,
    email varchar(255) NOT NULL UNIQUE,
    password varchar(255) NOT NULL,
    PRIMARY KEY (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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

CREATE TABLE IF NOT EXISTS posts (
    post_id int NOT NULL AUTO_INCREMENT,
    user_id int NOT NULL,
    content varchar(280) NOT NULL,
    created_at datetime NOT NULL,
    PRIMARY KEY (post_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS likes (
    post_id int NOT NULL,
    user_id int NOT NULL,
    PRIMARY KEY (post_id, user_id),
    FOREIGN KEY (post_id) REFERENCES posts(post_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS post_hashtags (
    post_id int NOT NULL,
    tag varchar(100) COLLATE utf8mb4_bin NOT NULL,
    PRIMARY KEY (post_id, tag),
    INDEX (tag),
    FOREIGN KEY (post_id) REFERENCES posts(post_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
