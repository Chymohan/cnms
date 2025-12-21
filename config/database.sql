CREATE DATABASE cnms;
USE cnms;

CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE,
    role ENUM('admin','teacher','student') NOT NULL,
    password VARCHAR(255) NOT NULL,
    profile_image VARCHAR(255),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE batches (
    batch_id INT AUTO_INCREMENT PRIMARY KEY,
    batch_year VARCHAR(10) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE students (
    student_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL UNIQUE,
    batch_id INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (batch_id) REFERENCES batches(batch_id) ON DELETE CASCADE
);


CREATE TABLE notices (
    notice_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    category ENUM('Academic','Exams','Events','Administration','General') NOT NULL,
    status ENUM('Draft','Published') DEFAULT 'Draft',
    attachment VARCHAR(255),
    created_by INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(user_id) ON DELETE CASCADE
);

INSERT INTO users (name, email, role, password)
VALUES
('Admin User', 'admin@gmail.com', 'admin', '$2y$10$q/VnS3lvq/YXNPxwv.WZg.kgYbnAYbxyWoQ0erR2LRZdi7/2Wl1GK'),
('Teacher User', 'teacher@gmail.com', 'teacher', '$2y$10$mt1mbU6Pi430F5suvN6kt.anOhk1f3Z2EszdnrDhwgVvFNqd14LwO'),
('Student User', 'student@gmail.com', 'student', '$2y$10$LH8ZRSh9zBAdpNDFaD8c7OyQf2A9nqUkk/eQJWSmSN53IJIi/98MG');

INSERT INTO batches (batch_year)
VALUES
('2023'),
('2024'),
('2025');

INSERT INTO students (user_id, batch_id)
VALUES
(3, 1);

INSERT INTO notices (title, description, category, status, created_by)
VALUES
(
 'Semester Exam Notice',
 'Semester exams will start from March 15.',
 'Exams',
 'Published',
 1
);

INSERT INTO notices (title, description, category, status, created_by)
VALUES
(
 'Project Proposal Submission',
 'Submit Project Proposal by next Monday.',
 'Academic',
 'Draft',
 1
);

INSERT INTO notices (title, description, category, status, attachment, created_by)
VALUES
(
 'Holiday Notice',
 'College will remain closed tomorrow.',
 'Administration',
 'Published',
 'holiday.pdf',
 1
);
