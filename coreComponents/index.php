<?php


require_once('fingerprint__controller.php');

$servername = "172.16.1.9\Production";
$username = "sa";
$password = "web@ccess.1";
$database = "ERP_Production";
try {
    $pdo = new PDO(
        "sqlsrv:server=$servername;Database=$database",
        $username,
        $password,
        array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        )
    );
    $stmt = $pdo->prepare("select * from MsPOSMemberFP");
    $stmt->execute();
    $get_all_user = $stmt->fetchAll(PDO::FETCH_ASSOC);
    header("content-type:application/json");
    // echo json_encode($get_all_user);
    $selectedCluster = 1;
    $fpController = new FingerprintController($selectedCluster);
    foreach ($get_all_user as $user) {
        if (connection_aborted()) {
            exit;
        }
        print " "; //https://stackoverflow.com/a/2389804
        flush();
        ob_flush();
        $verified_index_finger = $fpController->verify_fingerprint($user['FingerData'], $user['FingerData']);
        if ($verified_index_finger !== "verification failed" && $verified_index_finger) {
            echo json_encode(["success" => true, "data" => $user, "cluster_no" => $selectedCluster]);
            return;
            exit();
            break;
        }
        // echo $user['MemberCode'] . " - " .  $user['FingerType'] . " - " . $user['FingerData'];
    }

    echo json_encode(["success" => false, "data_count" => count($get_all_user), "a" => $verified_index_finger ,"cluster_no" => $selectedCluster]);
    return;
} catch (PDOException $e) {
    echo ("Error connecting to SQL Server: " . $e->getMessage());
}
