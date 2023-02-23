<?php

/**
 * this file uses the enrollment class to
 * enroll users
 * @authors Dahir Muhammad Dahir (dahirmuhammad3@gmail.com)
 * @date    2020-04-18 14:28:39
 */

require_once("basicRequirements.php");
header("content-type:application/json");
if (!empty($_POST["data"])) {
    $user_data = json_decode($_POST["data"]);

    $index_finger_string_array = $user_data->index_finger;
    $member_code = $user_data->id;
    $finger_type = $user_data->finger_type;
    // $middle_finger_string_array = $user_data->middle_finger;

    $enrolled_index_finger = enroll_fingerprint($index_finger_string_array);
    // $enrolled_middle_finger = enroll_fingerprint($middle_finger_string_array);
    // error_log(json_encode(["waduc" => $enrolled_index_finger]));
    if ($enrolled_index_finger !== false) {
        # todo: return the enrolled fmds instead
        // $output = ["enrolled_index_finger" => $enrolled_index_finger];
        // $data = [
        //     ':member_code' => $member_code,
        // ];
        // $sql = "DELETE FROM MsPOSMemberFP WHERE MemberCode = :member_code";
        // $stmt = $pdo->prepare($sql);
        // $stmt->execute($data);

        $sql = "insert into MsPOSMemberFP (MemberCode, FingerType, FingerData, CreatedBy, CreatedDate) values (:member_code, :finger_type, :finger_data, :created_by, :created_date)";

        $statement = $pdo->prepare($sql);

        $statement->execute([
            ':member_code' => $member_code,
            ':finger_type' => $finger_type,
            ':finger_data' => $enrolled_index_finger,
            ':created_by' => "mby",
            ':created_date' => date('Y-m-d H:i:s'),
        ]);

        $publisher_id = $pdo->lastInsertId();

        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false]);
    }
} else {
    echo json_encode(["success" => false, "message" => "error! no data provided in post request"]);
}
