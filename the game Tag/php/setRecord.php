<?php
error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
    //Устанавливаем доступы к базе данных:
$host = 'localhost'; //имя хоста, на локальном компьютере это localhost
$user = 'u1048275_admin'; //имя пользователя, по умолчанию это root
$password = 'G62022417508'; //пароль, по умолчанию пустой
$db_name = 'u1048275_sitedatabase'; //имя базы данных
$link = mysqli_connect($host, $user, $password, $db_name) or die(mysqli_error($link));
mysqli_query($link, "SET NAMES 'utf8'");

request_process($link);
header('Content-Type: Application/JSON, charset: utf-8');
read_database($link);
		
//ф-ция обработки запроса	
function request_process($link) { 
	$_myPost = file_get_contents('php://input'); //получем post
	$json = json_decode( $_myPost, true);//декодируем из json в ассоциативный массив
	//запоминаем поля
	$userName=$json['username'];
	$userScore = $json['userscore'];
	if(!empty($userName)) {
	    $userName = clean($userName);
		if (check_length($userName, 2, 12)) {
		    $sql = "INSERT INTO user_score (name, score) VALUES ('$userName', '$userScore')";
	        mysqli_query($link, $sql);
		} else {
			header('Content-type: application/json');
			http_response_code(401);
		}
	} else {
		
    	http_response_code(201);
	}
}
//ф-ция чтения БД
function read_database($link) {
	//ВЫБРАТЬ все_столбцы ИЗ user_score ПО столбцу счета
		$query = "SELECT * FROM user_score ORDER BY score";
	//Преобразуем то, что отдала нам база в нормальный массив PHP $data:
		$data = array(); // в этот массив запишем то, что выберем из базы

        $ta = mysqli_query($link, $query) or die( mysqli_error($link) ); // сделаем запрос в БД
        while($row = mysqli_fetch_assoc($ta)){ // оформим каждую строку результата
                                              // как ассоциативный массив
            $data[] = $row; // допишем строку из выборки как новый элемент результирующего массива
        }
        echo json_encode($data); // и отдаём как json		
}


//ф-ция форматирования данных
function clean($value = "") {
    $value = trim($value); // удаляет пробелы в начале и конце
    $value = stripslashes($value); // удаляет символы экранирования
    $value = strip_tags($value);// удаляет теги
    $value = htmlspecialchars($value); // удаляет html код
return $value;
}
//ф-ция проверки длины строки
function check_length($value = "", $min, $max) {
    $result = (mb_strlen($value) < $min || mb_strlen($value) > $max);
return !$result;
}
?>