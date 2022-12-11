<?php

try {
    echo 'Current PHP version: ' . phpversion();
    echo '<br />';

    $config = parse_ini_file('../.env');

    $host = $config['DB_HOST'] ?? 'db';
    $dbname = $config['DB_DATABASE'] ?? 'database';
    $user = $config['DB_USERNAME'] ?? 'user';
    $pass = $config['DB_PASSWORD'] ?? 'pass';
    $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8";
    $conn = new PDO($dsn, $user, $pass);

    echo 'Database connected successfully';
    echo '<br />';
    echo '<pre>';
    foreach ($conn->query('SHOW TABLES') as $row) {
        echo var_dump($row) . PHP_EOL . PHP_EOL;
    }
    echo '<pre>';

} catch (\Throwable $t) {
    echo 'Error: ' . $t->getMessage();
    echo '<br />';
}
