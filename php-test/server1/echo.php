<?php

include_once('libs/sql.php');

function dbSave($date) {

    global $host,$user,$password,$database;
    // подключаемся к SQL серверу
    $link = mysqli_connect($host, $user, $password, $database) or die("Ошибка " . mysqli_error($link));

    // Сохраняем в базу
    $query = "INSERT INTO test (test) VALUES ( '" . $date . "');";
    $res = mysqli_query($link,$query);

    $query = "SELECT test FROM test ORDER BY test DESC;";
    $res = mysqli_query($link,$query);
    $table_data = '';
    while($row = mysqli_fetch_array($res)) {
        $table_data = $table_data . $row['test'].'<br>\n';
    }

    //Закрываем соединение с БД.
    mysqli_close($link);
    return $table_data;
}

    date_default_timezone_set('Europe/Moscow');
    $dateFormat = new DateTime(); 
    $stringDate = $dateFormat->format('Y-m-d H:i:s');

    // Запишем в БД и возьмем результат
    $table_data = dbSave($stringDate);

    // Вывод текущей даты
    echo '{"date": "' . $stringDate . '", "table": "' . $table_data . '"}';

    die();
?>