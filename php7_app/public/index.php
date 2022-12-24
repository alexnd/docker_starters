<?php

try {
    echo 'Current PHP version: ' . phpversion();
    echo '<br />';

    $config = file_exists('../.env') ? parse_ini_file('../.env') : [];

    $host = $config['DB_HOST'] ?? 'db';
    $dbname = $config['DB_DATABASE'] ?? 'phpapp';
    $user = $config['DB_USERNAME'] ?? 'phpapp';
    $pass = $config['DB_PASSWORD'] ?? 'phpapp';
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
