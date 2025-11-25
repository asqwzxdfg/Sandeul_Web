CREATE TABLE board (
	id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    content TEXT NOT NULL,
    writer VARCHAR(100) NOT NULL,
    created_at DATETIME default NOW()
);