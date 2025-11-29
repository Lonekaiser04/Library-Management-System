CREATE TABLE `books` (
   `id` int NOT NULL AUTO_INCREMENT,
   `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
   `author` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
   `category` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
   `isbn` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
   `quantity` int NOT NULL DEFAULT '1',
   `available` int NOT NULL DEFAULT '1',
   `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
   `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
   PRIMARY KEY (`id`),
   UNIQUE KEY `isbn` (`isbn`),
   KEY `idx_category` (`category`),
   KEY `idx_author` (`author`),
   KEY `idx_isbn` (`isbn`)
 ) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
 
 CREATE TABLE `students` (
   `student_id` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
   `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
   `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
   `phone` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
   `address` text COLLATE utf8mb4_unicode_ci,
   `course` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
   `semester` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
   `join_date` date NOT NULL,
   `books_borrowed` int DEFAULT '0',
   `user_id` int DEFAULT NULL,
   `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
   `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
   PRIMARY KEY (`student_id`),
   UNIQUE KEY `email` (`email`),
   KEY `idx_email` (`email`),
   KEY `idx_user_id` (`user_id`)
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
 
 CREATE TABLE `transactions` (
   `id` int NOT NULL AUTO_INCREMENT,
   `book_id` int NOT NULL,
   `student_id` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
   `issue_date` date NOT NULL,
   `due_date` date NOT NULL,
   `return_date` date DEFAULT NULL,
   `status` enum('issued','returned') COLLATE utf8mb4_unicode_ci DEFAULT 'issued',
   `fine` decimal(10,2) DEFAULT '0.00',
   `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
   `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
   PRIMARY KEY (`id`),
   KEY `idx_book_id` (`book_id`),
   KEY `idx_student_id` (`student_id`),
   KEY `idx_status` (`status`),
   KEY `idx_issue_date` (`issue_date`),
   CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE,
   CONSTRAINT `transactions_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE
 ) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
 
 CREATE TABLE `users` (
   `id` int NOT NULL AUTO_INCREMENT,
   `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
   `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
   `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
   `full_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
   `role` enum('admin','student') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'student',
   `student_id` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
   `admin_id` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
   `temp_password` tinyint(1) DEFAULT '1',
   `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
   `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
   PRIMARY KEY (`id`),
   UNIQUE KEY `username` (`username`),
   UNIQUE KEY `email` (`email`),
   KEY `idx_username` (`username`),
   KEY `idx_email` (`email`),
   KEY `idx_role` (`role`),
   KEY `idx_student_id` (`student_id`),
   KEY `idx_admin_id` (`admin_id`),
   CONSTRAINT `users_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE SET NULL
 ) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
 
 
INSERT INTO books (title, author, category, isbn, quantity, available)
VALUES
('The Alchemist', 'Paulo Coelho', 'Fiction', '9780061122415', 10, 10),

('Harry Potter and the Philosopher\'s Stone', 'J.K. Rowling', 'Fantasy', '9780747532743', 12, 12),

('Atomic Habits', 'James Clear', 'Self-Help', '9780735211292', 8, 8),

('Think and Grow Rich', 'Napoleon Hill', 'Self-Help', '9781937879501', 6, 6),

('Clean Code', 'Robert C. Martin', 'Programming', '9780132350884', 5, 5),

('Introduction to Algorithms', 'Cormen, Leiserson, Rivest, Stein', 'Computer Science', '9780262033848', 4, 4),

('Python Crash Course', 'Eric Matthes', 'Programming', '9781593276034', 7, 7),

('Computer Networking: A Top-Down Approach', 'James F. Kurose, Keith W. Ross', 'Networking', '9780133594140', 6, 6),

('Artificial Intelligence: A Modern Approach', 'Stuart Russell, Peter Norvig', 'AI & ML', '9780136042594', 3, 3),

('The Psychology of Money', 'Morgan Housel', 'Finance', '9780857197689', 10, 10),

('Sapiens: A Brief History of Humankind', 'Yuval Noah Harari', 'History', '9780062316097', 5, 5),

('The Fault in Our Stars', 'John Green', 'Romance', '9780525478812', 8, 8),

('The Da Vinci Code', 'Dan Brown', 'Thriller', '9780307474278', 9, 9),

('Fortress of the Muslim (Hisnul Muslim)', 'Sa\'id bin Wahf Al-Qahtani', 'Islamic', '9789960892641', 10, 10),

('Stories of the Prophets', 'Ibn Kathir', 'Islamic History', '9786035000305', 6, 6),

('The Sealed Nectar (Ar-Raheeq Al-Makhtum)', 'Safiur Rahman Mubarakpuri', 'Seerah', '9781591440710', 5, 5),

('The Hunger Games', 'Suzanne Collins', 'Dystopian Fiction', '9780439023481', 7, 7),

('Deep Work', 'Cal Newport', 'Productivity', '9781455586691', 5, 5),

('Cracking the Coding Interview', 'Gayle Laakmann McDowell', 'Coding Interview', '9780984782857', 6, 6);