<?php
function setConnectionInfo() {
    $connString = "mysql:host=localhost;dbname=webtoolrental;charset=utf8mb4";
    $user = "admin";
    $password = "YOUR_PASSWORD_HERE"; // set to your local MySQL password (see README)

    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        // Optional: if your PHP/PDO doesn’t honor charset in DSN, keep this:
        // PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_general_ci",
    ];

    return new PDO($connString, $user, $password, $options);
}
