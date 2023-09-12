<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.0.1/mdb.dark.min.css" rel="stylesheet" />
    <title>Document</title>
    <style>
        body {
            height: 100vh;
        }

        .finger-enroll{
            margin-left: 14px;
        }
    </style>
</head>

<body>
    <div class="container h-100 d-flex flex-column align-items-center justify-content-center h-100">
        <div class="card">
            <div class="card-body text-center">
                <h5 class="card-title" id="main_status">Place Your Finger</h5>
                <p class="card-text">
                <div class="text-center" id="finger_icon">
                    <i class="fa-solid fa-fingerprint fa-2xl"></i>
                </div>
                </p>
                <span id="result">Waiting for a scan.</span>

                <br>
                <span id="last_timer"></span>
                <time class="fw-bold" id="timer" style="display: none;">0:00:00.00</time>
                <hr>
                <span>Fingerprint Status: <span class="text-warning fw-bold" id="connected_state">Waiting</span></span>
                <br>
                <span id="device_id">
                    <div class="spinner-border spinner-border-sm" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </span>
                <!-- <button type="button" class="btn btn-primary shadow-0">Button</button> -->
                <div class="mt-2"><button class="btn btn-success shadow-0" id="add_new_user_trigger">Add new user</button></div>
            </div>
        </div>
        <div class="card mt-4" id="enroll_status_wrapper" style="display: none;">
            <div class="card-body text-center">
                <h5 class="card-title" id="enroll_status">Enroll Status</h5>
                <div class="grid mt-4" id="fp_grid">
                    <i class="fa-solid fa-fingerprint finger-enroll fa-2xl"></i>
                    <i class="fa-solid fa-fingerprint finger-enroll fa-2xl"></i>
                    <i class="fa-solid fa-fingerprint finger-enroll fa-2xl"></i>
                    <i class="fa-solid fa-fingerprint finger-enroll fa-2xl"></i>
                </div>
                <div class="mt-4"><button class="btn btn-sm btn-warning shadow-0">Cancel</button></div>
            </div>
        </div>
    </div>
</body>
<script>
    const BASE_URL = "<?= $ENV['FP_CLIENT_SERVICE_HOST'] ?>";
    const CLUSTER_LENGTH = "<?= $ENV["CLUSTER_LIST"]; ?>".split("|").length;
</script>
<script src="src/js/jquery-3.5.0.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.0.1/mdb.min.js"></script>
<script src="src/js/es6-shim.js"></script>
<script src="src/js/websdk.client.bundle.min.js"></script>
<script src="src/js/fingerprint.sdk.min.js"></script>
<script src="src/js/customv2.js?ref=1346"></script>

</html>