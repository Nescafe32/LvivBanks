<?php
$db_username = 'root';
$db_password = '';
$db_name = 'lviv_banks';
$db_host = 'localhost';

$db = new mysqli($db_host, $db_username, $db_password, $db_name);
$db->set_charset("utf8") or die("Can`t set charset");

if (mysqli_connect_errno()) {
    header('HTTP/1.1 500 Error: Could not connect to db!');
    exit();
}

if ($_POST) {
    if (isset($_POST['sendfeedback'])) {
        $sItemId = (int)$_POST['itemid'];
        $sUserId = (int)$_POST['userid'];
        $sFeedbText = $_POST['feedbtext'];
        $FeedbDate = date("Y-m-d H:i:s");
        $feedbQuery = $db->query("INSERT INTO user_bank_feedbacks VALUES (NULL, $sItemId, $sUserId ,'$sFeedbText', '$FeedbDate')");

        if (!$feedbQuery) {
            header('HTTP/1.1 500 Error: Could not add feedback!');
            exit();
        }

        $_SESSION['user_id'] = $sUserId;
        header("Location: http://mymaps/loggedUser.php");
    } else {
        $xhr = $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest';
        if (!$xhr) {
            header('HTTP/1.1 500 Error: Request must come from Ajax!');
            exit();
        }

        $mLatLang = explode(',', $_POST["latlang"]);
        $mLat = filter_var($mLatLang[0], FILTER_VALIDATE_FLOAT);
        $mLng = filter_var($mLatLang[1], FILTER_VALIDATE_FLOAT);

        if (isset($_POST["del"]) && $_POST["del"] == true) {
            $sItemId = (int)$_POST['markerid'];
            $results = $db->query("DELETE FROM banks WHERE id=$sItemId");
            if (!$results) {
                header('HTTP/1.1 500 Error: Could not delete bank item!');
                exit();
            }
            exit("Done!");
        }

        $mType = filter_var($_POST["type"], FILTER_SANITIZE_STRING);
        $mName = filter_var($_POST["name"], FILTER_SANITIZE_STRING);
        $mAddress = filter_var($_POST["address"], FILTER_SANITIZE_STRING);

        $addBankQuery = $db->query("INSERT INTO banks VALUES (0, '$mType' ,'$mName','$mAddress','$mLat', '$mLng')");

        if (!$addBankQuery) {
            header('HTTP/1.1 500 Error: Could not insert new bank item!');
            exit();
        }

        $monStart = $_POST["monStart"];
        $monEnd = $_POST["monEnd"];
        $tueStart = $_POST["tueStart"];
        $tueEnd = $_POST["tueEnd"];
        $wedStart = $_POST["wedStart"];
        $wedEnd = $_POST["wedEnd"];
        $thuStart = $_POST["thuStart"];
        $thuEnd = $_POST["thuEnd"];
        $friStart = $_POST["friStart"];
        $friEnd = $_POST["friEnd"];
        $satStart = $_POST["satStart"];
        $satEnd = $_POST["satEnd"];
        $sunStart = $_POST["sunStart"];
        $sunEnd = $_POST["sunEnd"];

        $getID = $db->query("SELECT * FROM banks WHERE banks.name = '" . $mName . "' AND banks.type = '" . $mType . "' AND lat = '$mLat' AND lng = '$mLng'");
        $obj = $getID->fetch_object();
        $id = $obj->id;

        $monQuery = $db->query("INSERT INTO worktimes VALUES (1, $id, '$monStart' ,'$monEnd')");
        $tueQuery = $db->query("INSERT INTO worktimes VALUES (2, $id, '$tueStart' ,'$tueEnd')");
        $wedQuery = $db->query("INSERT INTO worktimes VALUES (3, $id, '$wedStart' ,'$wedEnd')");
        $thuQuery = $db->query("INSERT INTO worktimes VALUES (4, $id, '$thuStart' ,'$thuEnd')");
        $friQuery = $db->query("INSERT INTO worktimes VALUES (5, $id, '$friStart' ,'$friEnd')");
        $satQuery = $db->query("INSERT INTO worktimes VALUES (6, $id, '$satStart' ,'$satEnd')");
        $sunQuery = $db->query("INSERT INTO worktimes VALUES (7, $id, '$sunStart' ,'$sunEnd')");

        $output = '<h1 class="marker-heading">' . $mName . '</h1><p>' . $mAddress . '</p>';
        exit($output);
    }
}

if ($_GET) {
    $dom = new DOMDocument("1.0");
    $node = $dom->createElement("markers"); //Create new element node
    $parnode = $dom->appendChild($node); //make the node show up

    $results = $db->query("SELECT * FROM banks WHERE 1");
    if (!$results) {
        header('HTTP/1.1 500 Error: Could not get markers!');
        exit();
    }

    header("Content-type: text/xml");

    while ($obj = $results->fetch_object()) {
        $node = $dom->createElement("marker");
        $newnode = $parnode->appendChild($node);
        $newnode->setAttribute("id", $obj->id);
        $newnode->setAttribute("type", $obj->type);
        $newnode->setAttribute("name", $obj->name);
        $newnode->setAttribute("address", $obj->address);
        $newnode->setAttribute("lat", $obj->lat);
        $newnode->setAttribute("lng", $obj->lng);

        $wtimesQuery = $db->query("SELECT day_id, start_time, end_time FROM worktimes WHERE bank_id = $obj->id");
        if (!$wtimesQuery) {
            header('HTTP/1.1 500 Error: Could not get worktimes!');
            exit();
        }

        // do not forget that 1 - monday, 7 - sunday, because 'N'
        $curDay = date("N");
        $currTime = date('H:i:s');
        $status = "not active";
        $starttime = "00:00:00";
        $endtime = "00:00:01";
        while ($wtimes = $wtimesQuery->fetch_object()) {

            if ($curDay == $wtimes->day_id) {
                $dayid = $wtimes->id;
                $start_time = $wtimes->start_time;
                $end_time = $wtimes->end_time;
                if ($currTime > $start_time && $currTime < $end_time)
                {
                    $status = "active";
                    break;
                }
            }
        }
        $newnode->setAttribute("start_time", $start_time);
        $newnode->setAttribute("end_time", $end_time);
        $newnode->setAttribute("status", $status);
    }
    echo $dom->saveXML();
}
?>