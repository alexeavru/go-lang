<?php

include_once('libs/sql.php');

function dbSave($date) {

    global $host,$user,$password,$database;
    // подключаемся к SQL серверу
    $link = mysqli_connect($host, $user, $password, $database) or die("Ошибка " . mysqli_error($link));

    // Сохраняем в базу
    $query = "INSERT INTO test (test) VALUES ( '" . $date . "');";
    $res = mysqli_query($link,$query);

    //Закрываем соединение с БД.
    mysqli_close($link);
}

    date_default_timezone_set('Europe/Moscow');
    $dateFormat = new DateTime(); 
    $stringDate = $dateFormat->format('Y-m-d H:i:s');

    echo $stringDate;
    // Запишем в БД
    dbSave($stringDate);
    die();
?>