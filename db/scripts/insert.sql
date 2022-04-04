use kanjo_racing;

SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE user;
TRUNCATE car;
TRUNCATE race;
TRUNCATE user_location;
TRUNCATE user_race_fk;
TRUNCATE waypoint;

INSERT INTO user (email, nickname, password)
VALUES ('testa@aa.ca', 'tester', 'asda');
INSERT INTO user (email, nickname, password)
VALUES ("test@q.xc", 'testear', 'asda');

INSERT INTO car (user_id, name, brand, hp, car_type)
VALUES (1, 'Denise', 'Toyota', '100', 'Corrola');
INSERT INTO car (user_id, name, brand, hp, car_type)
VALUES (1, 'Aray', 'Mazda', '110', 'MX-5');
INSERT INTO car (user_id, name, brand, hp, car_type)
VALUES (2, 'Gras', 'Toyota', '180', 'Yaris');
INSERT INTO car (user_id, name, brand, hp, car_type)
VALUES (2, 'Lisa', 'BMW', '180', 'X5');

INSERT INTO race (name, start_time, latitude, longitude, height)
VALUES ('Tohoku', '2022-4-20 11:01:01', '14.11', '12.11', '1.0');
INSERT INTO race (name, start_time, latitude, longitude, height)
VALUES ('Tohoku', '2022-4-25 4:00:00', '14.11', '12.11', '1.0');
INSERT INTO race (name, start_time, latitude, longitude, height)
VALUES ('Tohoku', '2022-4-30 1:00:00', '14.11', '12.11', '1.0');

INSERT INTO user_location (user_id, time, latitude, longitude, height)
VALUES (1, CURRENT_TIMESTAMP, 14.11, 12.11, 1.0);

INSERT INTO user_race_fk (race_id, user_id, car_id)
VALUES (1, 1, 1);

INSERT INTO waypoint (race_id, latitude, longitude, height)
VALUES (1, 14.11, 12.11, 1);

SET FOREIGN_KEY_CHECKS = 1;