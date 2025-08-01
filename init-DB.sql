--Создание базы данных inline
CREATE DATABASE IF NOT EXISTS inline
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

-- Использование созданной БД
USE inline;

--Создание таблицы users(можно не создавать, т.к. я не увидил в этом необходимости)
CREATE TABLE IF NOT EXISTS users (
	userId INT NOT NULL AUTO_INCREMENT,
	name VARCHAR(255) NOT NULL,
	email VARCHAR(255) NOT NULL,
	password VARCHAR(255) NOT NULL,
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (userId),
	UNIQUE KEY (email)
) ENGINE=InnoDB;

--Создание таблицы articles
CREATE TABLE IF NOT EXISTS articles (
	id INT NOT NULL AUTO_INCREMENT,
	userId INT NOT NULL,
	title VARCHAR(255) NOT NULL,
	body TEXT NOT NULL,
	PRIMARY KEY (id),
	FOREIGN KEY (userId) REFERENCES users(userId)
) ENGINE=InnoDB;

--Создание таблицы comments
CREATE TABLE comments (
	id INT NOT NULL AUTO_INCREMENT,
	postId INT NOT NULL,
	name VARCHAR(255) NOT NULL,
	email VARCHAR(255) NOT NULL,
	body TEXT NOT NULL,
	PRIMARY KEY (id),
	FOREIGN KEY (postId) REFERENCES articles(id)
) ENGINE=InnoDB;
