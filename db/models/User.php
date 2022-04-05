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

    public function auth($session_pwd)
    {
        $ssid = hash("sha1", $session_pwd);
        $res = $this->query("SELECT user_id FROM user WHERE session_pwd='$ssid'");

        if (empty($res))
            return false;

        $this->id = $res["user_id"];
        return true;
    }

    public function login($email, $password)
    {
        $escaped_email = $this->escape($email);

        $res = $this->query("SELECT user_id,password FROM user WHERE email='$escaped_email'");

        if (empty($res))
            return false;

        if (password_verify($password, $res["password"])) {
            $this->id = $res["user_id"];
            $randomPassword = randomPassword(16);
            session_set_cookie_params(COOKIE_EXPIRE);
            if (isset($_SESSION["session_id"]))
                session_destroy();
            session_start();

            $_SESSION["session_id"] = $randomPassword;

            $this->setSessionPWD($randomPassword);
            return true;
        }
        return false;
    }

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

    public function addCar($name, $brand, $hp, $car_type, $img_url = null)
    {
        $escaped_name = $this->escape($name);
        $escaped_brand = $this->escape($brand);
        $escaped_hp = $this->escape($hp);
        $escaped_car_type = $this->escape($car_type);
        $escaped_img_url = $this->escape($img_url);

        return $this->non_return_query("INSERT INTO car (user_id, name, brand, hp, car_type,img_url) 
                VALUES ('$this->id', '$name', '$brand', '$hp', '$car_type','$img_url')");
    }

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

    public function commit()
    {
        $this->connection->commit();
    }

    public function getProfile()
    {
        if ($this->id == null)
            return null;

        $res = $this->query("SELECT * FROM user WHERE user_id='$this->id'");

        if (!empty($res))
            return $res;
        return null;
    }

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