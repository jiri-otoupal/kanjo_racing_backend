<?php

require "DB.php";

class User extends DB
{
    protected $id = null;

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

    public function login($email, $password)
    {
        $escaped_email = $this->escape($email);

        $res = $this->query("SELECT password FROM user WHERE email='$escaped_email'");

        if (empty($res))
            return false;

        if (password_verify($password, $res["password"]))
            return true;
        return false;
    }

    public function register($email, $nickname, $password)
    {
        $escaped_email = $this->escape($email);
        $escaped_nickname = $this->escape($nickname);

        $hashed_pwd = password_hash($password, PASSWORD_DEFAULT);

        $res = $this->non_return_query("INSERT INTO user (email, nickname, password) VALUES
                                                   ('$escaped_email', '$escaped_nickname', '$hashed_pwd')");

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

    /**
     * @param $escaped_email
     * @return array|false|null
     */
    private function getIdOfEmail($escaped_email)
    {
        $ids = $this->query("SELECT user_id FROM user WHERE email='$escaped_email'");
        return $ids;
    }

    public function commit()
    {
        $this->connection->commit();
    }
}