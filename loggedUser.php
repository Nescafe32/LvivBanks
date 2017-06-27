<?php
session_start();

if(!isset($_SESSION['user_id']))
{
    header("Location: index.php");
}
include_once 'dbconfig.php';

$uid = $_SESSION['user_id'];
$result = $db->query("SELECT * FROM users WHERE id=$uid");
$obj = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta charset="utf-8">
<!--    <meta http-equiv="refresh" content="60; url=index.php">-->
    <title>Logged user</title>
    <link rel="stylesheet" type="text/css" href="./css/menu.css">
    <link rel="stylesheet" type="text/css" href="./css/findpath.css">
    <link rel="stylesheet" type="text/css" href="./css/filter1.css">
    <link rel="stylesheet" type="text/css" href="./css/login1.css">
    <link rel="stylesheet" type="text/css" href="./css/feedback.css">
    <link rel="stylesheet" type="text/css" href="./css/reg1.css">
    <script type="text/javascript" src="./js/jquery-1.11.3.js"></script>
    <script type="text/javascript" src="./js/lvivArea.js"></script>
    <script type="text/javascript" src="./js/markerclustererplus.js"></script>
    <link rel="stylesheet" href="assets/css/font-awesome.min.css">
    <script src="assets/js/bootstrap.min.js"></script>
    <script type='text/javascript' src="./bootstrap-slider.js"></script>
    <link href="./bootstrap.min.css" rel="stylesheet">
    <link href="./bootstrap-slider.css" rel="stylesheet">

    <style>
        #map {
            height: 93%;
        }

        html,
        body {
            height: 100%;
            margin: 0;
            padding: 0;
        }

        .navbar-default .navbar-nav > li.clr1 a {
            color: #ffffff;
        }

        .navbar-default .navbar-nav > li.clr2 a {
            color: #FFEB3B;;
        }

        .navbar-default .navbar-nav > li.clr3 a {
            color: #5EC64D;
        }

        .navbar-default .navbar-nav > li.clr4 a {
            color: #29AAE2;
        }

        .navbar-default .navbar-nav > li.clr1 a:hover,
        .navbar-default .navbar-nav > li.clr1.active a {
            color: #fff;
            background: #F55;
        }

        .navbar-default .navbar-nav > li.clr2 a:hover,
        .navbar-default .navbar-nav > li.clr2.active a {
            color: #fff;
            background: #973CB6;
        }

        .navbar-default .navbar-nav > li.clr3 a:hover,
        .navbar-default .navbar-nav > li.clr3.active a {
            color: #fff;
            background: #5EC64D;
        }

        .navbar-default .navbar-nav > li.clr4 a:hover,
        .navbar-default .navbar-nav > li.clr4.active a {
            color: #fff;
            background: #29AAE2;
        }

        a {
            color: #FFC107;
            text-decoration: none;
        }

        #directions-explanation-panel {
            font-family: 'Roboto', 'sans-serif';
            line-height: 30px;
            padding-left: 10px;
        }

        #directions-explanation-panel select,
        #directions-explanation-panel input {
            font-size: 15px;
        }

        #directions-explanation-panel select {
            width: 100%;
        }

        #directions-explanation-panel i {
            font-size: 12px;
        }

        #directions-explanation-panel {
            height: 90%;
            float: right;
            width: 30%;
            overflow: auto;
        }

        @media print {
            #map {
                height: 500px;
                margin: 0;
            }

            #directions-explanation-panell {
                float: none;
                width: auto;
            }
        }

        .marker-edit label {
            display: block;
            margin-bottom: 5px;
        }

        .marker-edit label span {
            width: 100px;
            float: left;
        }

        .marker-edit label input,
        .marker-edit label select {
            height: 24px;
        }

        .marker-edit label textarea {
            height: 60px;
            width: 57%;
            margin: 0px;
            padding-left: 5px;
            border: 1px solid #DDD;
            border-radius: 3px;
        }

        .marker-edit label input,
        .marker-edit label select {
            width: 60%;
            margin: 0px;
            padding-left: 5px;
            border: 1px solid #DDD;
            border-radius: 3px;
        }

        h1.marker-heading {
            color: #585858;
            margin: 0px;
            padding: 0px;
            font: 25px "Trebuchet MS", Arial !important;
            border-bottom: 1px dotted #D8D8D8;
        }

        div.marker-info-win {
            max-width: 300px;
        }

        div.marker-info-win p {
            padding: 0px;
            margin: 10px 0px 10px 0;
        }

        div.marker-inner-win {
            padding: 5px;
        }

        #locs {
            margin-top: 0px;
            padding-top: 0px;
            margin-left: 10px;
            float:left;
            height: 90%;
            overflow-y: scroll;
        }
        .loc {
            border-style:solid;
            border-width:thin;
            width: 300px;
            padding: 5px;
            cursor:pointer;
            margin-top:0px;
        }
    </style>
</head>

<body>
<div id="menu_div">
    <div id="navigation">
        <div class="find-way-option">
            <ul class="nav navbar-nav navbar-left">
                <li class="clr3 dropdown"><a class="dropdown-toggle" data-toggle="dropdown">Find route<span
                            class="caret"></span></a>
                    <ul class="dropdown-menu" style="padding: 20px 10px 5px 10px; width:300px;">
                        <li>
                            <form id="test-form">
                                <div class="form-group">
                                    <input type="text" class="form-control" id="first-address" placeholder="From"></div>
                                <div class="form-group">
                                    <input type="text" class="form-control" id="second-address" placeholder="To"></div>
                                <div class="form-group">
                                    <select id="travel-mode">
                                        <option value="DRIVING">DRIVING</option>
                                        <option value="TRANSIT">TRANSIT</option>
                                        <option value="WALKING">WALKING</option>
                                    </select>
                                </div>
                                <input type="button" class="small color green button" value="Find way" id="find-way"/>
                                <input type="button" class="small color red button" value="Clear data" id="clear-data"/>
                                <input type="button" class="small color blue button" value="Close panel"
                                       id="close-explanation-panel"
                                       onclick="$('#directions-explanation-panel').css('display', 'none'); $(this).css('display', 'none')"
                                       hidden="true"/></form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
        <div class="filter-option">
            <ul class="nav navbar-nav navbar-left">
                <li class="clr3 dropdown"><a class="dropdown-toggle" data-toggle="dropdown">Find banks<span
                            class="caret"></span></a>
                    <ul class="dropdown-menu" style="padding: 20px 10px 5px 10px; width:300px;">
                        <li>
                            <div id="" style="overflow-y: scroll; height:400px;">
                                <form name="filter" id="form-filter" method="post">
                                    <div class="qf-1">
                                        <div class="w-100">
                                            <div class="field">
                                                <div class="w-30 mr-5">
                                                    <input type="checkbox" id="abank" name="banknames[]">
                                                    <label for="abank" class="tick"><span><img alt="А-Банк" height="20"
                                                                                               src="images/abank.png"
                                                                                               width="20"/> A-bank</span></label>
                                                </div>
                                                <div class="w-30 mr-5">
                                                    <input type="checkbox" id="accordbank" name="banknames[]">
                                                    <label for="accordbank" class="tick"><span><img alt="Аккордбанк"
                                                                                                    height="20"
                                                                                                    src="images/accordbank.png"
                                                                                                    width="20"/> Accordbank</span></label>
                                                </div>
                                                <div class="w-30 mr-5">
                                                    <input type="checkbox" id="alphabank" name="banknames[]">
                                                    <label for="alphabank" class="tick"><span><img alt="Альфабанк"
                                                                                                   height="20"
                                                                                                   src="images/alphabank.png"
                                                                                                   width="20"/> Alfabank</span></label>
                                                </div>
                                                <div class="w-30 mr-5">
                                                    <input type="checkbox" id="apeksbank" name="banknames[]">
                                                    <label for="apeksbank" class="tick"><span><img alt="Апексбанк"
                                                                                                   height="20"
                                                                                                   src="images/apeksbank.png"
                                                                                                   width="20"/> Apeksbank</span></label>
                                                </div>
                                                <div class="w-30 mr-5">
                                                    <input type="checkbox" id="bmbank" name="banknames[]">
                                                    <label for="bmbank" class="tick"><span><img alt="БМ" height="20"
                                                                                                src="images/bmbank.png"
                                                                                                width="20"/> BM</span></label>
                                                </div>
                                                <div class="w-30 mr-5">
                                                    <input type="checkbox" id="btabank" name="banknames[]">
                                                    <label for="btabank" class="tick"><span><img alt="БТА" height="20"
                                                                                                 src="images/btabank.png"
                                                                                                 width="20"/> BTA</span></label>
                                                </div>
                                                <div class="w-30 mr-5">
                                                    <input type="checkbox" id="concordbank" name="banknames[]">
                                                    <label for="concordbank" class="tick"><span><img alt="Конкордбанк"
                                                                                                     height="20"
                                                                                                     src="images/concordbank.png"
                                                                                                     width="20"/> Concordbank</span></label>
                                                </div>
                                                <div class="w-30 mr-5">
                                                    <input type="checkbox" id="creditagricolebank" name="banknames[]">
                                                    <label for="creditagricolebank" class="tick"><span><img
                                                                alt="Креди-агриколь банк" height="20"
                                                                src="images/creditagricolebank.png" width="20"/> Credit-agricole</span></label>
                                                </div>
                                                <div class="w-30 mr-5">
                                                    <input type="checkbox" id="creditdniprobank" name="banknames[]">
                                                    <label for="creditdniprobank" class="tick"><span><img
                                                                alt="Кредит-дніпро банк" height="20"
                                                                src="images/creditdniprobank.png" width="20"/> Credit-dnipro</span></label>
                                                </div>
                                                <div class="w-30 mr-5">
                                                    <input type="checkbox" id="expressbank" name="banknames[]">
                                                    <label for="expressbank" class="tick"><span><img alt="Експресбанк"
                                                                                                     height="20"
                                                                                                     src="images/expressbank.png"
                                                                                                     width="20"/> Expressbank</span></label>
                                                </div>
                                                <div class="clrfx mb-20"></div>
                                                <div class="w-30 mr-5">
                                                    <input type="checkbox" id="forwardbank" name="banknames[]">
                                                    <label for="forwardbank" class="tick"><span><img alt="Форвардбанк"
                                                                                                     height="20"
                                                                                                     src="images/forwardbank.png"
                                                                                                     width="20"/> Forwardbank</span></label>
                                                </div>
                                                <div class="w-30 mr-5">
                                                    <input type="checkbox" id="grantbank" name="banknames[]">
                                                    <label for="grantbank" class="tick"><span><img alt="Грантбанк"
                                                                                                   height="20"
                                                                                                   src="images/grantbank.png"
                                                                                                   width="20"/> Grantbank</span></label>
                                                </div>
                                                <div class="w-30 mr-5">
                                                    <input type="checkbox" id="ideabank" name="banknames[]">
                                                    <label for="ideabank" class="tick"><span><img alt="Ідеябанк"
                                                                                                  height="20"
                                                                                                  src="images/ideabank.png"
                                                                                                  width="20"/> Ideabank</span></label>
                                                </div>
                                                <div class="w-30 mr-5">
                                                    <input type="checkbox" id="industrialbank" name="banknames[]">
                                                    <label for="industrialbank" class="tick"><span><img
                                                                alt="Індастріалбанк" height="20"
                                                                src="images/industrialbank.png" width="20"/> Industrialbank</span></label>
                                                </div>
                                                <div class="w-30 mr-5">
                                                    <input type="checkbox" id="kredobank" name="banknames[]">
                                                    <label for="kredobank" class="tick"><span><img alt="Кредобанк"
                                                                                                   height="20"
                                                                                                   src="images/kredobank.png"
                                                                                                   width="20"/> Kredobank</span></label>
                                                </div>
                                                <div class="w-30 mr-5">
                                                    <input type="checkbox" id="lvivbank" name="banknames[]">
                                                    <label for="lvivbank" class="tick"><span><img alt="Львівбанк"
                                                                                                  height="20"
                                                                                                  src="images/lvivbank.png"
                                                                                                  width="20"/> Lvivbank</span></label>
                                                </div>
                                                <div class="w-30 mr-5">
                                                    <input type="checkbox" id="megabank" name="banknames[]">
                                                    <label for="megabank" class="tick"><span><img alt="Мегабанк"
                                                                                                  height="20"
                                                                                                  src="images/megabank.png"
                                                                                                  width="20"/> Megabank</span></label>
                                                </div>
                                                <div class="w-30 mr-5">
                                                    <input type="checkbox" id="mibbank" name="banknames[]">
                                                    <label for="mibbank" class="tick"><span><img alt="МІБ" height="20"
                                                                                                 src="images/mibbank.png"
                                                                                                 width="20"/> MIB</span></label>
                                                </div>
                                                <div class="w-30 mr-5">
                                                    <input type="checkbox" id="oksibank" name="banknames[]">
                                                    <label for="oksibank" class="tick"><span><img alt="Оксібанк"
                                                                                                  height="20"
                                                                                                  src="images/oksibank.png"
                                                                                                  width="20"/> Oksibank</span></label>
                                                </div>
                                                <div class="w-30 mr-5">
                                                    <input type="checkbox" id="oschadbank" name="banknames[]">
                                                    <label for="oschadbank" class="tick"><span><img alt="Ощадбанк"
                                                                                                    height="20"
                                                                                                    src="images/oschadbank.png"
                                                                                                    width="20"/> Oschadbank</span></label>
                                                </div>
                                                <div class="clrfx mb-20"></div>
                                                <div class="w-30 mr-5">
                                                    <input type="checkbox" id="otpbank" name="banknames[]">
                                                    <label for="otpbank" class="tick"><span><img alt="Отпбанк"
                                                                                                 height="20"
                                                                                                 src="images/otpbank.png"
                                                                                                 width="20"/> OTPbank</span></label>
                                                </div>
                                                <div class="w-30 mr-5">
                                                    <input type="checkbox" id="pinbank" name="banknames[]">
                                                    <label for="pinbank" class="tick"><span><img alt="ПІН" height="20"
                                                                                                 src="images/pinbank.png"
                                                                                                 width="20"/> PIN</span></label>
                                                </div>
                                                <div class="w-30 mr-5">
                                                    <input type="checkbox" id="piraeusbank" name="banknames[]">
                                                    <label for="piraeusbank" class="tick"><span><img alt="Піреусбанк"
                                                                                                     height="20"
                                                                                                     src="images/piraeusbank.png"
                                                                                                     width="20"/> Piraeusbank</span></label>
                                                </div>
                                                <div class="w-30 mr-5">
                                                    <input type="checkbox" id="pivdenniybank" name="banknames[]">
                                                    <label for="pivdenniybank" class="tick"><span><img
                                                                alt="Південнийбанк" height="20"
                                                                src="images/pivdenniybank.png" width="20"/> Pivdenniybank</span></label>
                                                </div>
                                                <div class="w-30 mr-5">
                                                    <input type="checkbox" id="poltavabank" name="banknames[]">
                                                    <label for="poltavabank" class="tick"><span><img alt="Полтавабанк"
                                                                                                     height="20"
                                                                                                     src="images/poltavabank.png"
                                                                                                     width="20"/> Poltavabank</span></label>
                                                </div>
                                                <div class="w-30 mr-5">
                                                    <input type="checkbox" id="praveksbank" name="banknames[]">
                                                    <label for="praveksbank" class="tick"><span><img alt="Правексбанк"
                                                                                                     height="20"
                                                                                                     src="images/praveksbank.png"
                                                                                                     width="20"/> Praveksbank</span></label>
                                                </div>
                                                <div class="w-30 mr-5">
                                                    <input type="checkbox" id="privatbank" name="banknames[]">
                                                    <label for="privatbank" class="tick"><span><img alt="Приватбанк"
                                                                                                    height="20"
                                                                                                    src="images/privatbank.png"
                                                                                                    width="20"/> Privatbank</span></label>
                                                </div>
                                                <div class="w-30 mr-5">
                                                    <input type="checkbox" id="procreditbank" name="banknames[]">
                                                    <label for="procreditbank" class="tick"><span><img
                                                                alt="Прокредитбанк" height="20"
                                                                src="images/procreditbank.png" width="20"/> Procreditbank</span></label>
                                                </div>
                                                <div class="w-30 mr-5">
                                                    <input type="checkbox" id="prominvestbank" name="banknames[]">
                                                    <label for="prominvestbank" class="tick"><span><img
                                                                alt="Промінвестбанк" height="20"
                                                                src="images/prominvestbank.png" width="20"/> Prominvestbank</span></label>
                                                </div>
                                                <div class="w-30 mr-5">
                                                    <input type="checkbox" id="pumbbank" name="banknames[]">
                                                    <label for="pumbbank" class="tick"><span><img alt="ПУМБ" height="20"
                                                                                                  src="images/pumbbank.png"
                                                                                                  width="20"/> PUMB</span></label>
                                                </div>
                                                <div class="w-30 mr-5">
                                                    <input type="checkbox" id="raiffeisenbank" name="banknames[]">
                                                    <label for="raiffeisenbank" class="tick"><span><img
                                                                alt="Райффайзенбанк" height="20"
                                                                src="images/raiffeisenbank.png" width="20"/> Raiffeisenbank</span></label>
                                                </div>

                                                <div class="clrfx mb-20"></div>
                                                <div class="w-30 mr-5">
                                                    <input type="checkbox" id="sberbank" name="banknames[]">
                                                    <label for="sberbank" class="tick"><span><img alt="Сбербанк"
                                                                                                  height="20"
                                                                                                  src="images/sberbank.png"
                                                                                                  width="20"/> Sberbank</span></label>
                                                </div>
                                                <div class="w-30 mr-5">
                                                    <input type="checkbox" id="sichbank" name="banknames[]">
                                                    <label for="sichbank" class="tick"><span><img alt="СІЧ" height="20"
                                                                                                  src="images/sichbank.png"
                                                                                                  width="20"/> Sichbank</span></label>
                                                </div>
                                                <div class="w-30 mr-5">
                                                    <input type="checkbox" id="tascombank" name="banknames[]">
                                                    <label for="tascombank" class="tick"><span><img alt="Таксомбанк"
                                                                                                    height="20"
                                                                                                    src="images/tascombank.png"
                                                                                                    width="20"/> Tascombank</span></label>
                                                </div>
                                                <div class="w-30 mr-5">
                                                    <input type="checkbox" id="ukreximbank" name="banknames[]">
                                                    <label for="ukreximbank" class="tick"><span><img alt="Укрексімбанк"
                                                                                                     height="20"
                                                                                                     src="images/ukreximbank.png"
                                                                                                     width="20"/> Ukreximbank</span></label>
                                                </div>
                                                <div class="w-30 mr-5">
                                                    <input type="checkbox" id="ukrgazbank" name="banknames[]">
                                                    <label for="ukrgazbank" class="tick"><span><img alt="Укргазбанк"
                                                                                                    height="20"
                                                                                                    src="images/ukrgazbank.png"
                                                                                                    width="20"/> Ukrgazbank</span></label>
                                                </div>
                                                <div class="w-30 mr-5">
                                                    <input type="checkbox" id="ukrsibbank" name="banknames[]">
                                                    <label for="ukrsibbank" class="tick"><span><img alt="Укрссиббанк"
                                                                                                    height="20"
                                                                                                    src="images/ukrsibbank.png"
                                                                                                    width="20"/> Ukrsibbank</span></label>
                                                </div>
                                                <div class="w-30 mr-5">
                                                    <input type="checkbox" id="unicreditbank" name="banknames[]">
                                                    <label for="unicreditbank" class="tick"><span><img
                                                                alt="Юнікредітбанк" height="20"
                                                                src="images/unicreditbank.png" width="20"/> Unicreditbank</span></label>
                                                </div>
                                                <div class="w-30 mr-5">
                                                    <input type="checkbox" id="universalbank" name="banknames[]">
                                                    <label for="universalbank" class="tick"><span><img
                                                                alt="Універсалбанк" height="20"
                                                                src="images/universalbank.png" width="20"/> Universalbank</span></label>
                                                </div>
                                                <div class="w-30 mr-5">
                                                    <input type="checkbox" id="vsbank" name="banknames[]">
                                                    <label for="vsbank" class="tick"><span><img alt="ВіЕсбанк"
                                                                                                height="20"
                                                                                                src="images/vsbank.png"
                                                                                                width="20"/> VSbank</span></label>
                                                </div>
                                                <div class="w-30 mr-5">
                                                    <input type="checkbox" id="vtbbank" name="banknames[]">
                                                    <label for="vtbbank" class="tick"><span><img alt="ВТБ банк"
                                                                                                 height="20"
                                                                                                 src="images/vtbbank.png"
                                                                                                 width="20"/> VTB</span></label>
                                                </div>
                                                <div class="w-30 mr-5">
                                                    <input type="checkbox" id="allbanks" name="banknames">
                                                    <label for="allbanks" class="tick"><span><img
                                                                alt="Select all" height="20"
                                                                src="images/bankdefault.png" width="20"/> Select all</span></label>
                                                </div>


                                                <div class="clrfx mb-20"></div>
                                                <div class="w-100 mr-5">
                                                    <div class="clrfx mt-10 mb-30 bt"><span class="inner-title">Select type(-s)</span>
                                                    </div>
                                                    <div class="w-30 mr-5">
                                                        <input type="checkbox" id="department">
                                                        <label for="department" class="tick"><span>Department</span></label>
                                                    </div>
                                                    <div class="w-30 mr-5">
                                                        <input type="checkbox" id="atm">
                                                        <label for="atm" class="tick"><span>ATM</span></label>
                                                    </div>
                                                    <div class="clrfx mt-10 mb-30 bt"><span class="inner-title">Additional options</span>
                                                    </div>
                                                    <div class="w-30 mr-5 mt-10">
                                                        <input type="text" class="form-control" id="find-from-location" placeholder="From"></div>
                                                    <div class="w-30 mr-5 mt-10">
                                                        <input type="checkbox" id="find-in-radius">
                                                        <label for="find-in-radius"
                                                               class="tick"><span>Find in radius</span></label>
                                                    </div>
                                                    <input id="ex6" type="text" data-slider-min="-150" data-slider-max="5000" data-slider-step="25" data-slider-value="100" data-slider-enabled="false"/>
                                                    <span id="ex6CurrentSliderValLabel" style="display: none;">Current Slider Value: <span id="ex6SliderVal">100</span></span>
                                                    <div class="w-30 mr-5 mt-10">
                                                        <input type="checkbox" id="find-closest">
                                                        <label for="find-closest"
                                                               class="tick"><span>Find closest</span></label>
                                                    </div>
                                                    <div class="w-30 mr-5 mt-10">
                                                        <input type="checkbox" id="only-active">
                                                        <label for="only-active"
                                                               class="tick"><span>Find only active</span></label>
                                                    </div>
                                                    <div class="w-30 mt-5">
                                                        <input type="button" class="small color green button" id = "aaa", value="Search" name ="send"
                                                               onclick="$('#locs').css('display', 'none');" />
                                                        <input type="button" class="small color blue button" value="Close panel"
                                                               id="close-filter-result-panel"
                                                               onclick="$('#locs').css('display', 'none'); $(this).css('display', 'none')"
                                                               hidden="true"/>
                                                    </div>
                                                </div>


                                                <div class="clrfx mt-10"></div>

                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
        <div id="menu">
            <ul id="nav">
                <div class="mapTypes">
                    <li><a href="#">Map type</a>
                        <ul>
                            <li id="roadmap"><a href="#">Roadmap</a></li>
                            <li id="satellite"><a href="#">Satellite</a></li>
                            <li id="hybrid"><a href="#">Hybrid</a></li>
                            <li id="terrain"><a href="#">Terrain</a></li>
                        </ul>
                    </li>
                </div>
                <div class="chat">
                    <li><a href="#">System chat</a></li>
                </div>
                <div class="log-out">
                    <li><a href="logout.php">Logout</a></li>
                </div>
            </ul>
        </div>
    </div>
</div>

<div id="feedback-popup">
    <a href="#close" class="close-overlay"></a>
    <section id="feedback">
        <div class="sb"><a href="#" class="entypo-cancel close-top"><span
                    class="feedback-tip right">Close window</span></a></div>
        <div class="feedback-form">
            <div class="title">Feedbacks</div>
            <p class="intro"><b>Welcome!</b> Please, enter your feedback below</p>
            <form action="./php/markerOperations.php" name="feedb" id="form-feedback" method="post">
                <div class="field">
                    <input name="itemid" type="text" id="item-id" hidden />
                </div>
                <div class="field">
                    <input name="userlogin" type="text" id="feedback-author" readonly />
                    <span class="entypo-user icon"></span>
                    <span class="feedback-tip left">You</span>
                </div>
                <div class="field">
                    <input name="name" type="text" id="item-address" readonly />
                    <span class="entypo-home icon"></span>
                    <span class="feedback-tip left">Bank info</span>
                </div>
                <div class="field">
                    <textarea name="feedbtext" placeholder="Feedback" id="feedback-text" required></textarea>
                    <span class="entypo-comment icon"></span>
                    <span class="feedback-tip left">Your feedback</span>
                </div>
                <div class="field">
                    <input name="userid" type="text" id="user-id" hidden />
                </div>

                <input type="submit" value="Send feedback" class="send" name="sendfeedback" />
            </form>
        </div>
    </section>
</div>

<div id="directions-explanation-panel" style="display: none;"></div>
<div id="locs" style="display: none;"></div>
<div id="map"></div>

<script>
    $(document).ready(function() {
        $("#ex6").slider();
        $("#ex6").on('slide', function(slideEvt) {
            $("#ex6SliderVal").text(slideEvt.value);
        });
        $("#find-in-radius").click(function() {
            if(this.checked) {
                $("#ex6").slider("enable");
                $("#ex6CurrentSliderValLabel").show();
            }
            else {
                $("#ex6").slider("disable");
                $("#ex6CurrentSliderValLabel").hide();
            }
        });
    });
</script>

<script>
    var map;
    var markers = [];
    var locids = [];
    var lviv = {
        lat: 49.839683,
        lng: 24.029717
    };
    var directionsDisplay;
    var autocomplete1;
    var autocomplete2;
    var autocomplete3;

    var lastinfowindow;

    var radiusCircle;
    var markerCluster;

    function create_marker(mId, mType, mName, mAddress, mStatus, mImage, mStartTime, mEndTime, isDragable, location) {
        var marker = new google.maps.Marker({
            locid: mId,
            position: location,
            map: map,
            name: mName,
            type: mType,
            address: mAddress,
            status: mStatus,
            startFrom: mStartTime,
            endTo: mEndTime,
            draggable: isDragable,
            icon: mImage
        });

        var sideHtml = '<p class="loc" data-locid="'+marker.locid+'"><b>' + mName + ' ' + mType + ' on ' + mAddress +
            '. Today works from ' + mStartTime + ' to ' + mEndTime + '</p>';
        $("#locs").append(sideHtml);
        var contentString = $('<div class="marker-info-win">' + '<div class="marker-inner-win"><span class="info-content">' + '<h1 class="marker-heading">' +
            mName + ' ' + mType + '</h1>' + mAddress + '<p>' + mStartTime + '-' + mEndTime + '</p>' + '</span></div></div>');
        var infowindow = new google.maps.InfoWindow();
        infowindow.setContent(contentString[0]);
        google.maps.event.addListener(marker, 'rightclick', function () {
            infowindow.open(map, marker);
        });

        google.maps.event.addListener(marker, 'click', function () {
            $("#item-id").val(marker['locid']);
            $("#feedback-author").val("<?php echo $obj['login']; ?>");
            $("#item-address").val(marker['address'] + ' (' + marker['type'] + ' ' + marker['name'] + ')');
            $("#user-id").val("<?php echo $obj['id']; ?>");

            window.location.href="#feedback-popup";
        });

        marker.infowindow = infowindow;

        google.maps.event.addListener(marker, 'dblclick', function () {
            var address = marker['address'];
            if ($("#first-address").val() === '') {
                $("#first-address").val(address);
            } else if ($("#second-address").val() === '') {
                $("#second-address").val(address);
            }
        });

        locids.push(marker['locid']);
        markers.push(marker);
    }

    function initMap() {
        map = new
        google.maps.Map(document.getElementById('map'), {
            zoom: 12,
            minZoom: 12,
//            maxZoom: 18,
            center: lviv,
            mapTypeControl: false
        });
        var styles = {
            default: null,
            hide: [{
                featureType: 'poi.business',
                stylers: [{
                    visibility: 'off'
                }]
            }, {
                featureType: 'transit',
                elementType: 'labels.icon',
                stylers: [{
                    visibility: 'off'
                }]
            }]
        };
        map.setOptions({
            styles: styles['hide']
        });
        map.setOptions({disableDoubleClickZoom: true});
        var lvivArea = new google.maps.Polygon({
            paths: lvivCoords,
            strokeColor: '#B0BBF0',
            strokeOpacity: 0.9,
            strokeWeight: 5,
            fillColor: '#B0BBF0',
            fillOpacity: 0.15
        });
        lvivArea.setMap(map);

        var strictBounds = new google.maps.LatLngBounds(new google.maps.LatLng(49.7679071, 23.9062801), new google.maps.LatLng(49.897471, 24.118191));
        google.maps.event.addListener(map, 'dragend', function () {
            if (strictBounds.contains(map.getCenter())) return;
            var c = map.getCenter(),
                x = c.lng(),
                y = c.lat(),
                maxX = strictBounds.getNorthEast().lng(),
                maxY = strictBounds.getNorthEast().lat(),
                minX = strictBounds.getSouthWest().lng(),
                minY = strictBounds.getSouthWest().lat();
            if (x < minX) x = minX;
            if (x > maxX) x = maxX;
            if (y < minY) y = minY;
            if (y > maxY) y = maxY;
            map.setCenter(new google.maps.LatLng(y, x));
        });
        var geocoder = new google.maps.Geocoder();

        google.maps.event.addListener(lvivArea, 'dblclick', function (event) {
            geocoder.geocode({
                'latLng': event.latLng
            }, function (results, status) {
                if (status == google.maps.GeocoderStatus.OK) {
                    if (results[0]) {
                        setAddress(results[0].formatted_address);
                    }
                }
            });
        });
        var directionsService = new google.maps.DirectionsService;
        $(".mapTypes ul li").click((e) => {
            map.setOptions({
            mapTypeId: $(e.currentTarget).attr('id')
        });
    });

        $('#allbanks').click(function() {
            if ($(this).is(':checked')) {
                $('input:checkbox[name="banknames[]"]').prop('checked', true);
            } else {
                $('input:checkbox[name="banknames[]"]').prop('checked', false);
            }
        });
        $('.find-way-option').on({
            "shown.bs.dropdown": function () {
                this.closable = false;
            },
            "click": function () {
                this.closable = true;
            },
            "hide.bs.dropdown": function () {
                return this.closable;
            }
        });

        $('.filter-option').on({
            "shown.bs.dropdown": function () {
                this.closable = false;
            },
            "click": function () {
                this.closable = true;
            },
            "hide.bs.dropdown": function () {
                return this.closable;
            }
        });

        document.getElementById("find-way").addEventListener("click", function () {
            if (directionsDisplay !== undefined && directionsDisplay !== null) {
                directionsDisplay.setMap(null);
                directionsDisplay.setPanel(null);
                directionsDisplay = null;
            }
            directionsDisplay = new google.maps.DirectionsRenderer();
            directionsDisplay.setMap(map);
            directionsDisplay.setPanel(document.getElementById('directions-explanation-panel'));
            $("#directions-explanation-panel").css('display', 'block');
            $("#clear-data").show();
            $("#close-explanation-panel").show();

            var address1 = $("#first-address").val();
            var address2 = $("#second-address").val();

            var addresses = [address1, address2];

            getPoints(addresses, function (coords) {
                calculateAndDisplayRoute(directionsService, directionsDisplay, coords[0], coords[1]);
            });

        }, false);

        document.getElementById("clear-data").addEventListener("click", function () {
            $("#directions-explanation-panel").css('display', 'none');
            $("#close-explanation-panel").hide();
            $("#first-address").val('');
            $("#second-address").val('');
            directionsDisplay.setMap(null);
        }, false);

        autocomplete1 = new google.maps.places.Autocomplete(
            (document.getElementById('first-address')), {
                types: ['geocode'],
                bounds: strictBounds,
                strictBounds: true
            });
        autocomplete2 = new google.maps.places.Autocomplete(
            (document.getElementById('second-address')), {
                types: ['geocode'],
                bounds: strictBounds,
                strictBounds: true
            });
        autocomplete3 = new google.maps.places.Autocomplete(
            (document.getElementById('find-from-location')), {
                types: ['geocode'],
                bounds: strictBounds,
                strictBounds: true
            });
        google.maps.event.addDomListener(document.getElementById('first-address'), 'focus');
        google.maps.event.addDomListener(document.getElementById('second-address'), 'focus');
        google.maps.event.addDomListener(document.getElementById('find-from-location'), 'focus');
        $('#aaa').click(function (event) {
            if (!$('#department').is(":checked") && !$('#atm').is(":checked"))
                alert("Please check department or/and atm!");
            else {
                types = [];
                if ($('#abank').is(":checked"))
                    types.push('abank');
                if ($('#accordbank').is(":checked"))
                    types.push('accordbank');
                if ($('#alphabank').is(":checked"))
                    types.push('alphabank');
                if ($('#apeksbank').is(":checked"))
                    types.push('apeksbank');
                if ($('#bmbank').is(":checked"))
                    types.push('bmbank');
                if ($('#btabank').is(":checked"))
                    types.push('btabank');
                if ($('#concordbank').is(":checked"))
                    types.push('concordbank');
                if ($('#creditagricolebank').is(":checked"))
                    types.push('creditagricolebank');
                if ($('#creditdniprobank').is(":checked"))
                    types.push('creditdniprobank');
                if ($('#expressbank').is(":checked"))
                    types.push('expressbank');
                if ($('#forwardbank').is(":checked"))
                    types.push('forwardbank');
                if ($('#grantbank').is(":checked"))
                    types.push('grantbank');
                if ($('#ideabank').is(":checked"))
                    types.push('ideabank');
                if ($('#industrialbank').is(":checked"))
                    types.push('industrialbank');
                if ($('#kredobank').is(":checked"))
                    types.push('kredobank');
                if ($('#lvivbank').is(":checked"))
                    types.push('lvivbank');
                if ($('#megabank').is(":checked"))
                    types.push('megabank');
                if ($('#mibbank').is(":checked"))
                    types.push('mibbank');
                if ($('#nadrabank').is(":checked"))
                    types.push('nadrabank');
                if ($('#neosbank').is(":checked"))
                    types.push('neosbank');
                if ($('#oksibank').is(":checked"))
                    types.push('oksibank');
                if ($('#oschadbank').is(":checked"))
                    types.push('oschadbank');
                if ($('#otpbank').is(":checked"))
                    types.push('otpbank');
                if ($('#pinbank').is(":checked"))
                    types.push('pinbank');
                if ($('#piraeusbank').is(":checked"))
                    types.push('piraeusbank');
                if ($('#pivdenniybank').is(":checked"))
                    types.push('pivdenniybank');
                if ($('#poltavabank').is(":checked"))
                    types.push('poltavabank');
                if ($('#praveksbank').is(":checked"))
                    types.push('praveksbank');
                if ($('#privatbank').is(":checked"))
                    types.push('privatbank');
                if ($('#procreditbank').is(":checked"))
                    types.push('procreditbank');
                if ($('#prominvestbank').is(":checked"))
                    types.push('prominvestbank');
                if ($('#pumbbank').is(":checked"))
                    types.push('pumbbank');
                if ($('#raiffeisenbank').is(":checked"))
                    types.push('raiffeisenbank');
                if ($('#sberbank').is(":checked"))
                    types.push('sberbank');
                if ($('#sichbank').is(":checked"))
                    types.push('sichbank');
                if ($('#tascombank').is(":checked"))
                    types.push('tascombank');
                if ($('#ukreximbank').is(":checked"))
                    types.push('ukreximbank');
                if ($('#ukrgazbank').is(":checked"))
                    types.push('ukrgazbank');
                if ($('#ukrsibbank').is(":checked"))
                    types.push('ukrsibbank');
                if ($('#unicreditbank').is(":checked"))
                    types.push('unicreditbank');
                if ($('#universalbank').is(":checked"))
                    types.push('universalbank');
                if ($('#vsbank').is(":checked"))
                    types.push('vsbank');
                if ($('#vtbbank').is(":checked"))
                    types.push('vtbbank');

                if (types.length !== 0) {
                    $("#close-filter-result-panel").show();
                    var departmentChecked = $('#department').is(":checked");
                    var atmChecked = $('#atm').is(":checked");
                    var isInRadius = false;
                    var isClosest = false;
                    var isOnlyActive = false;
                    if ($("#find-in-radius").is(":checked"))
                        isInRadius = true;
                    if ($("#find-closest").is(":checked"))
                        isClosest = true;
                    if ($('#only-active').is(":checked"))
                        isOnlyActive = true;
                    findBanksItems(types, departmentChecked, atmChecked, isInRadius, isClosest, isOnlyActive);

                } else
                    alert("Select at least one bank name from list!");
            }
        });

        $.get("./php/markerOperations.php?data=1", function (data) {
            $(data).find("marker").each(function () {
                var id = $(this).attr('id');
                var type = $(this).attr('type');
                var name = $(this).attr('name');
                var address = $(this).attr('address');
                var lat = parseFloat($(this).attr('lat'));
                var lng = parseFloat($(this).attr('lng'));
                var point = new google.maps.LatLng(lat, lng);
                var status = $(this).attr('status');
                var isActive = (status === 'active') ? name : name + 'off';
                var startTime = $(this).attr('start_time');
                var endTime = $(this).attr('end_time');
                create_marker(id, type, name, address, status, "images/" + isActive + ".png", startTime, endTime, false, point);
            });

            var options = {
                zoomOnClick: false,
                ignoreHidden: true
            };
            markerCluster = new MarkerClusterer(map, markers, options);
            google.maps.event.addListener(markerCluster, 'clusterclick', function(cluster) {
                var markers = cluster.getMarkers();

                var content = '';
                for (var i = 0; i < markers.length; i++) {
                    var marker = markers[i];
                    content += marker.name;
                    content += ("<br>");
                }
                var infowindow = new google.maps.InfoWindow();
                infowindow.setPosition(cluster.getCenter());
                infowindow.close();
                infowindow.setContent(content);
                infowindow.open(map);
                google.maps.event.addListener(map, 'zoom_changed', function() {
                    infowindow.close();
                });
            });
        });
    }

    $(document).on("click",".loc",function() {
        var thisloc = $(this).data("locid");
        for(var i=0; i<markers.length; i++) {
            if(locids[i] == thisloc) {
                if(lastinfowindow instanceof google.maps.InfoWindow) lastinfowindow.close();
                map.panTo(markers[i].getPosition());
                markers[i].infowindow.setPosition(markers[i].getPosition());
                markers[i].infowindow.open(map);
                lastinfowindow = markers[i].infowindow;
                break;
            }
        }
    });

    function findBankItemsInRadius(types, isDepartment, isATM, isActive) {
        var radius = $('#ex6').val();
        var address = $('#find-from-location').val();

        if (radiusCircle) {
            radiusCircle.setMap(null);
            radiusCircle = null;
        }

        var geocoder = new google.maps.Geocoder();
        if (geocoder) {
            geocoder.geocode({'address': address}, function (results, status) {
                if (status == google.maps.GeocoderStatus.OK) {
                    if (status != google.maps.GeocoderStatus.ZERO_RESULTS) {
                        var address_lat_lng = results[0].geometry.location;
                        radiusCircle = new google.maps.Circle({
                            strokeColor: '#FFAA43',
                            strokeOpacity: 0.5,
                            strokeWeight: 4,
                            fillColor: '#00aaaa',
                            fillOpacity: 0.2,
                            center: address_lat_lng,
                            radius: parseFloat(radius),
                            clickable: false,
                            map: map
                        });
                        if (radiusCircle)
                            map.fitBounds(radiusCircle.getBounds());
                        for (var i = 0; i < markers.length; i++) {
                            if (markers[i].getVisible() === false)
                                continue;
                            var sideDom = "p.loc[data-locid=" + (locids[i]) + "]";
                            $(sideDom).hide();
                            markers[i].setVisible(false);
                            for (var j = 0; j < types.length; ++j) {
                                if (markers[i].name === types[j]) {
                                    if ((markers[i].type === 'department' && isDepartment === true) || (markers[i].type === 'atm' && isATM === true)) {
                                        var marker_lat_lng = new google.maps.LatLng(markers[i].getPosition().lat(), markers[i].getPosition().lng());
                                        var distance_from_location = google.maps.geometry.spherical.computeDistanceBetween(address_lat_lng, marker_lat_lng);
                                        if (distance_from_location <= radius) {
                                            markers[i].setVisible(true);
                                            markers[i].checked = true;
                                            $(sideDom).show();
                                        }
                                    }
                                }
                            }
                        }
                        if (isActive === true)
                            findOnlyActiveBankItems(types, isDepartment, isATM);
                        markerCluster.repaint();
                    } else {
                        alert("No results found while geocoding!");
                    }
                } else {
                    alert("Geocode was not successful: " + status);
                }
            });
        }
    }

    function findClosestBankItems(types, isDepartment, isATM, isActive) {
        var address = $('#find-from-location').val();
        var sideDom;
        var geocoder = new google.maps.Geocoder();
        if (geocoder) {
            geocoder.geocode({'address': address}, function (results, status) {
                if (status == google.maps.GeocoderStatus.OK) {
                    if (status != google.maps.GeocoderStatus.ZERO_RESULTS) {
                        for (var z = 0; z < markers.length; ++z) {
                            if (markers[z].getVisible() === true) {
                                sideDom = "p.loc[data-locid=" + (locids[z]) + "]";
                                $(sideDom).hide();
                                markers[z].setVisible(false);
                            }
                        }
                        var address_lat_lng = results[0].geometry.location;
                        for (var j = 0; j < types.length; ++j) {
                            var closest = -1;
                            var distances = [];
                            for (var k = 0; k < markers.length; ++k) {
                                if (markers[k].name === types[j] && markers[k].type === 'department' && isDepartment === true) {
                                    var marker_lat_lng = new google.maps.LatLng(markers[k].getPosition().lat(), markers[k].getPosition().lng());
                                    var d = google.maps.geometry.spherical.computeDistanceBetween(address_lat_lng, marker_lat_lng);
                                    distances[k] = d;
                                    if (closest == -1 || d < distances[closest]) {
                                        closest = k;
                                    }
                                }
                            }
                            if (closest != -1) {
                                markers[closest].setVisible(true);
                                markers[closest].checked = true;
                                sideDom = "p.loc[data-locid=" + (locids[closest]) + "]";
                                $(sideDom).show();
                            }
                            var closest = -1;
                            var distances = [];
                            for (var k = 0; k < markers.length; ++k) {
                                if (markers[k].name === types[j] && markers[k].type === 'atm' && isATM === true) {
                                    var marker_lat_lng = new google.maps.LatLng(markers[k].getPosition().lat(), markers[k].getPosition().lng());
                                    var d = google.maps.geometry.spherical.computeDistanceBetween(address_lat_lng, marker_lat_lng);
                                    distances[k] = d;
                                    if (closest == -1 || d < distances[closest]) {
                                        closest = k;
                                    }
                                }
                            }
                            if (closest != -1) {
                                markers[closest].setVisible(true);
                                markers[closest].checked = true;
                                sideDom = "p.loc[data-locid=" + (locids[closest]) + "]";
                                $(sideDom).show();
                            }
                        }
                        if (isActive === true)
                            findOnlyActiveBankItems(types, isDepartment, isATM);
                        markerCluster.repaint();
                    } else {
                        alert("No results found while geocoding!");
                    }
                } else {
                    alert("Geocode was not successful: " + status);
                }
            });
        }
    }

    function findOnlyActiveBankItems(types, isDepartment, isATM) {
        for (var i = 0; i < markers.length; i++) {
            if (markers[i].getVisible() === false)
                continue;
            var sideDom = "p.loc[data-locid="+(locids[i])+"]";
            markers[i].setVisible(false);
            $(sideDom).hide();
            for (var j = 0; j < types.length; ++j) {
                if (markers[i].name === types[j]) {
                    markers[i].checked = true;
                    if (markers[i].status === 'not active')
                        break;
                    if ((markers[i].type === 'department' && isDepartment === true) || (markers[i].type === 'atm' && isATM === true)) {
                        $(sideDom).show();
                        markers[i].setVisible(true);
                        break;
                    }
                }
            }
        }
        markerCluster.repaint();
    }

    function findBankItemsByNames(types, isDepartment, isATM) {
        for (var i = 0; i < markers.length; i++) {
            if (markers[i].getVisible() === false)
                continue;
            var sideDom = "p.loc[data-locid="+(locids[i])+"]";
            markers[i].setVisible(false);
            $(sideDom).hide();
            for (var j = 0; j < types.length; ++j) {
                if (markers[i].name === types[j]) {
                    if ((markers[i].type === 'department' && isDepartment === true) || (markers[i].type === 'atm' && isATM === true)) {
                        markers[i].checked = true;
                        $(sideDom).show();
                        markers[i].setVisible(true);
                        break;
                    }
                }
            }
        }
        markerCluster.repaint();
    }

    function findBanksItems(types, isDepartment, isATM, isInRadius, isClosest, isOnlyActive) {
        $("#locs").css('display', 'block');

        if (radiusCircle) {
            radiusCircle.setMap(null);
            radiusCircle = null;
        }

        for (var i = 0; i < markers.length; ++i) {
            var sideDom = "p.loc[data-locid="+(locids[i])+"]";
            $(sideDom).hide();
            markers[i].setVisible(false);
            markers[i].checked = false;
            if ((markers[i].type === 'department' && isDepartment === true) || (markers[i].type === 'atm' && isATM === true)) {
                markers[i].setVisible(true);
                $(sideDom).show();
            }
        }

        markerCluster.repaint();

        if (isOnlyActive == false && isClosest == false && isInRadius == false)
            findBankItemsByNames(types, isDepartment, isATM);
        if (isOnlyActive == true && isClosest == false && isInRadius == false)
            findOnlyActiveBankItems(types, isDepartment, isATM);
        if (isClosest == true)
            findClosestBankItems(types, isDepartment, isATM, isOnlyActive);
        if (isInRadius == true)
            findBankItemsInRadius(types, isDepartment, isATM, isOnlyActive);
    }

    function setAddress(address) {
        if ($("#first-address").val() === '') {
            $("#first-address").val(address);
        } else if ($("#second-address").val() === '') {
            $("#second-address").val(address);
        }
    }

    function getPoints(addresses, callback) {
        var coords = [];
        for (var i = 0; i < addresses.length; i++) {
            currAddress = addresses[i];
            var geocoder = new google.maps.Geocoder();
            if (geocoder) {
                geocoder.geocode({'address': currAddress}, function (results, status) {
                    if (status == google.maps.GeocoderStatus.OK) {
                        coords.push(results[0].geometry.location);
                        if (coords.length == addresses.length) {
                            if (typeof callback == 'function') {
                                callback(coords);
                            }
                        }
                    }
                    else {
                        throw('No results found: ' + status);
                    }
                });
            }
        }
    }

    function calculateAndDisplayRoute(directionsService, directionsDisplay, pointA, pointB) {
        var selectedTravelMode = document.getElementById('travel-mode').value;
        directionsService.route({
            origin: pointA,
            destination: pointB,
            avoidTolls: true,
            avoidHighways: false,
            travelMode: google.maps.TravelMode[selectedTravelMode],
            provideRouteAlternatives: true
        }, function (response, status) {
            if (status == google.maps.DirectionsStatus.OK) {
                directionsDisplay.setDirections(response);
            } else {
                alert('Directions request failed due to ' + status);
            }
        });
    }


</script>

<script async defer
        src="https://maps.googleapis.com/maps/api/js?libraries=places&key=AIzaSyDjP7dvWo-KkDH7zCkGuzvrBJ_ZRCMiQ-s&callback=initMap&language=en-GB">
</script>

</body>

</html>