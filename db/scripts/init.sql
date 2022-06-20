use kanjo_racing;

-- SQLINES LICENSE FOR EVALUATION USE ONLY
CREATE TABLE car
(
    id           INTEGER     NOT NULL AUTO_INCREMENT,
    user_id      INTEGER,
    name         VARCHAR(50) NOT NULL,
    brand        LONGTEXT    NOT NULL,
    hp           INTEGER     NOT NULL,
    vehicle_type LONGTEXT    NOT NULL,
    img_url      LONGTEXT,
    PRIMARY KEY (id)
) AUTO_INCREMENT = 1;

-- SQLINES LICENSE FOR EVALUATION USE ONLY
CREATE UNIQUE INDEX car__idx ON
    car (
         id
         ASC);

ALTER TABLE car
    ADD CONSTRAINT car_name_un UNIQUE (name);

ALTER TABLE car
    ADD CONSTRAINT car_id_un UNIQUE (id);

ALTER TABLE car
    ADD CONSTRAINT car_user_un UNIQUE (user_id,
                                       id);


-- SQLINES LICENSE FOR EVALUATION USE ONLY
CREATE TABLE race
(
    race_id       INTEGER      NOT NULL AUTO_INCREMENT,
    name          VARCHAR(100) NOT NULL,
    start_time    DATETIME(6)  NOT NULL,
    latitude      DOUBLE       NOT NULL,
    longitude     DOUBLE       NOT NULL,
    owner_id      INTEGER      NOT NULL,
    min_racers    INTEGER     DEFAULT NULL,
    max_racers    INTEGER     DEFAULT NULL,
    max_hp        INTEGER     DEFAULT NULL,
    password      VARCHAR(10) DEFAULT NULL,
    heat_grade    VARCHAR(30) DEFAULT NULL COMMENT 'Enum:
All laws respect
Offense Only
Traffic lights respect
No rules',
    min_req_karma SMALLINT    DEFAULT NULL,
    chat_link     LONGTEXT    DEFAULT NULL,
    img_url       LONGTEXT    DEFAULT NULL,
    laps          SMALLINT    DEFAULT NULL,
    PRIMARY KEY (race_id)
) AUTO_INCREMENT = 1;

ALTER TABLE race
    ADD CHECK ( heat_grade IN ('no_offence', 'no_rules', 'offences', 'only_traffic_lights') );

/* Moved to CREATE TABLE
COMMENT ON COLUMN race.heat_grade IS
    'Enum:
All laws respect
Offense Only
Traffic lights respect
No rules'; */

-- SQLINES LICENSE FOR EVALUATION USE ONLY
CREATE UNIQUE INDEX race__idx ON
    race (
          race_id
          ASC);

ALTER TABLE race
    ADD CONSTRAINT race_race_id_un UNIQUE (race_id);

ALTER TABLE race
    ADD CONSTRAINT race__un UNIQUE (name,
                                    start_time,
                                    latitude,
                                    longitude);

-- SQLINES LICENSE FOR EVALUATION USE ONLY
CREATE TABLE `user`
(
    user_id     INTEGER            NOT NULL AUTO_INCREMENT,
    email       VARCHAR(50)        NOT NULL,
    nickname    VARCHAR(50)        NOT NULL,
    password    LONGTEXT           NOT NULL,
    karma       SMALLINT DEFAULT 0 NOT NULL,
    session_pwd VARCHAR(40)        NOT NULL,
    PRIMARY KEY (user_id),
    UNIQUE (session_pwd)
) AUTO_INCREMENT = 1;

-- SQLINES LICENSE FOR EVALUATION USE ONLY
CREATE UNIQUE INDEX user__idx ON
    `user` (
            user_id
            ASC);

CREATE UNIQUE INDEX user__ssid ON
    `user` (
            session_pwd
            ASC);

ALTER TABLE `user`
    ADD CONSTRAINT user_user_id_un UNIQUE (user_id);

ALTER TABLE `user`
    ADD CONSTRAINT user_username_un UNIQUE (nickname);

ALTER TABLE `user`
    ADD CONSTRAINT user_nickname_un UNIQUE (email);

ALTER TABLE `user`
    ADD CONSTRAINT user_username_nickname_un UNIQUE (email,
                                                     nickname);

ALTER TABLE `user`
    ADD CONSTRAINT user_user_id_unv1 UNIQUE (user_id);

-- SQLINES LICENSE FOR EVALUATION USE ONLY
CREATE TABLE user_location
(
    user_id   INTEGER   NOT NULL,
    time      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    latitude  DOUBLE    NOT NULL,
    longitude DOUBLE    NOT NULL
);

-- SQLINES LICENSE FOR EVALUATION USE ONLY
CREATE UNIQUE INDEX user_location__idx ON
    user_location (
                   user_id
                   ASC,
                   time
                   ASC);

ALTER TABLE user_location
    ADD CONSTRAINT user_location_pk PRIMARY KEY (time,
                                                 user_id);

ALTER TABLE user_location
    ADD CONSTRAINT user_loc_id UNIQUE (user_id,
                                       time);


-- SQLINES LICENSE FOR EVALUATION USE ONLY
CREATE TABLE user_race_fk
(
    race_id   INTEGER NOT NULL,
    user_id   INTEGER NOT NULL,
    car_id    INTEGER NOT NULL,
    step      INTEGER DEFAULT 0,
    lap       INTEGER NOT NULL DEFAULT 1,
    pass_time TIMESTAMP DEFAULT NOW()
);

-- SQLINES LICENSE FOR EVALUATION USE ONLY
CREATE INDEX user_race_fk__idx ON
    user_race_fk (
                  user_id
                  ASC);

ALTER TABLE user_race_fk
    ADD CONSTRAINT user_race_fk__un UNIQUE (race_id,
                                            user_id,
                                            car_id);

-- SQLINES LICENSE FOR EVALUATION USE ONLY
CREATE TABLE waypoint
(
    race_id   INTEGER  NOT NULL,
    step      SMALLINT NOT NULL DEFAULT 1,
    latitude  DOUBLE   NOT NULL,
    longitude DOUBLE   NOT NULL,
    PRIMARY KEY (step, race_id)
);

-- SQLINES LICENSE FOR EVALUATION USE ONLY
CREATE UNIQUE INDEX waypoint__idx ON
    waypoint (
              race_id
              ASC,
              step
              ASC);


ALTER TABLE waypoint
    ADD CONSTRAINT waypoint__un UNIQUE (latitude,
                                        longitude);

ALTER TABLE car
    ADD CONSTRAINT car_user_fk FOREIGN KEY (user_id)
        REFERENCES `user` (user_id)
        ON DELETE CASCADE;

ALTER TABLE user_location
    ADD CONSTRAINT user_location_user_fk FOREIGN KEY (user_id)
        REFERENCES `user` (user_id)
        ON DELETE CASCADE;

ALTER TABLE user_race_fk
    ADD CONSTRAINT user_race_fk_car_fk FOREIGN KEY (car_id)
        REFERENCES car (id)
        ON DELETE CASCADE;

ALTER TABLE user_race_fk
    ADD CONSTRAINT user_race_fk_race_fk FOREIGN KEY (race_id)
        REFERENCES race (race_id)
        ON DELETE CASCADE;

ALTER TABLE user_race_fk
    ADD CONSTRAINT user_race_fk_user_fk FOREIGN KEY (user_id)
        REFERENCES `user` (user_id)
        ON DELETE CASCADE;

ALTER TABLE waypoint
    ADD CONSTRAINT waypoint_race_fk FOREIGN KEY (race_id)
        REFERENCES race (race_id)
        ON DELETE CASCADE;

