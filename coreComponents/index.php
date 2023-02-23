<?php
require_once('fingerprint__controller.php');

$servername = "172.16.1.9\Production";
$username = "sa";
$password = "web@ccess.1";
$database = "ERP_Production";
$pdo = new PDO(
    "sqlsrv:server=$servername;Database=$database",
    $username,
    $password,
    array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    )
);
try {
    
    $stmt = $pdo->prepare("select * from MsPOSMemberFP");
    $stmt->execute();
    $get_all_user = $stmt->fetchAll(PDO::FETCH_ASSOC);
    header("content-type:application/json");
    $selectedCluster = 1;
    $fpController = new FingerprintController($selectedCluster);
    foreach ($get_all_user as $user) {
        if (connection_aborted()) {
            exit;
        }
        print " "; //https://stackoverflow.com/a/2389804
        flush();
        ob_flush();
        $verified_index_finger = $fpController->verify_fingerprint('AOg4Acgp43NcwEE381mKK8FcZ2ZaAdRqojdOh5vN642S3TSMv6oFdYEYBlCOOVOpsvlxwAB4dXH_pmx9RxlkPULs7hBNFXe7LrFSHqKURueNrUUDwvxeV5VV4y5canRbVK02eSA-ihz881mnQzd3eedhT-1LX8p8kv4c-yo0cGa8IIq13RkqfBtXzkQ6MxwZEybUg0VUaFMeaWx7vsBjEJrSj6Ddzpo1mD8omRu6uASHQuqfnzuS1DzOUXELDe94MPKNOHRUVvWD1Fo0GGYsMfmrF63aXJtlZvf0PHl9eVCH7JhRVSraAvqEfD3Te08C0JjWO0Kf-tBlTO0nJxNDq9IPWraj-SIOu69lv18OMFSQXs2LQe14GOg2KA-hAeoX3UqebHfqWn0d3bB7zFJJn4HY6SdRyObbs-uqr28A', $user['FingerData']);
        if ($verified_index_finger !== "verification failed" && $verified_index_finger) {
            echo json_encode(["success" => true, "data" => $user, "cluster_no" => $selectedCluster]);
            return;
            exit();
            break;
        }
    }
    echo json_encode(["success" => false, "data_count" => count($get_all_user), "a" => $verified_index_finger ,"cluster_no" => $selectedCluster]);
    return;
} catch (PDOException $e) {
    echo ("Error connecting to SQL Server: " . $e->getMessage());
}
