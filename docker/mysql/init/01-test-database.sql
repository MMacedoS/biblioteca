CREATE DATABASE IF NOT EXISTS biblioteca_test
    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

GRANT ALL PRIVILEGES ON biblioteca_test.* TO 'app'@'%';
FLUSH PRIVILEGES;