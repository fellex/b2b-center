<?php
/* Исходный код
function load_users_data($user_ids) {
    $user_ids = explode(',', $user_ids);
    foreach ($user_ids as $user_id) {
        $db = mysqli_connect("localhost", "root", "123123", "database");
        $sql = mysqli_query($db, "SELECT * FROM users WHERE id=$user_id");
        while($obj = $sql->fetch_object()){
            $data[$user_id] = $obj->name;
        }
        mysqli_close($db);
    }
    return $data;
}
// Как правило, в $_GET['user_ids'] должна приходить строка
// с номерами пользователей через запятую, например: 1,2,17,48
$data = load_users_data($_GET['user_ids']);
foreach ($data as $user_id=>$name) {
    echo "<a href=\"/show_user.php?id=$user_id\">$name</a>";
}
*/
/*
УЯЗВИМОСТИ
1. SQL-инъекция. Например, добавляем в адресную строку "?user_ids=1+AND+gender=2"
2. Отсутствие (или пустое значение) в адресной строке параметра "user_ids" приводит к ошибке
3. В функции "load_users_data" не объявлена переменная "$data". Значит эта функция может вернуть NULL, который попадет в foreach, и снова ошибка
4. Если SQL запрос будет неверным и mysqli_query вернет false, то в переменной "$sql" будет false. Тогда выражение $sql->fetch_object() сгенерирует ошибку
5. Подключение к базе данных открывается/закрывается в цикле...нет комментариев
6. Лучше перейти от прямого SQL-запроса к подготовленному
*/


function load_users_data($user_ids) {
    $data = []; // объявили переменную с пустым массивом
    if($user_ids == '' || is_null($user_ids)) { // если входные данные пустые, сразу вернем пустой массив
        return $data;
    }
    $user_ids = explode(',', $user_ids);
    //$db = new mysqli("localhost", "root", "123123", "database");
    $db = new mysqli("localhost", "root", "", "b2b-center");

    /* создание подготавливаемого запроса */
    $stmt = $db->prepare("SELECT `name` FROM `users` WHERE `id`=?");

    foreach ($user_ids as $user_id) {
        $user_id = intval($user_id); // мы знаем, что user_id имеет тип данных integer. Выполним преобразование данных, чтобы не пропустить лишнего в запрос

        /* связывание параметров с метками */
        $stmt->bind_param("i", $user_id); // привязка как integer
        /* выполнение запроса */
        $stmt->execute();
        /* связывание переменных с результатами запроса */
        $stmt->bind_result($user_name);
        /* получение значения */
        $stmt->fetch();

        if(!is_null($user_name)) {
            $data[$user_id] = $user_name;
        }
    }

    /* Завершить запрос */
    $stmt->close();
    /* Закрыть соединение */
    $db->close();

    return $data;
}
// Как правило, в $_GET['user_ids'] должна приходить строка
// с номерами пользователей через запятую, например: 1,2,17,48
$data = load_users_data($_GET['user_ids']);
foreach ($data as $user_id=>$name) {
    echo "<a href=\"/show_user.php?id=$user_id\">$name</a>";
}
