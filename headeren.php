<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Beautyblog Maksonchika</title>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css">
    <link rel="stylesheet" href="/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Swiper/4.4.6/css/swiper.min.css">
    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="/chosen.css">
</head>
<body>
<div class="upper">
    <h3>Thanks for the help to these <a href="https://www.w3schools.com">guys</a></h3>
</div>
<header>
    <nav>
        <ul class="topmenu"  style="margin-left: 17.5%">
            <li><a href="/en/">Main</a></li>
            <li><a href="/about/en/">About me</a></li>
            <?php if (isset($_SESSION['session_username'])):?>
            <li class="submenu-link-li"><a href="#" class="submenu-link" style="pointer-events: none; cursor: default">Other</a>
                <ul class="submenu">
                        <li><a href="/labs/testlaba/ru/">Cameras</a></li>
                        <li><a href="/labs/kursach/ru/">Tables</a></li>
                        <li><a href="/labs/kursach/lessons/">Schedule</a></li>
                </ul>
            </li>
            <?php else:?>
            <li><a href="/labs/kursach/lessons/">Schedule</a></li>
            <?php endif;?>
            <li class="submenu-link-li"><a href="" class="submenu-link" style="pointer-events: none; cursor: default">Language</a>
                <ul class="submenu">
                    <li><a href="<?="../ru"?>">Русский <img src="/images/ru.png" width="24" height="20" style="float: right; margin-top: -2px"></a></li>
                    <li><a href="#">English <img src="/images/en.png" width="24" height="20" style="float: right; margin-top: -2px"></a></li>
                </ul>
            </li>
        </ul>
    </nav>
</header>

<?php
$date = file_get_contents($_SERVER['DOCUMENT_ROOT']."/test.txt");
$date .= date(DATE_RFC822)."\t".$_SERVER["REMOTE_ADDR"]."\r\n";
file_put_contents($_SERVER['DOCUMENT_ROOT']."/test.txt", $date);