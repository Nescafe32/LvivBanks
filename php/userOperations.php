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
    $xhr = $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest';
    if (!$xhr) {
        header('HTTP/1.1 500 Error: Request must come from Ajax!');
        exit();
    }

    $sLogin = $_POST['username'];
    $sPassword = $_POST['password'];

    if (isset($_POST["log"]) && $_POST["log"] == true) {

        $result = $db->query("SELECT * FROM users WHERE login='$sLogin' AND password='$sPassword'");

        $num_rows = mysqli_num_rows($result);
        if ($num_rows == 0) {
            echo "No such user!";
            exit;
        }
        $obj = $result->fetch_object();

        $status = $obj->status;

        if ($status == 'banned') {
            echo $status;
        } else {
//            $role = $obj->role;
//            echo $role;
            header('Location: http://mymaps/admin.php');
            die();
//            echo json_encode(array("location" => "admin.php"));
          //  exit;
        }
    }
    if (isset($_POST["del"]) && $_POST["del"] == true) {

        $result = $db->query("DELETE FROM users WHERE login='$sLogin'");

        $num_rows = mysqli_num_rows($result);
        if ($num_rows == 0) {
            echo "No such user!";
            exit;
        }
        exit("User has been successfully deleted!");
    }
    if (isset($_POST["reg"]) && $_POST["reg"] == true) {

        $result = $db->query("SELECT * FROM users WHERE login='$sLogin'");
        $num_rows = mysqli_num_rows($result);
        if ($num_rows != 0) {
            echo "User with this login already exists!";
            exit;
        }

        $sEmail = filter_var($_POST['email'], FILTER_SANITIZE_STRING);
        $result = $db->query("SELECT * FROM users WHERE email='$sEmail'");
        $num_rows = mysqli_num_rows($result);
        if ($num_rows != 0) {
            echo "User with this email already exists!";
            exit;
        }

        $sConfirmPassword = filter_var($_POST['confirm_password'], FILTER_SANITIZE_STRING);
        if ($sPassword != $sConfirmPassword) {
            echo "Passwords are different!";
            exit;
        }

        $sFirstname = filter_var($_POST['firstname'], FILTER_SANITIZE_STRING);
        $sLastname = filter_var($_POST['lastname'], FILTER_SANITIZE_STRING);

        $result = $db->query("INSERT INTO users VALUES (NULL,'$sFirstname' ,'$sLastname','$sLogin','$sPassword', '$sEmail', 'active', 'user')");
        exit("User has been successfully registered!");
    }
}
?>