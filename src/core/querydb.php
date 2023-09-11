<?php
/*
 * Author: Dahir Muhammad Dahir
 * Date: 26-April-2020 12:41 AM
 * About: this file is responsible
 * for all Database queries
 */

namespace fingerprint;

require_once("../core/Database.php");

function setUserFmds($user_id, $index_finger_fmd_string)
{
    $myDatabase = new Database();
    /* INSERT INTO table_name (column1, column2, column3, ...)
VALUES (value1, value2, value3, ...);
 */
    $sql_query = "insert into users (indexfinger) VALUES (?)";
    // $sql_query = "update users set indexfinger=? where id=?";
    $param_type = "s";
    $param_array = [$index_finger_fmd_string];
    $affected_rows = $myDatabase->update($sql_query, $param_type, $param_array);

    if ($affected_rows > 0) {
        return "success";
    } else {
        return "failed in querydb";
    }
}

function getUserFmds($user_id)
{
    $myDatabase = new Database();
    $sql_query = "select indexfinger, middlefinger from users where id=?";
    $param_type = "i";
    $param_array = [$user_id];
    $fmds = $myDatabase->select($sql_query, $param_type, $param_array);
    return json_encode($fmds);
}

function getAllUser()
{
    $myDatabase = new Database();
    $sql_query = "SELECT id, username, fullname, indexfinger FROM users WHERE indexfinger IS NOT NULL LIMIT 100";
    $fmds = $myDatabase->select($sql_query);
    return $fmds;
}

function getUserDetails($user_id)
{
    $myDatabase = new Database();
    $sql_query = "select username, fullname from users where id=?";
    $param_type = "i";
    $param_array = [$user_id];
    $user_info = $myDatabase->select($sql_query, $param_type, $param_array);
    return json_encode($user_info);
}

function getAllFmds()
{
    $myDatabase = new Database();
    $sql_query = "select indexfinger, middlefinger from users where indexfinger != ''";
    $allFmds = $myDatabase->select($sql_query);
    return json_encode($allFmds);
}
