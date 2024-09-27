<?php

// Задание 1. Посмотрите на реализацию функции в файле fwrite-cli.php в исходниках. Может ли пользователь ввести некорректную информацию (например, дату в виде 12-50-1548)?
// Какие еще некорректные данные могут быть введены?
// Исправьте это, добавив соответствующие обработки ошибок.

// В данный момент времени проверка идет, если:
// a. Пользователь ввел дату не в формате ДД-ММ-ГГГГ, то есть если (количество знаков "-" в дате было меньше 2) if(count($dateBlocks) < 3) return false;
// Можно сделать проверку чтобы было ровно 3 части даты, то есть равенство на 2 знака "-")

// b. Пользователь ввел дату в которой количество дней более 31 if(isset($dateBlocks[0]) && $dateBlocks[0] > 31) return false;
// Можно сделать проверку чтобы количество дней не более 31 и не менее 1

// c. Пользователь ввел дату в которой количество месяцев более 12 if(isset($dateBlocks[1]) && $dateBlocks[0] > 12) return false;
// Можно сделать проверку чтобы количество месяцев не более 12 и не менее 1

// d. Пользователь ввел дату в которой год больше текущего if(isset($dateBlocks[2]) && $dateBlocks[2] > date('Y')) return false;
// Можно сделать проверку чтобы год не был больше текущего и не делал человека живущим более 150 лет

// e. Можно сделать проверку, что части даты можно преобразовать к числу

function validate(string $date): bool {
    $dateBlocks = explode("-", $date);

    if(count($dateBlocks) !== 3){ // Условие 1
        return false;
    }

    if(isset($dateBlocks[0]) && ($dateBlocks[0] > 31 || $dateBlocks[0] < 1)) { // Условие 2
        return false;
    }

    if(isset($dateBlocks[1]) && ($dateBlocks[1] > 12 || $dateBlocks[1] < 1)) { // Условие 3
        return false;
    }

    if(isset($dateBlocks[2]) && ($dateBlocks[2] > date('Y') || ($dateBlocks[2] < date('Y') - 150))) { // Условие 4
        return false;
    }

    if((isset($dateBlocks[0]) && !is_numeric($dateBlocks[0])) || (isset($dateBlocks[1]) && !is_numeric($dateBlocks[1])) || (isset($dateBlocks[2]) && !is_numeric($dateBlocks[2]))) { // Условие 5
        return false;
    }

    return true;
}

// $date = readline("Введите дату рождения в формате ДД-ММ-ГГГГ: "); //
$date = '11-11-1999';
echo (validate($date)) ? 'Дата '.$date.' корректна'.PHP_EOL : 'Дата '.$date.' не корректна'.PHP_EOL;
$date = 'qw-13-2027';
echo (validate($date)) ? 'Дата '.$date.' корректна'.PHP_EOL : 'Дата '.$date.' не корректна'.PHP_EOL;


// Задание 2. Поиск по файлу. Когда мы научились сохранять в файле данные, нам может быть интересно не только чтение, но и поиск по нему.
// Например, нам надо проверить, кого нужно поздравить сегодня с днем рождения среди пользователей, хранящихся в формате Василий Васильев, 05-06-1992
// И здесь нам на помощь снова приходят циклы. Понадобится цикл, который будет построчно читать файл и искать совпадения в дате.
// Для обработки строки пригодится функция explode, а для получения текущей даты – date.

function find_user_by_birthday(string $path, string $birthday) {

    if (file_exists($path) && is_readable($path)) {
        $file = fopen($path, "rb");
        $result = [];
        while (!feof($file)) {
            $line = fgets($file);
            $userDataArr = explode(',', $line);
            if (count($userDataArr) !== 2) continue; //Пропускаем возможную некорректную строку
            $clearBirthday = trim($userDataArr[1]);
            if ($clearBirthday === $birthday) {
                $result[] = $line;
            }
        }

        fclose($file);
    }
    else {
        echo("Файл не существует");
    }
    return $result;
}

// $birthday = readline("Введите дату рождения в формате ДД.ММ.ГГГГ: "); //
$birthday = '04.05.2005';
$filePath = '//cli//example_copy.txt';
$birthdayPeople = find_user_by_birthday($filePath, $birthday);
foreach ($birthdayPeople as $key => $value) echo $value;


// Задание 3. Удаление строки. Когда мы научились искать, надо научиться удалять
// конкретную строку. Запросите у пользователя имя или дату для удаляемой строки.
// После ввода либо удалите строку, оповестив пользователя, либо сообщите о том,
// что строка не найдена.
// Придется перезаписывать файл. То есть, нужно выгрузить данные в память,
// найти удаляемую строку, а затем записать файл заново.

function delete_user_by_birthday(string $path, string $birthday) {

    $deletedUsers = [];

    if (file_exists($path) && is_readable($path)) {
        $file = fopen($path, "rb"); // Старый файл откуда читаем данные
        $newPath = $path.'_'; // Название нового файла
        if (file_exists($newPath)) unlink($newPath); // Удаляем новый файл, если он существует
        $newFile = fopen($newPath, "x"); // Новый файл куда пишем данные

        while (!feof($file)) {
            $line = fgets($file); // Берем строку из старого файла
            $userDataArr = explode(',', $line);
            if (count($userDataArr) !== 2) continue; //Пропускаем возможную некорректную строку
            $clearBirthday = trim($userDataArr[1]);
            if ($clearBirthday === $birthday) { // Если найден пользователь с указанным днем рождения
                $deletedUsers[] = $line;
                continue;
            }
            fputs($newFile, $line);  // Помещаем строку из старого файла в новый файл
        }

        fclose($file); // Закрываем старый файл
        fclose($newFile); // Закрываем новый файл
        unlink($path); // Удаляем старый файл unlink()
        rename($newPath, $path); // Переименовываем новый файл как старый rename()
    }
    else {
        echo("Файл не существует");
    }

    return $deletedUsers;
}

// $birthdayToDelete = readline("Введите дату рождения в формате ДД.ММ.ГГГГ: "); //
$birthdayToDelete = '23.09.2024';
$deletedUsers = delete_user_by_birthday($filePath, $birthdayToDelete);

if (count($deletedUsers) > 0) {
    echo "Удалены:".PHP_EOL;
    foreach ($deletedUsers as $key => $value) echo $value;
} else {
    echo PHP_EOL."Пользователи с таким днем рождения не обнаружены...";
}


// 4. Добавьте новые функции в итоговое приложение работы с файловым
// хранилищем.

// Функцию validate корректирую в файле fwrite-cli.php,
// Код функций function find_user_by_birthday(string $path, string $birthday) и function delete_user_by_birthday(string $path, string $birthday)
// добавляю в файл file.function.php, а также соотвественным образом исправляю функцию parseCommand() в файле main.function.php

// docker run --rm -it -v ${pwd}/:/cli php:8.2-cli php /cli/homework3.php