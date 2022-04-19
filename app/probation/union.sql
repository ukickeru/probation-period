CREATE TABLE IF NOT EXISTS cities (
    id INT NOT NULL GENERATED ALWAYS AS IDENTITY,
    name VARCHAR DEFAULT NULL,
    PRIMARY KEY (id)
);

DELETE FROM cities;

INSERT INTO cities (name)
VALUES
       ('Moscow'),
       ('Saint-Petersburg'),
       ('Moscow'),
       ('Saint-Petersburg'),
       ('Krasnodar'),
       ('N`sk'),
       (NULL);

-- Cities:
SELECT * FROM cities;
-- +--+----------------+
-- |id|name            |
-- +--+----------------+
-- |1 |Moscow          |
-- |2 |Saint-Petersburg|
-- |3 |Moscow          |
-- |4 |Saint-Petersburg|
-- |5 |Krasnodar       |
-- |6 |N`sk            |
-- |7 |NULL            |
-- +--+----------------+


-- Union example
SELECT 'first' as "column"
UNION
SELECT 'second';

-- Result:
-- +------+
-- |column|
-- +------+
-- |first |
-- |second|
-- +------+


-- Select duplicated cities:
SELECT c1.name
FROM cities c1
JOIN cities c2 on c1.name = c2.name
WHERE c1.id <> c2.id;
-- Result:
-- +----------------+
-- |name            |
-- +----------------+
-- |Moscow          |
-- |Moscow          |
-- |Saint-Petersburg|
-- |Saint-Petersburg|
-- +----------------+

-- UNION with SELECT clause:
SELECT name
FROM cities
WHERE name LIKE 'Moscow'
UNION
SELECT 'second_row';

-- Result:
-- +----------+
-- |name      |
-- +----------+
-- |second_row|
-- |Moscow    |
-- +----------+

-- UNION ALL with SELECT clause:
SELECT name
FROM cities
WHERE name LIKE 'Moscow'
UNION ALL
SELECT 'second_row';

-- Result:
-- +----------+
-- |name      |
-- +----------+
-- |Moscow    |
-- |Moscow    |
-- |second_row|
-- +----------+

-- 2 different UNIONs:
SELECT name
FROM cities
WHERE name LIKE 'Moscow'
UNION ALL
SELECT 'second_row'
UNION
SELECT name
FROM cities
WHERE name LIKE 'Saint-Petersburg';

-- Result (last UNION changed mode to DISTINCT):
-- +----------------+
-- |name            |
-- +----------------+
-- |second_row      |
-- |Moscow          |
-- |Saint-Petersburg|
-- +----------------+

-- 2 same UNIONs:
SELECT name
FROM cities
WHERE name LIKE 'Moscow'
UNION ALL
SELECT 'second_row'
UNION ALL
SELECT name
FROM cities
WHERE name LIKE 'Saint-Petersburg';

-- Result (all UNIONs worked in ALL mode):
-- +----------------+
-- |name            |
-- +----------------+
-- |Moscow          |
-- |Moscow          |
-- |second_row      |
-- |Saint-Petersburg|
-- |Saint-Petersburg|
-- +----------------+

-- 2 same UNIONs with global LIMIT:
SELECT name
FROM cities
WHERE name LIKE 'Moscow'
UNION ALL
SELECT 'second_row'
UNION ALL
SELECT name
FROM cities
WHERE name LIKE 'Saint-Petersburg'
LIMIT 2;

-- Result (fetched first 2 rows):
-- +----------------+
-- |name            |
-- +----------------+
-- |Moscow          |
-- |Moscow          |
-- +----------------+

-- 2 same UNIONs with local LIMIT:
SELECT name
FROM cities
WHERE name LIKE 'Moscow'
UNION ALL
SELECT 'second_row'
UNION ALL
(SELECT name
FROM cities
WHERE name LIKE 'Saint-Petersburg'
LIMIT 1);

-- Result (fetched only 1 Saint-Petersburg):
-- +----------------+
-- |name            |
-- +----------------+
-- |Moscow          |
-- |Moscow          |
-- |second_row      |
-- |Saint-Petersburg|
-- +----------------+

-- 2 same UNIONs with ORDERs:
SELECT id, name
FROM cities
WHERE name LIKE 'Moscow'
UNION ALL
SELECT 0, 'second_row' -- Query requires 2 columns!
UNION ALL
(
    SELECT id, name
    FROM cities
    WHERE name LIKE 'Saint-Petersburg'
    ORDER BY id
)
ORDER BY name;

-- Result:
-- +--+----------------+
-- |id|name            |
-- +--+----------------+
-- |1 |Moscow          |
-- |3 |Moscow          |
-- |2 |Saint-Petersburg|
-- |4 |Saint-Petersburg|
-- |0 |second_row      |
-- +--+----------------+
