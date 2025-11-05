<?php

declare(strict_types=1);
/*
	ЗАДАНИЕ 1
	- Присвойте переменной $now значение метки времени актуальной даты(сегодня)
	- Присвойте переменной $birthday значение метки времени Вашего дня рождения
	- Создайте переменную $hour
	- С помощью функции getdate() присвойте переменной $hour текущий час
	*/
$now = time();
$birthday = mktime(13, 55, 00, 2, 21, 2006);
$date_i = getdate();
$hour = $date_i['hours'];
?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Использование функций даты и времени</title>
</head>

<body>
    <h1>Использование функций даты и времени</h1>
    <?php
    /*
	ЗАДАНИЕ 2
	- Используя управляющую конструкцию if – elseif - else присвойте 
	  переменной $welcome значение, изходя из следующих условий
	  если число в переменной $hour попадает в диапазон:
	  * от 0 до 6 - 'Доброй ночи'
	  * от 6 (включительно) до 12 - 'Доброе утро'
	  * от 12 (включительно) до 18 - 'Добрый день'
	  * от 18 (включительно) до 23 - 'Добрый вечер'
	  * Если число в переменной $hour не попадает ни в один из вышеперечисленных
	    диапазонов, то присвойте переменной $welcome значение 'Доброй ночи'
	- Выведите $welcome на отдельной строке
	- Установите локаль ru_RU.UTF-8
	- С помощью функции datefmt_format() на отдельной строке выведите 
	  текущую дату, месяц, год, день недели и время,
	  например, "Сегодня 1 сентября 2018 года, суббота 09:30:00" 
	- На отдельной строке выведите фразу "До моего дня рождения осталось "
	- Выведите количество дней, часов, минут и секунд оставшееся до Вашего дня рождения
	*/

    if ($hour >= 0 && $hour < 6) {
        $welcome = 'Доброй ночи';
    } elseif ($hour >= 6 && $hour < 12) {
        $welcome = 'Доброе утро';
    } elseif ($hour >= 12 && $hour < 18) {
        $welcome = 'Добрый день';
    } elseif ($hour >= 18 && $hour <= 23) {
        $welcome = 'Добрый вечер';
    } else {
        $welcome = 'Доброй ночи';
    }

    echo "<p>$welcome</p>";

    setlocale(LC_ALL, 'ru_RU.UTF-8');

    /**
     * Форматирует дату на русском языке
     * 
     * @param int $timestamp метка времени
     * @return string отформатированная дата
     */
    function formatRussianDate(int $timestamp): string
    {
        $months = [
            1 => 'января',
            2 => 'февраля',
            3 => 'марта',
            4 => 'апреля',
            5 => 'мая',
            6 => 'июня',
            7 => 'июля',
            8 => 'августа',
            9 => 'сентября',
            10 => 'октября',
            11 => 'ноября',
            12 => 'декабря'
        ];

        $daysOfWeek = [
            'воскресенье',
            'понедельник',
            'вторник',
            'среда',
            'четверг',
            'пятница',
            'суббота'
        ];

        $date = getdate($timestamp);
        $day = $date['mday'];
        $month = $months[$date['mon']];
        $year = $date['year'];
        $dayOfWeek = $daysOfWeek[$date['wday']];
        $time = date('H:i:s', $timestamp);

        return "Сегодня $day $month $year года, $dayOfWeek $time";
    }
    $formattedDate = formatRussianDate($now);
    echo $formattedDate . "<br>";

    $be = date('Y');
    $bm = (int) $_POST['Birthday_Month'];
    $bd = (int) $_POST['Birthday_day'];

    $date = "$be-$bm-$bd";
    $check_time = strtotime($date) - time();

    $months = floor($check_time / 2592000);
    $days = floor($check_time / 86400);
    $hours = floor(($check_time % 86400) / 3600);
    $minutes = floor(($check_time % 3600) / 60);

    echo "Осталось минут: $minutes <br>\n";
    echo "Осталось часов $hours<br>\n";
    echo "Осталось дней $days<br>\n";
    echo "Осталось месяцев $months<br>\n";
    ?>
</body>

</html>