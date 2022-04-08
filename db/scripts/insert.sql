use kanjo_racing;

SET FOREIGN_KEY_CHECKS = 0;


INSERT INTO user (email, nickname, password,session_pwd)
VALUES ('testa@aa.ca', 'tester', 'asda','aqq');
INSERT INTO user (email, nickname, password,session_pwd)
VALUES ("test@q.xc", 'testear', 'asda','aasd');

INSERT INTO car (user_id, name, brand, hp, vehicle_type)
VALUES (1, 'Denise', 'Toyota', '100', 'Corrola');
INSERT INTO car (user_id, name, brand, hp, vehicle_type)
VALUES (1, 'Aray', 'Mazda', '110', 'MX-5');
INSERT INTO car (user_id, name, brand, hp, vehicle_type)
VALUES (2, 'Gras', 'Toyota', '180', 'Yaris');
INSERT INTO car (user_id, name, brand, hp, vehicle_type)
VALUES (2, 'Lisa', 'BMW', '180', 'X5');

INSERT INTO race (name, start_time, latitude, longitude, owner_id)
VALUES ('Tohoku', '2022-4-20 11:01:01', '14.11', '12.11', '1');
INSERT INTO race (name, start_time, latitude, longitude, owner_id)
VALUES ('Tohoku-ku', '2022-4-25 4:00:00', '14.11', '12.11', '1');
INSERT INTO race (name, start_time, latitude, longitude, owner_id)
VALUES ('Tohoku-wa', '2022-4-30 1:00:00', '14.11', '12.11', '2');

INSERT INTO user_location (user_id, time, latitude, longitude)
VALUES (1, CURRENT_TIMESTAMP, 14.11, 12.11);

INSERT INTO user_race_fk (race_id, user_id, car_id)
VALUES (1, 1, 1);

INSERT INTO user_race_fk (race_id, user_id, car_id)
VALUES (1, 2, 1);


INSERT INTO waypoint (race_id, latitude, longitude)
VALUES (1, 14.11, 12.11);

SET FOREIGN_KEY_CHECKS = 1;