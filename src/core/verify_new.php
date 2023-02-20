<?php
/*
 * Author: Dahir Muhammad Dahir
 * Date: 26-April-2020 5:44 PM
 * About: identification and verification
 * will be carried out in this file
 */


namespace fingerprint;

$start = microtime(true);

// echo "ehe";
require_once("../core/helpers/helpers.php");
require_once("../core/querydb.php");

if (!empty($post_datanya = $_POST["data"])) {
    // $jayParsedAry = [
    //     "id" => "1",
    //     "index_finger" => "AOg3Acgp43NcwEE381mKKyZdZ2YgQ0qX0nk2nzgcL8vRcLSXXeV77tjTpmOsq2IZP8nXLjkghtXbj5iSeO_tJEFlUeG3OTU2Z_kvXY5Hszt3Ep9psjKD2p_MQitPnYZxPesYvR-s_5Wlew5JHTvZa5-qJumizvhuZe1fdVjiKvdEH5VhIovwcM5gz_oNFYCCz9P5eGMwYIcMoJZoRNykmWfZs3Uxe7dpelQ8lWJKWlT9ZAt62rBxdGn-VZLDl_1N93hvdamJHk0v8n_axsbUWS1FeiUDXVoV0nsQWPuUDdLtFDUCQfz12HXQl4JX7JQ2luJ_YXMFcMoz0o4AgDQJXdaPk70I09dNugAOgYJEzfBqj61kpqolBwP9n7EVAXBeUS9TBBqGg3bC9bjXSYHhCrOo6HC_sW_qAfR0bwAA",
    //     "middle_finger" => []
    // ];

    header("content-type:application/json");

    // $user_data = $jayParsedAry;
    // $user_id = $user_data['id'];
    //this is not necessarily index_finger it could be
    //any finger we wish to identify
    // $pre_reg_fmd_string = $user_data['index_finger'][0];

    // $hand_data = json_decode(getUserFmds($user_id));
    $user_data = getAllUser();
    // echo json_encode($user_data);

    function getSiapa($userData, $inputFinger)
    {
        $userFound = null;
        foreach ($userData as $vall) {
            $json_response = verify_fingerprint($inputFinger, [
                "index_finger" => $vall['indexfinger'],
                "middle_finger" => ""
            ]);
            if (json_decode($json_response) == "match") {
                $userFound = $vall;
                break;
            }
        }
        return $userFound;
    }
    $check_data = getSiapa($user_data, $post_datanya);
    $result = !is_null($check_data);
    echo json_encode(["success" => $result, "message" => $result ? "A User Found" : "Finger not matched any.", "user_data" => $check_data ?? []]);
    // exit();
    // $enrolled_fingers = [
    //     "index_finger" => $hand_data[0]->indexfinger,
    //     "middle_finger" => $hand_data[0]->middlefinger
    // ];

    // $json_response = verify_fingerprint($pre_reg_fmd_string, $enrolled_fingers);
    // $response = json_decode($json_response);
    // if ($response === "match") {
    //     echo json_encode(["success" => true, "user_data" => json_decode(getUserDetails($user_id))]);
    // } else {
    //     echo json_encode(["success" => false, "user_data" => []]);
    // }
    // $time_elapsed_secs = microtime(true) - $start;
} else {
    echo json_encode(["success" => false, "message" => "Invalid Parameter", "user_data" => []]);
}
