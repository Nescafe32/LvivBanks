<?php
session_start();
require_once 'dbconfig.php';

if (isset($_POST['trylogin'])) {
    $sLogin = $_POST['username'];
    $sPassword = $_POST['password'];

    $result = $db->query("SELECT * FROM users WHERE login='$sLogin' AND password='$sPassword'");

    $num_rows = mysqli_num_rows($result);
    if ($num_rows == 0) {
        echo "No such user!";
        exit;
    }

    $obj = $result->fetch_assoc();

    if ($obj['status'] != 'banned') {
        $_SESSION['user_id'] = $obj['id'];
        if ($obj['role'] == 'admin') {
            $_SESSION['isAdmin'] = true;
            header("Location: http://mymaps/admin.php");
        }
        else {
            $_SESSION['isLoggedUser'] = true;
            header("Location: http://mymaps/loggedUser.php");
        }
        die();
    } else {

        echo "Sorry, you're banned!";
    }

}
if (isset($_POST['tryregister'])) {

    $sLogin = $_POST['username'];

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

    $sPassword = filter_var($_POST['password'], FILTER_SANITIZE_STRING);
    $sPasswordrepeat = filter_var($_POST['passwordrepeat'], FILTER_SANITIZE_STRING);
    if ($sPassword != $sPasswordrepeat) {
        echo "Passwords are different!";
        exit;
    }

    $sFirstname = filter_var($_POST['firstname'], FILTER_SANITIZE_STRING);
    $sLastname = filter_var($_POST['lastname'], FILTER_SANITIZE_STRING);

    $result = $db->query("INSERT INTO users VALUES (NULL,'$sFirstname' ,'$sLastname','$sLogin','$sPassword', '$sEmail', 'active', 'user')");

    $_SESSION['isLoggedUser'] = true;
    $result = $db->query("SELECT * FROM users WHERE login='".$sLogin."'");
    $obj = $result->fetch_object();
    $_SESSION['user_id'] = $obj->id;

    header("Location: http://mymaps/loggedUser.php");
}
?>