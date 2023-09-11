<?php

/**
 * -=-<[ Bismillahirrahmanirrahim ]>-=-
 * Verify fingerprint
 * @authors Dahir Muhammad Dahir (dahirmuhammad3@gmail.com)
 * @date    2022-04-10 15:48:48
 * @version 1.0.0
 */

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

ignore_user_abort(TRUE);

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: X-Requested-With");

require_once("../coreComponents/basicRequirements.php");
require_once("db.php");


function getAllUser($clusterNumber = 1, $totalCluster = 1)
{
    if ($clusterNumber < 1 || $totalCluster < 1) throw new Error("Minimum selected cluster or total cluster number is 1");
    $myDatabase = new Database();
    $sql_query = "SELECT COUNT(*) AS total FROM vw_fingerlist WHERE indexfinger IS NOT NULL";
    $count_result = (int)$myDatabase->execute_count($sql_query);
    // error_log(json_encode(["INI" => $count_result]));
    $perCluster = round($count_result / $totalCluster);
    $offset = $clusterNumber == 1 ? 0 : ($perCluster * ($clusterNumber - 1));
    $sql_query_2 = "SELECT id, username, fullname,indexfinger FROM vw_fingerlist WHERE indexfinger IS NOT NULL LIMIT $perCluster OFFSET $offset";
    $fmds = $myDatabase->select2($sql_query_2);
    error_log($sql_query_2);
    error_log(json_encode(["result_data" => $fmds]));
    return $fmds;
}

header("content-type: application/json");
if (!empty($post_data = $_POST["data"]) || !empty($selectedCluster = $_POST["selected_cluster"]) || !empty($totalCluster = $_POST["total_cluster"])) {
    $selectedCluster = $_POST["selected_cluster"];
    $totalCluster = $_POST["total_cluster"];
    // $selectedCluster = 2;
    // $totalCluster = 2;
    $get_all_user = getAllUser($selectedCluster, $totalCluster);
    $fpController = new FingerprintController($selectedCluster);
    foreach ($get_all_user as $vall) {

        // error_log(connection_aborted() ? "CANCELED $selectedCluster" : "GAK CANCEL $selectedCluster");
        // switch (connection_status()) {
        //     case CONNECTION_NORMAL:
        //         $status = 'Normal';
        //         break;
        //     case CONNECTION_ABORTED:
        //         $status = 'User Abort';
        //         break;
        //     case CONNECTION_TIMEOUT:
        //         $status = 'Max Execution Time exceeded';
        //         break;
        //     case (CONNECTION_ABORTED & CONNECTION_TIMEOUT):
        //         $status = 'Aborted and Timed Out';
        //         break;
        //     default:
        //         $status = 'Unknown';
        //         break;
        // }

        // error_log("CONNECTION STATE $status $selectedCluster");
        if (connection_aborted()) {
            exit;
        }
        print " "; //https://stackoverflow.com/a/2389804
        flush();
        ob_flush();
        $verified_index_finger = $fpController->verify_fingerprint($post_data, $vall['indexfinger']);
        if ($verified_index_finger !== "verification failed" && $verified_index_finger) {
            echo json_encode(["success" => true, "data" => $vall, "cluster_no" => $selectedCluster]);
            return;
            exit();
            break;
        }
    }

    echo json_encode(["success" => false, "data_count" => count($get_all_user), "cluster_no" => $selectedCluster]);
    return;
} else {
    echo json_encode("error! no data or missing provided in post request");
}
