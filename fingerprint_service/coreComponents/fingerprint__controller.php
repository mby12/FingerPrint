<?php
require_once(__DIR__ . "/" . "../vendor/autoload.php");

class FingerprintController
{
    private $client;
    // private $clusterArray = ["fingerprint_engine:4134", "fingerprint_engine_2:4134"];
    public function __construct($cluster = 1)
    {
        // $clusterArray = explode("|", getenv("CLUSTER_LIST"));
        // error_log(json_encode(["Cluster Array" => $clusterArray]));

        // if (!in_array($cluster, array_keys($clusterArray))) throw new Error("Invalid cluster id");
        $this->client = new Fingerprint\FingerPrintClient("fingerprint-engine-services-fingerprint_compare_engine-$cluster:4134", [
            "credentials" => Grpc\ChannelCredentials::createInsecure(),
        ]);
    }


    public function enroll_fingerprint($pre_fmd_string_array)
    {
        $enrollment_request = new Fingerprint\EnrollmentRequest();

        $pre_enrolled_fmds = array();

        foreach ($pre_fmd_string_array as $pre_reg_fmd) {
            $pre_enrollment_fmd = new Fingerprint\PreEnrolledFMD();
            $pre_enrollment_fmd->setBase64PreEnrolledFMD($pre_reg_fmd);
            array_push($pre_enrolled_fmds, $pre_enrollment_fmd);
        }

        $enrollment_request->setFmdCandidates($pre_enrolled_fmds);

        list($enrolled_fmd, $status) = $this->client->EnrollFingerprint($enrollment_request)->wait();

        if ($status->code === Grpc\STATUS_OK) {
            return $enrolled_fmd->getBase64EnrolledFMD();
        } else {
            return "enrollment failed";
        }
    }

    function check_duplicate($pre_fmd_string, $enrolled_fmd_string_list)
    {
        $pre_enrolled_fmd = new Fingerprint\PreEnrolledFMD(array("base64PreEnrolledFMD" => $pre_fmd_string));
        $verification_request = new Fingerprint\VerificationRequest(array("targetFMD" => $pre_enrolled_fmd));

        $enrolled_fmds = array();

        foreach ($enrolled_fmd_string_list as $hand) {
            array_push($enrolled_fmds, new Fingerprint\EnrolledFMD(array("base64EnrolledFMD" => $hand->indexfinger)));
            array_push($enrolled_fmds, new Fingerprint\EnrolledFMD(array("base64EnrolledFMD" => $hand->middlefinger)));
        }

        $verification_request->setFmdCandidates($enrolled_fmds);

        list($response, $status) = $this->client->CheckDuplicate($verification_request)->wait();
        return $response->getIsDuplicate();
    }

    function verify_fingerprint($pre_enrolled_fmd_string, $enrolled_fmd_string)
    {
        $pre_enrolled_fmd = new Fingerprint\PreEnrolledFMD();
        $pre_enrolled_fmd->setBase64PreEnrolledFMD($pre_enrolled_fmd_string);

        $enrolled_cand_fmd = new Fingerprint\EnrolledFMD();
        $enrolled_cand_fmd->setBase64EnrolledFMD($enrolled_fmd_string);

        $verification_request = new Fingerprint\VerificationRequest(array("targetFMD" => $pre_enrolled_fmd));
        $verification_request->setFmdCandidates(array($enrolled_cand_fmd));

        list($verification_response, $status) = $this->client->VerifyFingerprint($verification_request)->wait();

        if ($status->code === Grpc\STATUS_OK) {
            return $verification_response->getMatch();
        } else {
            return "verification failed";
        }
    }
}
