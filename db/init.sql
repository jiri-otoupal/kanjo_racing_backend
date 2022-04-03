use kanjo_racing;

CREATE TABLE car (
                     user_id  INTEGER NOT NULL,
                     car_id   INTEGER NOT NULL,
                     name     VARCHAR(50) NOT NULL,
                     brand    LONGTEXT NOT NULL,
                     hp       INTEGER NOT NULL,
                     car_type LONGTEXT NOT NULL
);

ALTER TABLE car ADD CONSTRAINT car_pk PRIMARY KEY ( car_id,
                                                    user_id );

ALTER TABLE car ADD CONSTRAINT car_name_un UNIQUE ( name );


CREATE TABLE race (
                      race_id    INTEGER NOT NULL,
                      name       VARCHAR(100) NOT NULL,
                      start_time DATETIME(6) NOT NULL,
                      latitude   DOUBLE NOT NULL,
                      longitude DOUBLE NOT NULL,
                      height     DOUBLE NOT NULL,
                      min_racers INTEGER,
                      max_racers INTEGER,
                      max_hp     INTEGER
);


CREATE UNIQUE INDEX race__idx ON
    race (
          race_id
          ASC );

ALTER TABLE race ADD CONSTRAINT race_pk PRIMARY KEY ( race_id );

ALTER TABLE race
    ADD CONSTRAINT race__un UNIQUE ( name,
                                     start_time,
                                     latitude,
                                     longitude,
                                     height );

ALTER TABLE race ADD CONSTRAINT race_race_id_un UNIQUE ( race_id );


CREATE TABLE `User` (
                        user_id  INTEGER NOT NULL,
                        username VARCHAR(50) NOT NULL,
                        password LONGTEXT NOT NULL,
                        nickname VARCHAR(50) NOT NULL
);



ALTER TABLE `User` ADD CONSTRAINT user_pk PRIMARY KEY ( user_id );

ALTER TABLE `User` ADD CONSTRAINT user_user_id_un UNIQUE ( user_id );

ALTER TABLE `User` ADD CONSTRAINT user_user_id_unv1 UNIQUE ( user_id );

ALTER TABLE `User` ADD CONSTRAINT user_username_nickname_un UNIQUE ( username,
                                                                     nickname );


CREATE TABLE user_location (
                               time       DATETIME(6) NOT NULL,
                               latitude   DOUBLE NOT NULL,
                               longitude DOUBLE NOT NULL,
                               height     DOUBLE NOT NULL,
                               user_id    INTEGER NOT NULL
);

ALTER TABLE user_location ADD CONSTRAINT user_location_pk PRIMARY KEY ( time,
                                                                        user_id );

ALTER TABLE user_location
    ADD CONSTRAINT user_loc_id UNIQUE ( user_id,
                                        time );


CREATE TABLE user_race_fk (
                              race_id INTEGER NOT NULL,
                              user_id INTEGER NOT NULL
);

ALTER TABLE user_race_fk ADD CONSTRAINT user_race_fk__un UNIQUE ( race_id,
                                                                  user_id );


CREATE TABLE waypoint (
                          race_id    INTEGER NOT NULL,
                          step       DECIMAL(38) NOT NULL,
                          latitude   DOUBLE NOT NULL,
                          longitude DOUBLE NOT NULL,
                          height     DOUBLE NOT NULL
);

ALTER TABLE waypoint
    ADD CONSTRAINT waypoint_pk PRIMARY KEY ( step,
                                             race_id );

ALTER TABLE waypoint ADD CONSTRAINT waypoint_loc_uq UNIQUE ( latitude,longitude,height);

ALTER TABLE car
    ADD CONSTRAINT car_user_fk FOREIGN KEY ( user_id )
        REFERENCES `User` ( user_id )
        ON DELETE CASCADE;

ALTER TABLE user_location
    ADD CONSTRAINT user_location_user_fk FOREIGN KEY ( user_id )
        REFERENCES `User` ( user_id )
        ON DELETE CASCADE;

ALTER TABLE user_race_fk
    ADD CONSTRAINT user_race_fk_race_fk FOREIGN KEY ( race_id )
        REFERENCES race ( race_id )
        ON DELETE CASCADE;

ALTER TABLE user_race_fk
    ADD CONSTRAINT user_race_fk_user_fk FOREIGN KEY ( user_id )
        REFERENCES `User` ( user_id )
        ON DELETE CASCADE;

ALTER TABLE waypoint
    ADD CONSTRAINT waypoint_race_fk FOREIGN KEY ( race_id )
        REFERENCES race ( race_id )
        ON DELETE CASCADE;