<?php
// config.php — ajuste suas credenciais
$DB_HOST = '127.0.0.1';
$DB_NAME = 'VolleyConnect';
$DB_USER = 'seu_usuario';
$DB_PASS = 'sua_senha';
$DSN = "mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4";
$options = [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC];
try { $pdo = new PDO($DSN, $DB_USER, $DB_PASS, $options); } 
catch (PDOException $e) { die("Erro DB: ".$e->getMessage()); }
session_start();
