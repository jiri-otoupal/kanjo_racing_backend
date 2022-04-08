<?php

require_once "DB.php";
require_once realpath(dirname(__FILE__) . '/..') . "/../utils/utils.php";
require_once realpath(dirname(__FILE__) . '/..') . "/../consensus/config.php";

class Race extends DB
{
    protected $id;

    public function __construct($id = null)
    {
        parent::__construct();
        if ($id == null)
            return;
        $this->$id = $id;
    }

    public function getAll()
    {
        return $this->query("SELECT * FROM race");
    }


    public function getRace($id)
    {
        if ($id == null)
            return null;

        $escaped_id = $this->escape($id);
        $res = $this->query
        ("SELECT race_id FROM race WHERE race_id='$escaped_id'");

        if (!empty($res))
            return $res;
        return null;
    }

    public function addWaypoint($raceId, $step, $lat, $lng)
    {
        $escaped_raceId = $this->escape($raceId);
        $escaped_step = $this->escape($step);
        $escaped_lat = $this->escape($lat);
        $escaped_lng = $this->escape($lng);

        return $this->non_return_query("INSERT INTO waypoint 
                                                 (race_id, step, latitude, longitude) VALUES 
                                                                 ('$escaped_raceId','$escaped_step','$escaped_lat','$escaped_lng')");
    }

    public function add($rid, $name, $start_time, $lat, $lng, $owner_id, $min_r, $max_r, $max_hp, $password, $heat_grade, $min_karma, $chat_link, $img_url = null)
    {
        $escaped_rid = $this->escape($rid);
        $escaped_name = $this->escape($name);
        $escaped_start_time = $this->escape($start_time);
        $escaped_lat = $this->escape($lat);
        $escaped_lng = $this->escape($lng);
        $escaped_owner_id = $this->escape($owner_id);
        $escaped_min_r = $this->escape($min_r);
        $escaped_max_r = $this->escape($max_r);
        $escaped_max_hp = $this->escape($max_hp);
        $escaped_password = $this->escape($password);
        $escaped_heat_grade = $this->escape($heat_grade);
        $escaped_min_karma = $this->escape($min_karma);
        $escaped_chat_link = $this->escape($chat_link);
        $escaped_img_url = $this->escape($img_url);

        $prepared = $this->connection->prepare("INSERT INTO race
        VALUES 
               (?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
        $prepared->bind_param("issddiiiississ", $escaped_rid, $escaped_name, $escaped_start_time, $escaped_lat, $escaped_lng, $escaped_owner_id,
            $escaped_min_r, $escaped_max_r, $escaped_max_hp, $escaped_password, $escaped_heat_grade,
            $escaped_min_karma, $escaped_chat_link, $escaped_img_url);

        return $prepared->execute();
    }

    public function modifyRace($race_id, $name, $start_time, $lat, $lng, $owner_id, $min_r, $max_r, $max_hp, $password, $heat_grade, $min_karma, $chat_link, $img_url = null)
    {
        //TODO: Merge duplicity
        $escaped_rid = $this->escape($race_id);
        $escaped_name = $this->escape($name);
        $escaped_start_time = $this->escape($start_time);
        $escaped_lat = $this->escape($lat);
        $escaped_lng = $this->escape($lng);
        $escaped_owner_id = $this->escape($owner_id);
        $escaped_min_r = $this->escape($min_r);
        $escaped_max_r = $this->escape($max_r);
        $escaped_max_hp = $this->escape($max_hp);
        $escaped_password = $this->escape($password);
        $escaped_heat_grade = $this->escape($heat_grade);
        $escaped_min_karma = $this->escape($min_karma);
        $escaped_chat_link = $this->escape($chat_link);
        $escaped_img_url = $this->escape($img_url);

        $prepared = $this->connection->prepare("UPDATE race SET  
                race_id=?,name=?,start_time=?,
                latitude=?,longitude=?,owner_id=?,min_racers=?,
                             max_racers=?,max_hp=?,password=?,
                             heat_grade=?,min_req_karma=?,
                             chat_link=?,img_url=?
             WHERE race_id=?");
        $prepared->bind_param("issddiiiississi", $escaped_rid, $escaped_name, $escaped_start_time, $escaped_lat, $escaped_lng, $escaped_owner_id,
            $escaped_min_r, $escaped_max_r, $escaped_max_hp, $escaped_password, $escaped_heat_grade,
            $escaped_min_karma, $escaped_chat_link, $escaped_img_url, $escaped_rid);

        return $prepared->execute();
    }

    public function deleteRace($id)
    {
        $escaped_id = $this->escape($id);
        return $this->non_return_query("DELETE FROM race WHERE race_id='$escaped_id'");
    }
}