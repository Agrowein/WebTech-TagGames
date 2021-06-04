<?php
    setlocale(LC_ALL, "ru_RU.UTF-8");
    
    //Получаем запрос от от сайта
    $_myPost = file_get_contents('php://input');

    //Декодируем данные из строки в объекты
    $json = json_decode( $_myPost, true);

    //Заполняем поля
    $text = $json["text"];
    $word = $json["word"];

    //Ищем совпадения в строке
    $counter = 0; //счетчик совпадений
    $new_text = preg_replace_callback(
        '/('.$word.')/iu', 
        function ($m) {
            foreach ($m as &$value) {
                $value = '<span class="marked">'. $value . '</span>';
            }
            return $m[0];
        },
        $text,
        -1,
        $counter
    );
    

    //Формируем ответ

    //Задаем заголовки
    header('Content-Type: Application/JSON, charset: utf-8');

    //формируем тело ответа
    $response = array();
    $response["text"] = $new_text;
    $response["word"] = $word;
    $response["count"] = $counter;

    //Отправляем ответ
    echo json_encode( $response );
?>