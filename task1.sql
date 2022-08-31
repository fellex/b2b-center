# создаем таблицы
/*CREATE TABLE `users` (
    `id`         INT(11) NOT NULL AUTO_INCREMENT,
    `name`       VARCHAR(255) DEFAULT NULL,
    `gender`     INT(11) NOT NULL COMMENT '0 - не указан, 1 - мужчина, 2 - женщина.',
    `birth_date` INT(11) NOT NULL COMMENT 'Дата в unixtime.',
    PRIMARY KEY (`id`)
);
CREATE TABLE `phone_numbers` (
    `id`      INT(11) NOT NULL AUTO_INCREMENT,
    `user_id` INT(11) NOT NULL,
    `phone`   VARCHAR(255) DEFAULT NULL,
    PRIMARY KEY (`id`)
);*/

# выключаем ONLY_FULL_GROUP_BY для MySQL
SET sql_mode = '';

# добавляем индекс по полю `user_id`
ALTER TABLE `phone_numbers` ADD INDEX `user_id` (`user_id`);

# Получаем имя и число указанных телефонных номеров девушек в возрасте от 18 до 22 лет.
SELECT `u`.`name`, COUNT(`p`.`id`) AS `count_phones`
FROM `users` AS `u`
LEFT JOIN `phone_numbers` AS `p` ON `p`.`user_id` = `u`.`id`
WHERE `birth_date` <= UNIX_TIMESTAMP(NOW() + INTERVAL -18 YEAR) -- 18 лет назад
AND `birth_date` >= UNIX_TIMESTAMP(NOW() + INTERVAL -22 YEAR) -- 22 года назад
AND `gender` = 2 -- только женщины
GROUP BY `u`.`id`
