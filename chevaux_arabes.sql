-- SQL Database Schema for Tunisian Arabian Horses System
-- Compatible with SQLite and adaptable to MySQL

-- 1. Table for Owners (Propriétaires)
CREATE TABLE IF NOT EXISTS owner (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nom VARCHAR(100) NOT NULL,
    telephone VARCHAR(20),
    email VARCHAR(100),
    adresse VARCHAR(200)
);

-- 2. Table for Jockeys
CREATE TABLE IF NOT EXISTS jockey (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nom VARCHAR(100) NOT NULL,
    nationalite VARCHAR(50) DEFAULT 'Tunisienne',
    experience_annees INTEGER DEFAULT 0
);

-- 3. Table for Horses (Chevaux)
-- Contains self-referencing foreign keys for father (pere_id) and mother (mere_id) to construct pedigrees
CREATE TABLE IF NOT EXISTS cheval (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nom VARCHAR(100) NOT NULL,
    race VARCHAR(50) NOT NULL, -- e.g., Pur-Sang Arabe, Barbe, Arabe-Barbe
    sexe VARCHAR(10) NOT NULL, -- Mâle, Femelle
    date_naissance DATE,
    robe VARCHAR(50), -- e.g., Alezan, Bai, Gris, Noir
    pere_id INTEGER NULL,
    mere_id INTEGER NULL,
    owner_id INTEGER NULL,
    image_url VARCHAR(255) NULL,
    FOREIGN KEY (pere_id) REFERENCES cheval(id) ON DELETE SET NULL,
    FOREIGN KEY (mere_id) REFERENCES cheval(id) ON DELETE SET NULL,
    FOREIGN KEY (owner_id) REFERENCES owner(id) ON DELETE SET NULL
);

-- 4. Table for Races (Courses)
CREATE TABLE IF NOT EXISTS course (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nom VARCHAR(150) NOT NULL,
    date_course DATE NOT NULL,
    lieu VARCHAR(100) NOT NULL, -- e.g., Hippodrome de Ksar Saïd
    distance INTEGER NOT NULL, -- in meters, e.g. 1600, 2000
    prix_millimes INTEGER DEFAULT 0 -- prize money in Millimes or Dinars
);

-- 5. Table for Race Participations (Horses in Races)
CREATE TABLE IF NOT EXISTS participation (
    cheval_id INTEGER,
    course_id INTEGER,
    jockey_id INTEGER NULL,
    classement INTEGER NULL, -- e.g. 1 for winner, 2 for second, etc.
    PRIMARY KEY (cheval_id, course_id),
    FOREIGN KEY (cheval_id) REFERENCES cheval(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES course(id) ON DELETE CASCADE,
    FOREIGN KEY (jockey_id) REFERENCES jockey(id) ON DELETE SET NULL
);
