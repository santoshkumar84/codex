CREATE DATABASE IF NOT EXISTS coding_portal CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE coding_portal;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(120) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin','student') NOT NULL DEFAULT 'student',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE problems (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description TEXT NOT NULL,
    input_format TEXT NOT NULL,
    output_format TEXT NOT NULL,
    constraints_text TEXT NOT NULL,
    sample_input TEXT NOT NULL,
    sample_output TEXT NOT NULL,
    difficulty ENUM('Easy','Medium','Hard') NOT NULL,
    created_by INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE test_cases (
    id INT AUTO_INCREMENT PRIMARY KEY,
    problem_id INT NOT NULL,
    input_data TEXT NOT NULL,
    expected_output TEXT NOT NULL,
    is_sample TINYINT(1) DEFAULT 0,
    FOREIGN KEY (problem_id) REFERENCES problems(id) ON DELETE CASCADE
);

CREATE TABLE submissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    problem_id INT NOT NULL,
    language ENUM('c','cpp','java','python','php') NOT NULL,
    source_code MEDIUMTEXT NOT NULL,
    result ENUM('Accepted','Wrong Answer','Runtime Error','Time Limit Exceeded') NOT NULL,
    result_details TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (problem_id) REFERENCES problems(id) ON DELETE CASCADE,
    INDEX idx_user_problem (user_id, problem_id)
);

CREATE TABLE tags (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE
);

CREATE TABLE problem_tags (
    problem_id INT NOT NULL,
    tag_id INT NOT NULL,
    PRIMARY KEY (problem_id, tag_id),
    FOREIGN KEY (problem_id) REFERENCES problems(id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE
);

INSERT INTO users (full_name, email, password_hash, role) VALUES
('Admin User', 'admin@codearena.local', '$2y$10$F5D0CzY9Xnm0DZKO8x7fDuxs1M5fS5T91x5Q0w5Q9LhWgXzVS7kW2', 'admin');
-- password for admin row above: admin123

INSERT INTO problems (title, description, input_format, output_format, constraints_text, sample_input, sample_output, difficulty, created_by)
VALUES (
    'Two Sum',
    'Given an array of integers and a target value, return indices of two numbers such that they add up to target. Output indices in ascending order.',
    'First line: n (size of array).\nSecond line: n integers.\nThird line: target.',
    'Two space-separated indices i j where arr[i] + arr[j] = target.',
    '2 <= n <= 10^5\n-10^9 <= arr[i], target <= 10^9',
    '4\n2 7 11 15\n9',
    '0 1',
    'Easy',
    1
);

INSERT INTO tags (name) VALUES ('Array'), ('HashMap');
INSERT INTO problem_tags (problem_id, tag_id)
SELECT 1, id FROM tags WHERE name IN ('Array', 'HashMap');

INSERT INTO test_cases (problem_id, input_data, expected_output, is_sample) VALUES
(1, '4\n2 7 11 15\n9', '0 1', 1),
(1, '5\n3 2 4 9 1\n6', '1 2', 0),
(1, '6\n-1 4 10 3 7 8\n11', '1 4', 0);
