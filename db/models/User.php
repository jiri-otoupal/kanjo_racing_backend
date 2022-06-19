<?php

require_once "DB.php";
require_once realpath(dirname(__FILE__) . '/..') . "/../utils/utils.php";
require_once realpath(dirname(__FILE__) . '/..') . "/../consensus/config.php";

class User extends DB
{
    protected $id = null;
    protected $sessionPWD = null;

    /**
     * @return null
     */
    public function getSessionPWD()
    {
        return $this->sessionPWD;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param $email
     */
    public function __construct($email = null)
    {
        parent::__construct();
        if ($email == null)
            return;

        $escaped_email = $this->escape($email);

        $ids = $this->getIdOfEmail($escaped_email);

        if (!empty($ids))
            $this->id = $ids["user_id"];
    }

    /**
     * @param $session_pwd
     * @return bool
     */
    public function auth($session_pwd)
    {
        $ssid = hash("sha1", $session_pwd);
        $res = $this->query("SELECT user_id FROM user WHERE session_pwd='$ssid'");

        if (empty($res))
            return false;

        $this->id = $res["user_id"];
        return true;
    }

    /**
     * @param $email
     * @param $password
     * @return bool
     */
    public function login($email, $password)
    {
        $escaped_email = $this->escape($email);

        $res = $this->query("SELECT user_id,password FROM user WHERE email='$escaped_email'");

        if (empty($res))
            return false;

        if (password_verify($password, $res["password"])) {
            $this->id = $res["user_id"];
            $randomPassword = randomPassword(16);
            setcookie("session_id", $randomPassword, COOKIE_EXPIRE, "/");

            $_COOKIE["session_id"] = $randomPassword;

            $this->setSessionPWD($randomPassword);
            return true;
        }
        return false;
    }

    /**
     * @param $email
     * @param $nickname
     * @param $password
     * @return array|false|null
     */
    public function register($email, $nickname, $password)
    {
        $escaped_email = $this->escape($email);
        $escaped_nickname = $this->escape($nickname);

        $hashed_pwd = password_hash($password, PASSWORD_DEFAULT);
        $randomPassword = randomPassword(16);

        $res = $this->non_return_query("INSERT INTO user (email, nickname, password,session_pwd) VALUES
                                                   ('$escaped_email', '$escaped_nickname', '$hashed_pwd','$randomPassword')");

        if ($res == false)
            return false;

        return $this->getIdOfEmail($escaped_email);
    }

    /**
     * @param $name
     * @param $brand
     * @param $hp
     * @param $vehicle_type
     * @param $img_url
     * @return array
     */
    protected final function escapeCarParams($name, $brand, $hp, $vehicle_type, $img_url = null)
    {
        $escaped_name = $this->escape($name);
        $escaped_brand = $this->escape($brand);
        $escaped_hp = $this->escape($hp);
        $escaped_vehicle_type = $this->escape($vehicle_type);
        $escaped_img_url = $this->escape($img_url);

        $escaped = array();
        $escaped["name"] = $escaped_name;
        $escaped["brand"] = $escaped_brand;
        $escaped["hp"] = $escaped_hp;
        $escaped["vehicle_type"] = $escaped_vehicle_type;
        $escaped["img_url"] = $escaped_img_url;

        return $escaped;
    }

    /**
     * @param $name
     * @param $brand
     * @param $hp
     * @param $vehicle_type
     * @param $img_url
     * @return bool|mysqli_result
     */
    public function addCar($name, $brand, $hp, $vehicle_type, $img_url = null)
    {
        $escaped_name = $this->escape($name);
        $escaped_brand = $this->escape($brand);
        $escaped_hp = $this->escape($hp);
        $escaped_car_type = $this->escape($vehicle_type);
        $escaped_img_url = $this->escape($img_url);

        return $this->non_return_query("INSERT INTO car (user_id, name, brand, hp, vehicle_type,img_url) 
                VALUES ('$this->id', '$escaped_name', '$escaped_brand', '$escaped_hp', '$escaped_car_type','$escaped_img_url')");
    }

    /**
     * @param $id
     * @return bool|mysqli_result
     */
    public function deleteCar($id)
    {
        $escaped_id = $this->escape($id);
        return $this->non_return_query("DELETE FROM car WHERE id='$escaped_id'");
    }

    /**
     * @param $car_id
     * @param $name
     * @param $brand
     * @param $hp
     * @param $vehicle_type
     * @param $img_url
     * @return bool|mysqli_result
     */
    public function modifyCar($car_id, $name, $brand, $hp, $vehicle_type, $img_url = null)
    {
        $escaped_id = $this->escape($car_id);
        $escaped_name = $this->escape($name);
        $escaped_brand = $this->escape($brand);
        $escaped_hp = $this->escape($hp);
        $escaped_car_type = $this->escape($vehicle_type);
        $escaped_img_url = $this->escape($img_url);

        return $this->non_return_query("UPDATE car SET  
                user_id='$this->id', name='$escaped_name', brand='$escaped_brand', hp='$escaped_hp', vehicle_type='$escaped_car_type',
               img_url='$escaped_img_url' WHERE id='$escaped_id'");
    }

    /**
     * @param $latitude
     * @param $longitude
     * @return bool|mysqli_result
     */
    public function addLocation($latitude, $longitude)
    {
        $escaped_lat = $latitude;
        $escaped_lng = $longitude;

        //$this->non_return_query("DELETE FROM user_location WHERE time < NOW() - INTERVAL 1 MINUTE");

        return $this->non_return_query("INSERT INTO user_location (user_id,latitude,longitude) 
                VALUES ('$this->id','$escaped_lat','$escaped_lng')");
    }

    /**
     * @param $race_id
     * @return array|false|null
     */
    public function getNextWaypoint($race_id)
    {
        $escaped_race_id = $this->escape($race_id);

        return $this->query("SELECT w.* FROM user_race_fk 
            JOIN waypoint w on user_race_fk.step = w.step WHERE w.race_id='$escaped_race_id' AND user_id='$this->id'");
    }

    /**
     * @param $race_id
     * @return array|false|null
     */
    public function getUserLocations($race_id)
    {
        $escaped_race_id = $this->escape($race_id);
        return $this->query("SELECT user_location.*, u2.nickname,u.step
            FROM user_location
                     RIGHT JOIN user_race_fk u on user_location.user_id = u.user_id
                     JOIN user u2 on u.user_id = u2.user_id WHERE race_id='$escaped_race_id' 
            GROUP BY user_id
            HAVING MAX(time) ORDER BY step DESC");
    }

    /**
     * @return array|null
     */
    public function getCars()
    {
        if ($this->id == null)
            return null;

        $res = $this->query
        ("SELECT car.* FROM car JOIN user u on u.user_id = car.user_id
                                    WHERE u.user_id='$this->id'");

        if (!empty($res))
            return $res;
        return null;
    }

    /**
     * @param $car_id
     * @return array|null
     */
    public function getCar($car_id)
    {
        if ($this->id == null)
            return null;

        $escaped_id = $this->escape($car_id);
        $res = $this->query
        ("SELECT car.* FROM car JOIN user u on u.user_id = car.user_id
                                    WHERE u.user_id='$this->id' AND car.id='$escaped_id'");

        if (!empty($res))
            return $res;
        return null;
    }

    /**
     * @param $sessionPwd
     * @return bool|mysqli_result
     */
    public function setSessionPWD($sessionPwd)
    {
        $this->sessionPWD = $sessionPwd;
        $ssid = hash("sha1", $sessionPwd);
        return $this->non_return_query("UPDATE user SET session_pwd='$ssid' WHERE user_id='$this->id'");
    }

    /**
     * @param $escaped_email
     * @return array|false|null
     */
    private function getIdOfEmail($escaped_email)
    {
        return $this->query("SELECT user_id FROM user WHERE email='$escaped_email'");
    }

    /**
     * @return array|null
     */
    public function getProfile()
    {
        if ($this->id == null)
            return null;

        $res = $this->query("SELECT * FROM user WHERE user_id='$this->id'");

        if (!empty($res))
            return $res;
        return null;
    }

    /**
     * @return array|null
     */
    public function getRaces()
    {
        if ($this->id == null)
            return null;

        $res = $this->query
        ("SELECT race.* FROM race JOIN user_race_fk urf on race.race_id = urf.race_id JOIN user u on u.user_id = urf.user_id WHERE u.user_id='$this->id'");

        if (!empty($res))
            return $res;
        return null;
    }
}