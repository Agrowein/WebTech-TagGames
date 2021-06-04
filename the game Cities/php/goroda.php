<?php
//задаем заголовок запроса
header("Content-Type: text/plain; charset=UTF-8");
//Начинаем игровую сессиию
session_start();

if (!array_key_exists("towns", $_SESSION))
    $_SESSION["towns"] = [];

$towns = file("..\assets\goroda_rossii.txt");
$towns = array_map("trim", $towns);
$towns_lower = array_map("mb_strtolower", $towns);

//обработка сигналов от пользователя
$act = array_key_exists("act", $_GET) ? $_GET["act"] : null;
switch ($act) {
    case "new_game": //пользователь начал новую игру
        echo "Это игра в города. Я начну\n";
        $_SESSION["towns"] = [];
        computer_turn();
        break;
    case "new_turn": //пользователь ответил
        if (!array_key_exists("town", $_GET))
            continue;
        $town = trim($_GET["town"]);
        $town_lower = mb_strtolower($town);
        if (!check_town_exists($town_lower)) {
            echo "> " . $town . "\n";
            echo "Я не знаю такого города\n";
        } else if (!check_town_new($town_lower)) {
            echo "> " . $town . "\n";
            echo "Этот город уже был\n";
        } else if (!check_town_match($town_lower)) {
            echo "> " . $town . "\n";
            echo "Тебе нужно назвать город на " . get_next_letter() . "\n";
        } else {
            echo "> " . get_comment($town);
            save_town($town_lower);
            computer_turn();
        }
        break;
    default:
        http_response_code(400);
        echo("Неверный запрос");
}

//функция хода компьютера
function computer_turn()
{
    $town = get_rand_town();
    if (!$town) {
        echo "Я не знаю больше городов на эту букву. Начнём сначала? Твой ход\n";
        $_SESSION["towns"] = [];
        exit;
    }
    save_town($town);
    echo(get_comment($town));
    if (count(get_towns_for_next_letter()) == 0) {
        echo "Но если честно, я не знаю больше городов на эту букву. Поэтому, давай начнём игру сначала. Твой ход\n";
        $_SESSION["towns"] = [];
        exit;
    }
}
//функция выбора случайного города
function get_rand_town()
{
    $towns = get_towns_for_next_letter();
    if (count($towns) == 0)
        return false;
    return $towns[array_rand($towns)];
}

//функция поиска города по последней букве предыдущего слова
function get_towns_for_next_letter()
{
    $towns = $GLOBALS["towns"];
    $towns = array_filter($towns, "check_town_new");
    $letter = get_next_letter();
    if ($letter) {
        $towns = array_filter($towns, function ($town) use ($letter) {
            return starts_with(mb_strtolower($town), $letter);
        });
    }
    return $towns;
}
//получение следующей буквы слова
function get_next_letter()
{
    $last = end($_SESSION["towns"]);
    $letter = $last ? get_last_letter($last) : "";
    return $letter;
}
//получение последней буквы слова
function get_last_letter($town_lower)
{
    $chars = array_reverse(chars($town_lower));
    foreach ($chars as $char)
        if (!in_array($char, ["ы", "й", "ь", "ъ", " ", "-"]))
            return $char;
    return end($chars);
}

//впомогательные функции



function check_town_new($town_lower)
{
    return !in_array($town_lower, $_SESSION["towns"]);
}

function save_town($town_lower)
{
    $_SESSION["towns"][] = $town_lower;
}

function get_first_letter($town_lower)
{
    return chars($town_lower)[0];
}

function starts_with($town_lower, $letter)
{
    return get_first_letter($town_lower) == $letter;
}

function check_town_match($town_lower)
{
    $last = end($_SESSION["towns"]);
    if (!$last)
        return true;
    return starts_with($town_lower, get_last_letter($last));
}

function check_town_exists($town_lower)
{
    return in_array($town_lower, $GLOBALS["towns_lower"]);
}

function chars($word)
{
    return preg_split('//u', $word, null, PREG_SPLIT_NO_EMPTY);
}

function get_comment($town)
{
    return $town . ". Тебе на " . mb_strtoupper(get_last_letter($town)) . "\n";
}