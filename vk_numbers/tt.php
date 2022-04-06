<html>
    <head>
        <meta charset="UTF-8">
        <style>
        #com{
            visibility: visible;
            width:300px;
            height:50px;
        }
        #num{
            border:0px;
            font-size:x-large;
            font-weight: bold;
        }
        body{
            font-size:x-large;
        }
        #users{
            border:0px;
            float: right;
        }
        </style>
    </head>
    <body>
        <form id = "first" method="POST">
            <input type="submit" name="in" value="Войти в аккаунт" />
            <input type="submit" name="anon" value="Не авторизовываться" />
        </form>
    </body>
</html>
<?php
    //Читаем json
    try {
        $jsonString = file_get_contents('tel.json');
        $data = json_decode($jsonString, true);
    }catch (Exception $e) {
        echo 'Выброшено исключение: ',  $e->getMessage(), "\n";
    }
    
    //Авторизованный или нет
    if( isset( $_POST['in'] ) )
    {
        $users = "Я пользователь";
        user($users);
    }
    //Авторизованный или нет
    if( isset( $_POST['anon'] ) )
    {
        $users = "Аноним";
        user($users);
    }
    function user($users)
    {
        echo '<form method="POST"> <input name="tel" type="number" value=""> <input type="submit" name="but" value="Найти" /> <input id="users" type="text" name="user" value="'.$users.'" readonly/> </form>';
    }
    
    //Функция показа информации о номере(ах)
    function show_num($value,$data)
    {
        $users = $_POST['user'];
        $с = 0;
        echo '<form method="POST"> <input name="tel" type="number"> <input type="submit" name="but" value="Найти" /> <input id="users" type="text" name="user" value="'.$users.'" readonly/>';
        echo 'Ваш поиск <input id ="num" name="val" type="text" value="'.$value.'" readonly><br><br>';
        for($i = 0; $i<count($data); $i++)
        {
            $find = strpos($data[$i]["number"],$value);
            if($find !== false)
            {
                $comments = $data[$i]["comments"];
                echo "Номер: <b>".$data[$i]["number"]."</b><br>";
                echo "Страна: ".$data[$i]["country"]."<br>";
                echo "Оставить отзыв"."<br>".'<input id="com" name="'.$data[$i]["number"].'" type="text" value=""><input type="submit" name="send_com" value="Отправить" /><br>ОТЗЫВЫ:<br> ';
                if(count($comments)!=0)
                {
                    for($j = 0; $j<count($comments); $j++)
                    {
                        echo "Никнейм: ".$comments[$j]["name"]."<br>";
                        echo $comments[$j]["message"]."<br>";
                        echo "Лайки: ".$comments[$j]["likes"]." Дизлайки: ".$comments[$j]["dislikes"]."<br>";
                        if($users!=="Аноним")
                        {
                            echo '<input type="submit" name="'.$data[$i]["number"].$comments[$j]["id"]."like".'" value="👍"/><input type="submit" name="'.$data[$i]["number"].$comments[$j]["id"]."dislike".'" value="👎" /><br>';
                        }
                        echo "<br>";
                            
                    }
                    echo '<br><br><br>';
                }
                $c = $c + 1;
            }
        }
        if($c==0)
        {
            echo "Такого номера в нашей базе нет";
        }
        echo '</form>';
    }
    
    //При нажатии на кнопку поиска
    if( isset( $_POST['but'] ) )
    {
        $value = $_POST['tel'];
        show_num($value,$data);
    }
    
    //При нажатии на кнопку оставить комментарий
    if( isset( $_POST['send_com'] ) )
    {
        $value = $_POST['val'];
        $users = $_POST['user'];
        for($i = 0; $i<count($data); $i++)
        {
            $find = strpos($data[$i]["number"],$value);
            if($find !== false)
            {
                $comments = $data[$i]["comments"];
                $mes = $_POST[$data[$i]["number"]];
                if($mes!=NULL)
                {
                    array_push($data[$i]["comments"], ["id" => count($data[$i]["comments"]),"name" => $users,"message" => $mes, "likes" => 0, "dislikes" => 0]);
                }
          	    $newJsonString = json_encode($data);
          	    try {
                    file_put_contents('tel.json', $newJsonString);
          	    }catch (Exception $e) {
                    echo 'Выброшено исключение: ',  $e->getMessage(), "\n";
                }
            }
        }
        show_num($value,$data);
    }
    
    //При нажатии на кнопку лайка или дизлайка
    for($i = 0; $i<count($data); $i++)
    {
        $comments = $data[$i]["comments"];
        for($j = 0; $j<count($comments); $j++)
        {
            if(isset( $_POST[$data[$i]["number"].$comments[$j]["id"]."like"]) )
            {
                $value = $_POST['val'];
                $data[$i]["comments"][$j]["likes"] = $data[$i]["comments"][$j]["likes"] + 1;
                $newJsonString = json_encode($data);
                try {
                    file_put_contents('tel.json', $newJsonString);
                }catch (Exception $e) {
                    echo 'Выброшено исключение: ',  $e->getMessage(), "\n";
                }
                show_num($value,$data);
            }
            if(isset( $_POST[$data[$i]["number"].$comments[$j]["id"]."dislike"]) )
            {
                $value = $_POST['val'];
                $data[$i]["comments"][$j]["dislikes"] = $data[$i]["comments"][$j]["dislikes"] + 1;
                $newJsonString = json_encode($data);
                try{
                    file_put_contents('tel.json', $newJsonString);
                }catch (Exception $e) {
                    echo 'Выброшено исключение: ',  $e->getMessage(), "\n";
                }
                show_num($value,$data);
            }
        }
    }
?>
