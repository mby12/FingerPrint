/**
 * Custom implementation for the FingerPrint
 * Reader and other JS functions
 * @authors Dahir Muhammad Dahir (dahirmuhammad3@gmail.com)
 * @date    2020-04-14 17:06:41
 * @version 1.0.0
 */

let currentFormat = Fingerprint.SampleFormat.Intermediate;

let isDeviceConnected = false;
let isEnrolling = false;

var output = document.getElementById('timer');

let timer;

// timer start time
let then;
// pause duration
let delay;

let FingerprintSdkTest = (function () {
    function FingerprintSdkTest() {
        let _instance = this;
        this.operationToRestart = null;
        this.acquisitionStarted = false;
        // instantiating the fingerprint sdk here
        this.sdk = new Fingerprint.WebApi;
        this.sdk.onDeviceConnected = function (e) {
            // Detects if the device is connected for which acquisition started
            // showMessage("Scan Appropriate Finger on the Reader", "success");
            isDeviceConnected = true;
            updateConnectedLabel();
            getConnectedDeviceId();
            // $("#result").html("Waiting for a scan.");
            // $("#finger_icon").attr("class", "text-center");
        };
        this.sdk.onDeviceDisconnected = function (e) {
            // Detects if device gets disconnected - provides deviceUid of disconnected device
            // showMessage("Device is Disconnected. Please Connect Back");
            isDeviceConnected = false;
            updateConnectedLabel();
            $("#device_id").html("");
            showMessage("Please Connect Your Device");
            $("#result").html("");
        };
        this.sdk.onCommunicationFailed = function (e) {
            // Detects if there is a failure in communicating with U.R.U web SDK
            showMessage("Communication Failed. Please Reconnect Device")
        };
        this.sdk.onSamplesAcquired = function (s) {
            // Sample acquired event triggers this function
            console.log("onSamplesAcquired", s);
            verifyFinger(s);
            // storeSample(s);
        };
        this.sdk.onQualityReported = function (e) {
            // Quality of sample acquired - Function triggered on every sample acquired
            // console.log(Fingerprint.QualityCode[(e.quality)]);
        }
    }

    // this is were finger print capture takes place
    FingerprintSdkTest.prototype.startCapture = function () {
        if (this.acquisitionStarted) // Monitoring if already started capturing
            return;
        let _instance = this;
        // showMessage("");
        this.operationToRestart = this.startCapture;
        this.sdk.startAcquisition(currentFormat, "").then(function () {
            _instance.acquisitionStarted = true;

            //Disabling start once started
            //disableEnableStartStop();

        }, function (error) {
            showMessage(error.message);
        });
    };

    FingerprintSdkTest.prototype.stopCapture = function () {
        if (!this.acquisitionStarted) //Monitor if already stopped capturing
            return;
        let _instance = this;
        showMessage("");
        this.sdk.stopAcquisition().then(function () {
            _instance.acquisitionStarted = false;

            //Disabling stop once stopped
            //disableEnableStartStop();

        }, function (error) {
            showMessage(error.message);
        });
    };

    FingerprintSdkTest.prototype.getInfo = function () {
        let _instance = this;
        return this.sdk.enumerateDevices();
    };

    FingerprintSdkTest.prototype.getDeviceInfoWithID = function (uid) {
        let _instance = this;
        return this.sdk.getDeviceInfo(uid);
    };

    return FingerprintSdkTest;
})();

const run = function () {
    // get output array and print
    var time = parseTime(Date.now() - then - delay);
    output.innerHTML = time[0] + ':' + time[1] + ':' + time[2] + '.' + time[3];
};

const parseTime = function (elapsed) {
    // array of time multiples [hours, min, sec, decimal]
    var d = [3600000, 60000, 1000, 10];
    var time = [];
    var i = 0;

    while (i < d.length) {
        var t = Math.floor(elapsed / d[i]);

        // remove parsed time for next iteration
        elapsed -= t * d[i];

        // add '0' prefix to m,s,d when needed
        t = (i > 0 && t < 10) ? '0' + t : t;
        time.push(t);
        i++;
    }

    return time;
};

const startTimer = function () {
    delay = 0;
    running = true;
    then = Date.now();
    timer = setInterval(run, 51);
}

const stopTimer = function () {
    clearInterval(timer);
    run();
}
const total_cluster = 2;

async function verifyFinger({ type, deviceUid, sampleFormat, samples }) {
    startTimer();
    // var start = new Date().getTime();
    // $("#finger_icon").attr("class", "text-center");
    // $("#last_timer").text("").hide();
    // $("#timer").show();

    // {
    //     "type": "SamplesAcquired",
    //     "deviceUid": "19E0DA0F-E29F-364C-BA3F-120C54745A3B",
    //     "sampleFormat": 2,
    //     "samples": "[{\"Data\":\"AOg3Acgp43NcwEE381mKKyZdZ2YgQ0qX0nk2nzgcL8vRcLSXXeV77tjTpmOsq2IZP8nXLjkghtXbj5iSeO_tJEFlUeG3OTU2Z_kvXY5Hszt3Ep9psjKD2p_MQitPnYZxPesYvR-s_5Wlew5JHTvZa5-qJumizvhuZe1fdVjiKvdEH5VhIovwcM5gz_oNFYCCz9P5eGMwYIcMoJZoRNykmWfZs3Uxe7dpelQ8lWJKWlT9ZAt62rBxdGn-VZLDl_1N93hvdamJHk0v8n_axsbUWS1FeiUDXVoV0nsQWPuUDdLtFDUCQfz12HXQl4JX7JQ2luJ_YXMFcMoz0o4AgDQJXdaPk70I09dNugAOgYJEzfBqj61kpqolBwP9n7EVAXBeUS9TBBqGg3bC9bjXSYHhCrOo6HC_sW_qAfR0bwAA\",\"Header\":{\"Encryption\":0,\"Factor\":8,\"Format\":{\"FormatID\":0,\"FormatOwner\":51},\"Purpose\":0,\"Quality\":-1,\"Type\":2},\"Version\":1}]\n"
    // }
    //src\core\verify_new.php
    $("#result").html(`<div class="spinner-border spinner-border-sm" role="status"><span class="visually-hidden">Loading...</span></div>`);
    const [{ Data: finger_data }] = JSON.parse(samples);
    console.log("INI", finger_data);
    // const selected_cluster = 1;
    const array_of_request = [];
    for (let current_cluster = 1; current_cluster <= total_cluster; current_cluster++) {
        const request = $.post("http://localhost:5556/coreComponents/verify_new.php", { data: finger_data, selected_cluster: current_cluster, total_cluster }, function (check_result) {
            console.log("FROM CLUSTER", current_cluster, check_result);
            const { success, data: user_data, data_count } = check_result;
            if (success) {
                for (const iterator of array_of_request) {
                    console.log(`aborted find on cluster ${array_of_request.indexOf(iterator)}`);
                    iterator.abort();
                }
                const { id, username, fullname } = user_data;
                $("#finger_icon").attr('class', "text-center text-success");
                $("#result").html(`<span class="text-info fw-bold">User Found!</span> <br>User ID : ${id}<br> Username: ${username}<br> Fullname: ${fullname}<br>Worker: ${current_cluster}`)
                console.log(`FOUND IN CLUSTER ${current_cluster}`);
            }

        });
        array_of_request.push(request);
        console.log("added");
    }
    // const check_result = await $.post("http://localhost:5556/coreComponents/verify_new.php", { data: finger_data });
    // console.log(check_result);
    // const { success, data: user_data, data_count } = check_result;
    // // $("#result").html()
    // if (success) {
    //     const { id, username, fullname } = user_data;
    //     $("#finger_icon").attr('class', "text-center text-success");
    //     $("#result").html(`<span class="text-info fw-bold">User Found!</span> <br>User ID : ${id}<br> Username: ${username}<br> Fullname: ${fullname}`)
    // } else {
    //     $("#finger_icon").attr('class', "text-center text-danger");
    //     $("#result").html(`Finger not matched any user. scanned ${data_count} data`);
    // }
    // var end = new Date().getTime();
    // var time = end - start;
    // const result_time = (time / 1000);
    // $("#last_timer").attr("class", result_time >= 5 ? "text-danger fw-bold" : "text-info fw-bold").text(result_time + "s");
    // stopTimer();
    // $("#last_timer").show();
    // $("#timer").hide();
}

class Reader {
    constructor() {
        this.reader = new FingerprintSdkTest();
        this.selectFieldID = null;
        this.currentStatusField = null;
        /**
         * @type {Hand}
         */
        this.currentHand = null;
    }

    readerSelectField(selectFieldID) {
        this.selectFieldID = selectFieldID;
    }

    setStatusField(statusFieldID) {
        this.currentStatusField = statusFieldID;
    }

    displayReader() {
        let readers = this.reader.getInfo();  // grab available readers here
        let id = this.selectFieldID;
        let selectField = document.getElementById(id);
        selectField.innerHTML = `<option>Select Fingerprint Reader</option>`;
        readers.then(function (availableReaders) {  // when promise is fulfilled
            if (availableReaders.length > 0) {
                showMessage("");
                for (let reader of availableReaders) {
                    selectField.innerHTML += `<option value="${reader}" selected>${reader}</option>`;
                }
            }
            else {
                showMessage("Please Connect the Fingerprint Reader");
            }
        })
    }
}

class Hand {
    constructor() {
        this.id = 0;
        this.index_finger = [];
        this.middle_finger = [];
    }

    addIndexFingerSample(sample) {
        this.index_finger.push(sample);
    }

    addMiddleFingerSample(sample) {
        this.middle_finger.push(sample);
    }

    generateFullHand() {
        let id = this.id;
        let index_finger = this.index_finger;
        let middle_finger = this.middle_finger;
        return JSON.stringify({ id, index_finger, middle_finger });
    }
}

let myReader = new Reader();

function beginEnrollment() {
    setReaderSelectField("enrollReaderSelect");
    myReader.setStatusField("enrollmentStatusField");
}

function beginIdentification() {
    setReaderSelectField("verifyReaderSelect");
    myReader.setStatusField("verifyIdentityStatusField");
}

function setReaderSelectField(fieldName) {
    myReader.readerSelectField(fieldName);
    myReader.displayReader();
}

function updateConnectedLabel() {
    const el = $("#connected_state");
    if (isDeviceConnected) {
        el.attr("class", "text-success fw-bold").text("Connected");
    } else {
        el.attr("class", "text-danger fw-bold").text("Disconnected");
    }
}

function showMessage(message, message_type = "a") {
    const el = $("#main_status");
    let types = new Map();
    types.set("success", "fw-bold text-success");
    types.set("error", "fw-bold text-danger");
    types.set("a", "")
    el.html(`<span class="${types.get(message_type)} pw-bold">${message}</p>`);
}

function getConnectedDeviceId() {
    const reader = new FingerprintSdkTest();
    let readers = reader.getInfo();  // grab available readers here
    // let id = this.selectFieldID;
    // let selectField = document.getElementById(id);
    // selectField.innerHTML = `<option>Select Fingerprint Reader</option>`;
    readers.then(function (availableReaders) {  // when promise is fulfilled
        if (availableReaders.length > 0) {
            showMessage("Place Your Finger");
            for (let reader of availableReaders) {
                $("#device_id").text(reader);
            }
        }
        else {
            // alert("Please Connect the Fingerprint Reader");
        }
    })
}

function init() {
    showMessage("Initializing");
    if (isEnrolling) {
        console.log("Already Enrolling");
        return;
    }
    getConnectedDeviceId();
    myReader.currentHand = new Hand();
    // storeUserID();  // for current user in Hand instance
    myReader.reader.startCapture();
    // showNextNotEnrolledItem();
    isEnrolling = true;
}

function captureForIdentify() {
    if (!readyForIdentify()) {
        return;
    }
    myReader.currentHand = new Hand();
    storeUserID();
    myReader.reader.startCapture();
    showNextNotEnrolledItem();
}

/**
 * @returns {boolean}
 */
function readyForEnroll() {
    return ((document.getElementById("userID").value !== "") && (document.getElementById("enrollReaderSelect").value !== "Select Fingerprint Reader"));
}

/**
* @returns {boolean}
*/
function readyForIdentify() {
    return ((document.getElementById("userIDVerify").value !== "") && (document.getElementById("verifyReaderSelect").value !== "Select Fingerprint Reader"));
}

function clearCapture() {
    clearInputs();
    clearPrints();
    clearHand();
    myReader.reader.stopCapture();
    document.getElementById("userDetails").innerHTML = "";
}

function clearInputs() {
    document.getElementById("userID").value = "";
    document.getElementById("userIDVerify").value = "";
    //let id = myReader.selectFieldID;
    //let selectField = document.getElementById(id);
    //selectField.innerHTML = `<option>Select Fingerprint Reader</option>`;
}

function clearPrints() {
    let indexFingers = document.getElementById("indexFingers");
    let middleFingers = document.getElementById("middleFingers");
    let verifyFingers = document.getElementById("verificationFingers");

    if (indexFingers) {
        for (let indexfingerElement of indexFingers.children) {
            indexfingerElement.innerHTML = `<span class="icon icon-indexfinger-not-enrolled" title="not_enrolled"></span>`;
        }
    }

    if (middleFingers) {
        for (let middlefingerElement of middleFingers.children) {
            middlefingerElement.innerHTML = `<span class="icon icon-middlefinger-not-enrolled" title="not_enrolled"></span>`;
        }
    }

    if (verifyFingers) {
        for (let finger of verifyFingers.children) {
            finger.innerHTML = `<span class="icon icon-indexfinger-not-enrolled" title="not_enrolled"></span>`;
        }
    }
}

function clearHand() {
    myReader.currentHand = null;
}

function showSampleCaptured() {
    let nextElementID = getNextNotEnrolledID();
    let markup = null;
    if (nextElementID.startsWith("index") || nextElementID.startsWith("verification")) {
        markup = `<span class="icon icon-indexfinger-enrolled" title="enrolled"></span>`;
    }

    if (nextElementID.startsWith("middle")) {
        markup = `<span class="icon icon-middlefinger-enrolled" title="enrolled"></span>`;
    }

    if (nextElementID !== "" && markup) {
        let nextElement = document.getElementById(nextElementID);
        nextElement.innerHTML = markup;
    }
}

function showNextNotEnrolledItem() {
    let nextElementID = getNextNotEnrolledID();
    let markup = null;
    if (nextElementID.startsWith("index") || nextElementID.startsWith("verification")) {
        markup = `<span class="icon capture-indexfinger" title="not_enrolled"></span>`;
    }

    if (nextElementID.startsWith("middle")) {
        markup = `<span class="icon capture-middlefinger" title="not_enrolled"></span>`;
    }

    if (nextElementID !== "" && markup) {
        let nextElement = document.getElementById(nextElementID);
        nextElement.innerHTML = markup;
    }
}

/**
 * @returns {string}
 */
function getNextNotEnrolledID() {
    let indexFingers = document.getElementById("indexFingers");
    let middleFingers = document.getElementById("middleFingers");
    let verifyFingers = document.getElementById("verificationFingers");

    let enrollUserId = document.getElementById("userID").value;
    let verifyUserId = document.getElementById("userIDVerify").value;

    let indexFingerElement = findElementNotEnrolled(indexFingers);
    let middleFingerElement = findElementNotEnrolled(middleFingers);
    let verifyFingerElement = findElementNotEnrolled(verifyFingers);

    //assumption is that we will always start with
    //indexfinger and run down to middlefinger
    if (indexFingerElement !== null && enrollUserId !== "") {
        return indexFingerElement.id;
    }

    if (middleFingerElement !== null && enrollUserId !== "") {
        return middleFingerElement.id;
    }

    if (verifyFingerElement !== null && verifyUserId !== "") {
        return verifyFingerElement.id;
    }

    return "";
}

/**
 * 
 * @param {Element} element
 * @returns {Element}
 */
function findElementNotEnrolled(element) {
    if (element) {
        for (let fingerElement of element.children) {
            if (fingerElement.firstElementChild.title === "not_enrolled") {
                return fingerElement;
            }
        }
    }

    return null;
}

function storeUserID() {
    let enrollUserId = document.getElementById("userID").value;
    let identifyUserId = document.getElementById("userIDVerify").value;
    myReader.currentHand.id = enrollUserId !== "" ? enrollUserId : identifyUserId;
}

function storeSample(sample) {
    let samples = JSON.parse(sample.samples);
    let sampleData = samples[0].Data;

    let nextElementID = getNextNotEnrolledID();

    if (nextElementID.startsWith("index") || nextElementID.startsWith("verification")) {
        myReader.currentHand.addIndexFingerSample(sampleData);
        showSampleCaptured();
        showNextNotEnrolledItem();
        return;
    }

    if (nextElementID.startsWith("middle")) {
        myReader.currentHand.addMiddleFingerSample(sampleData);
        showSampleCaptured();
        showNextNotEnrolledItem();
    }
}

function serverEnroll() {
    if (!readyForEnroll()) {
        return;
    }

    let data = myReader.currentHand.generateFullHand();
    let successMessage = "Enrollment Successful!";
    let failedMessage = "Enrollment Failed!";
    let payload = `data=${data}`;

    let xhttp = new XMLHttpRequest();

    xhttp.onreadystatechange = function () {
        if (this.readyState === 4 && this.status === 200) {
            if (this.responseText === "success") {
                showMessage(successMessage, "success");
            }
            else {
                showMessage(`${failedMessage} ${this.responseText}`);
            }
        }
    };

    xhttp.open("POST", "src/core/enroll.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send(payload);
}

function serverIdentify() {
    if (!readyForIdentify()) {
        return;
    }

    let data = myReader.currentHand.generateFullHand();
    let detailElement = document.getElementById("userDetails");
    let successMessage = "Identification Successful!";
    let failedMessage = "Identification Failed!. Try again";
    let payload = `data=${data}`;

    let xhttp = new XMLHttpRequest();

    xhttp.onreadystatechange = function () {
        if (this.readyState === 4 && this.status === 200) {
            if (this.responseText !== null && this.responseText !== "") {
                let response = JSON.parse(this.responseText);
                if (response !== "failed" && response !== null) {
                    showMessage(successMessage, "success");
                    detailElement.innerHTML = `<div class="col text-center">
                                <label for="fullname" class="my-text7 my-pri-color">Fullname</label>
                                <input type="text" id="fullname" class="form-control" value="${response[0].fullname}">
                            </div>
                            <div class="col text-center">
                                <label for="email" class="my-text7 my-pri-color">Email</label>
                                <input type="text" id="email" class="form-control" value="${response[0].username}">
                            </div>`;
                }
                else {
                    showMessage(failedMessage);
                }
            }
        }
    };

    xhttp.open("POST", "src/core/verify.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send(payload);
}

init();