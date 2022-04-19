-- CREATE DATABASE probation;

CREATE TABLE IF NOT EXISTS users (
    id INT NOT NULL,
    name VARCHAR DEFAULT NULL,
    PRIMARY KEY (id)
);

DELETE FROM users;

CREATE TABLE IF NOT EXISTS posts (
   id INT NOT NULL,
   author_id INT DEFAULT NULL,
   title VARCHAR DEFAULT NULL,
   PRIMARY KEY (id)
--    FOREIGN KEY (author_id) REFERENCES users (id) ON DELETE CASCADE ON UPDATE CASCADE
);

DELETE from posts;

INSERT INTO users
VALUES
       (1, 'User 1'),
       (2, 'User 2'),
       (3, 'User 3'),
       (4, NULL),
       (5, NULL);

INSERT INTO posts
VALUES
       (1, 1, 'User`s 1 post 1'),
       (2, 1, 'User`s 1 post 2'),
       (3, 1, NULL),
       (4, 2, 'User`s 2 post 1'),
       (5, 2, NULL),
       (6, 3, NULL),
       (7, 4, 'User`s 4 (NULL) post 1'),
       (8, 4, NULL),
       (9, 6, 'Unknown user`s post'),
       (10,6, NULL);


-- Users:
SELECT * FROM users;
-- +--+------+
-- |id|name  |
-- +--+------+
-- |1 |User 1|
-- |2 |User 2|
-- |3 |User 3|
-- |4 |NULL  |
-- |5 |NULL  |
-- +--+------+


-- Posts:
SELECT * FROM posts;
-- +--+---------+----------------------+
-- |id|author_id|title                 |
-- +--+---------+----------------------+
-- |1 |1        |User`s 1 post 1       |
-- |2 |1        |User`s 1 post 2       |
-- |3 |1        |NULL                  |
-- |4 |2        |User`s 2 post 1       |
-- |5 |2        |NULL                  |
-- |6 |3        |NULL                  |
-- |7 |4        |User`s 4 (NULL) post 1|
-- |8 |4        |NULL                  |
-- |9 |6        |Unknown user`s post   |
-- |10|6        |NULL                  |
-- +--+---------+----------------------+


-- LEFT JOIN
SELECT u.id as user_id, u.name as user_name, p.id as post_id, p.title as post_title
FROM users u
LEFT JOIN posts p on u.id = p.author_id;

-- Result:
-- +-------+---------+-------+----------------------+
-- |user_id|user_name|post_id|post_title            |
-- +-------+---------+-------+----------------------+
-- |1      |User 1   |1      |User`s 1 post 1       |
-- |1      |User 1   |2      |User`s 1 post 2       |
-- |1      |User 1   |3      |NULL                  |
-- |2      |User 2   |4      |User`s 2 post 1       |
-- |2      |User 2   |5      |NULL                  |
-- |3      |User 3   |6      |NULL                  |
-- |4      |NULL     |7      |User`s 4 (NULL) post 1|
-- |4      |NULL     |8      |NULL                  |
-- |5      |NULL     |NULL   |NULL                  |
-- +-------+---------+-------+----------------------+


-- LEFT OUTER JOIN
SELECT u.id as user_id, u.name as user_name, p.id as post_id, p.title as post_title
FROM users u
LEFT JOIN posts p on u.id = p.author_id
WHERE p.author_id IS NULL;

-- Result:
-- +-------+---------+-------+----------+
-- |user_id|user_name|post_id|post_title|
-- +-------+---------+-------+----------+
-- |5      |NULL     |NULL   |NULL      |
-- +-------+---------+-------+----------+


-- RIGHT JOIN
SELECT u.id as user_id, u.name as user_name, p.id as post_id, p.title as post_title
FROM users u
RIGHT JOIN posts p on u.id = p.author_id;

-- Result:
-- +-------+---------+-------+----------------------+
-- |user_id|user_name|post_id|post_title            |
-- +-------+---------+-------+----------------------+
-- |1      |User 1   |1      |User`s 1 post 1       |
-- |1      |User 1   |2      |User`s 1 post 2       |
-- |1      |User 1   |3      |NULL                  |
-- |2      |User 2   |4      |User`s 2 post 1       |
-- |2      |User 2   |5      |NULL                  |
-- |3      |User 3   |6      |NULL                  |
-- |4      |NULL     |7      |User`s 4 (NULL) post 1|
-- |4      |NULL     |8      |NULL                  |
-- |NULL   |NULL     |9      |Unknown user`s post   |
-- |NULL   |NULL     |10     |NULL                  |
-- +-------+---------+-------+----------------------+


-- RIGHT OUTER JOIN
SELECT u.id as user_id, u.name as user_name, p.id as post_id, p.title as post_title
FROM users u
RIGHT JOIN posts p on u.id = p.author_id
WHERE u.id IS NULL;

-- Result:
-- +-------+---------+-------+-------------------+
-- |user_id|user_name|post_id|post_title         |
-- +-------+---------+-------+-------------------+
-- |NULL   |NULL     |9      |Unknown user`s post|
-- |NULL   |NULL     |10     |NULL               |
-- +-------+---------+-------+-------------------+


-- INNER JOIN
SELECT u.id as user_id, u.name as user_name, p.id as post_id, p.title as post_title
FROM users u
INNER JOIN posts p on u.id = p.author_id;

-- Result:
-- +-------+---------+-------+----------------------+
-- |user_id|user_name|post_id|post_title            |
-- +-------+---------+-------+----------------------+
-- |1      |User 1   |1      |User`s 1 post 1       |
-- |1      |User 1   |2      |User`s 1 post 2       |
-- |1      |User 1   |3      |NULL                  |
-- |2      |User 2   |4      |User`s 2 post 1       |
-- |2      |User 2   |5      |NULL                  |
-- |3      |User 3   |6      |NULL                  |
-- |4      |NULL     |7      |User`s 4 (NULL) post 1|
-- |4      |NULL     |8      |NULL                  |
-- +-------+---------+-------+----------------------+


-- OUTER JOIN
SELECT u.id as user_id, u.name as user_name, p.id as post_id, p.title as post_title
FROM users u
FULL OUTER JOIN posts p on u.id = p.author_id
WHERE u.id IS NULL OR p.author_id IS NULL;

-- Result:
-- +-------+---------+-------+-------------------+
-- |user_id|user_name|post_id|post_title         |
-- +-------+---------+-------+-------------------+
-- |NULL   |NULL     |9      |Unknown user`s post|
-- |NULL   |NULL     |10     |NULL               |
-- |5      |NULL     |NULL   |NULL               |
-- +-------+---------+-------+-------------------+


-- FULL OUTER JOIN
SELECT u.id as user_id, u.name as user_name, p.id as post_id, p.title as post_title
FROM users u
FULL OUTER JOIN posts p on u.id = p.author_id;

-- Result:
-- +-------+---------+-------+----------------------+
-- |user_id|user_name|post_id|post_title            |
-- +-------+---------+-------+----------------------+
-- |1      |User 1   |1      |User`s 1 post 1       |
-- |1      |User 1   |2      |User`s 1 post 2       |
-- |1      |User 1   |3      |NULL                  |
-- |2      |User 2   |4      |User`s 2 post 1       |
-- |2      |User 2   |5      |NULL                  |
-- |3      |User 3   |6      |NULL                  |
-- |4      |NULL     |7      |User`s 4 (NULL) post 1|
-- |4      |NULL     |8      |NULL                  |
-- |NULL   |NULL     |9      |Unknown user`s post   |
-- |NULL   |NULL     |10     |NULL                  |
-- |5      |NULL     |NULL   |NULL                  |
-- +-------+---------+-------+----------------------+


-- CROSS JOIN
SELECT u.id as user_id, u.name as user_name, p.id as post_id, p.title as post_title
FROM users u
CROSS JOIN posts p;

-- Result:
-- 50 rows...
