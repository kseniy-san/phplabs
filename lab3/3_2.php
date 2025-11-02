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
$hour = getdate();
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

    echo "<p>$welcome</p>" ;

    setlocale(LC_ALL, 'ru_RU.UTF-8');

    
    ?>
</body>

</html>



    // Форматирование даты с помощью IntlDateFormatter
    if (class_exists('IntlDateFormatter')) {
    $formatter=new IntlDateFormatter( 'ru_RU' ,
    IntlDateFormatter::FULL,
    IntlDateFormatter::MEDIUM, 'Europe/Moscow' ,
    IntlDateFormatter::GREGORIAN
    );

    // Устанавливаем кастомный паттерн для нужного формата
    $formatter->setPattern("'Сегодня' d MMMM Y 'года,' eeee HH:mm:ss");
    $formatted_date = $formatter->format($now);
    echo "<p>$formatted_date</p>";
    } else {
    // Альтернатива если Intl не установлен
    $formatted_date = strftime("Сегодня %d %B %Y года, %A %H:%M:%S", $now);
    echo "<p>$formatted_date</p>";
    }

    // Вычисление времени до дня рождения
    $time_until_birthday = $birthday - $now;

    if ($time_until_birthday > 0) {
    $days = floor($time_until_birthday / (60 * 60 * 24));
    $hours = floor(($time_until_birthday % (60 * 60 * 24)) / (60 * 60));
    $minutes = floor(($time_until_birthday % (60 * 60)) / 60);
    $seconds = $time_until_birthday % 60;

    echo "<p>До моего дня рождения осталось:</p>";
    echo "<p>$days дней, $hours часов, $minutes минут, $seconds секунд</p>";
    } else {
    // Если день рождения уже прошел в этом году, вычисляем для следующего года
    $next_year_birthday = mktime(0, 0, 0, 12, 15, date('Y') + 1);
    $time_until_birthday = $next_year_birthday - $now;

    $days = floor($time_until_birthday / (60 * 60 * 24));
    $hours = floor(($time_until_birthday % (60 * 60 * 24)) / (60 * 60));
    $minutes = floor(($time_until_birthday % (60 * 60)) / 60);
    $seconds = $time_until_birthday % 60;

    echo "<p>До моего дня рождения осталось:</p>";
    echo "<p>$days дней, $hours часов, $minutes минут, $seconds секунд</p>";
    }