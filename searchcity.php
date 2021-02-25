<?php

define("DB_HOST", "localhost");
define("DB_NAME", "***");
define("DB_USER", "***");
define("DB_PASSWORD", "***");
define("PREFIX", ""); //Префикс если нужно

$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
$mysqli->query("SET NAMES 'utf8'") or die ("Ошибка соединения с базой!");

if (!empty($_POST["referal"])) { //Принимаем данные

    $referal = trim(strip_tags(stripcslashes(htmlspecialchars($_POST["referal"]))));

    $db_referal = $mysqli->query("SELECT * FROM `geo_city`" . PREFIX . "search WHERE name LIKE '%$referal%'")
    or die('Ошибка №' . __LINE__);

    while ($row = $db_referal->fetch_array()) {
        echo "\n<li>" . $row["name"] . "</li>"; //$row["name"] - имя таблицы
    }

}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    use jquery
</head>
<body>
<form class="inputsearch" >
    <input type="text" name="referal" placeholder="Введите город" value="" class="who"  autocomplete="off">
    <ul class="search_result" style="display:none"></ul>
</form>
</body>
<script>
    $(function(){
        $('.who').bind("change keyup input click", function() {
            if(this.value.length >= 2){
                $.ajax({
                    type: 'post',
                    url: "assets/data/searchcity.php", //Путь к обработчику
                    data: {'referal':this.value},
                    response: 'text',
                    success: function(data){
                        $(".search_result").html(data).fadeIn(); //Выводим полученые данные в списке
                    }
                })
            }
        })

        $(".search_result").hover(function(){
            $(".who").blur(); //Убираем фокус с input
        })

        //При выборе результата поиска, прячем список и заносим выбранный результат в input
        $(".search_result").on("click", "li", function(){
            var s_user = $(this).text();
            $(".who").val(s_user).attr('disabled', 'disabled'); //деактивируем input, если нужно
            $(".search_result").fadeOut();
            var urlSearch = s_user.replace(/\s+/g, '').toLowerCase();
            var region_code = $(this).find(".-num").text();   //перезаписываем куки
            var region_name = $(this).find(".-name").text();
            //  {"region_name":"Белгородская область","region_code":"31"}
            var preliminary_region_obj = {
                region_name: region_name,
                region_code: region_code
            };
            var preliminary_region = JSON.stringify(preliminary_region_obj);
            //alert(preliminary_region);
            document.cookie = "preliminary_region=" + preliminary_region;
            document.cookie = "dacity=" + region_code + "; path=/";
            document.cookie = "identyfyon=" + region_code + "; path=/";
            document.location.href = '/' + urlSearch + '/';

            daCityNum = region_code;
            if (!isNaN(dacity)) {
                daCityNum = dacity;
            }

        })
    })
</script>
</html>
