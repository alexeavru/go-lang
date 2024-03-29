<?php

include_once('libs/sql.php');

date_default_timezone_set('Europe/Moscow');
$dateFormat = new DateTime(); 
$stringDate = $dateFormat->format('Y-m-d H:i:s');

function getDataFromDB() {
    global $host,$user,$password,$database;
    // подключаемся к SQL серверу
    $link = mysqli_connect($host, $user, $password, $database) or die("Ошибка " . mysqli_error($link));

    $query = "SELECT test FROM test ORDER BY test DESC;";
    $result = $link->query($query);
    $table_data = '';
    while($row = mysqli_fetch_array($result)) {
        $table_data = $table_data . $row['test']."<br>\n";
    }

    //Закрываем соединение с БД.
    mysqli_close($link);
    return $table_data;
}

// Выборка из БД
$table_data = getDataFromDB();

?>

<!DOCTYPE html>

<head >

    <meta charset="utf-8">

    <title>Server</title>
    <meta name="description" content="">

    <!-- Header CSS (First Sections of Website: paste after release from _header.min.css here) -->
    <style></style>

    <!-- Load CSS Compilled without JS -->
    <noscript>
        <link rel="stylesheet" href="css/style.css">
    </noscript>

    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

    <link rel="shortcut icon" href="img/favicon/favicon.ico" type="image/x-icon">

</head>

<body class="ishome">

    <header class="main-head">

        <div class="container" style="padding: 0;">
            <div class="row">

                <h3><div id="message"><?= $stringDate ?></div></h3>
                <button onclick="clickMe()"> Update </button>

            </div>
            <div class="row">

                <h3><div id="table"><?= $table_data ?></div></h3>

            </div>

        </div>

    </header>

    <script>

        function clickMe(){
            $.ajax({url:"echo.php", 
                success: function(result){
                    var obj = jQuery.parseJSON(result);
                    $("#message").html(obj.date);
                    $("#table").html(obj.table);
                }
            })
        } 

    </script>


<!-- Load Scripts -->
<script>var scr = {"scripts":[
    {"src" : "js/jquery-1.11.2.min.js", "async" : false},
    ]};!function(t,n,r){"use strict";var c=function(t){if("[object Array]"!==Object.prototype.toString.call(t))return!1;for(var r=0;r<t.length;r++){var c=n.createElement("script"),e=t[r];c.src=e.src,c.async=e.async,n.body.appendChild(c)}return!0};t.addEventListener?t.addEventListener("load",function(){c(r.scripts);},!1):t.attachEvent?t.attachEvent("onload",function(){c(r.scripts)}):t.onload=function(){c(r.scripts)}}(window,document,scr);
</script>


</body>
</html>

