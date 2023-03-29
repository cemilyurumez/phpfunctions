<?php

/*
    * Bu dosya yüksek boyutlu tablolarda bir tabloya bağlı olarak farklı tabloda veri ekleme / güncelleme işlemleri yapmak için kullanılır.
    * Sayfalama yaparak kendini yeniden çağırır ve PHP timeout hatasına düşmeden veri eklemesi / güncellemesi yapar.
    * Aşağıdaki örnekte mydatatable daki verileri LIMIT belirleyerek çekip, otherdatatable tablosuna bu tabloya göre veri eklemesi yapar.
*/


ini_set('display_errors', 1);
error_reporting(E_ALL);
$config = [
    "host" => '127.0.0.1',
    "port" => 3306,
    "dbname" => "your_db_name",
    "username" => "your_db_username",
    "password" => "your_db_password",
];
try {
    $db = new PDO('mysql:host=' . $config["host"] . '; port=' . $config["port"] . '; dbname=' . $config["dbname"] . '; charset=utf8', $config["username"], $config["password"]);
} catch (PDOException $e) {
    print $e->getMessage();
}

if (!isset($_GET["page"])) $page = 1;
else $page = $_GET["page"];
if (!isset($_GET["limit"])) $limit = 1000;
else $limit = $_GET["limit"];
$start = ($page - 1) * $limit;
$total = 142638;
$totalpages = ceil($total  / $limit);


$users = $db->query("SELECT * FROM mydatatable LIMIT $start,$limit")->fetchAll(PDO::FETCH_ASSOC);
foreach ($users as $user) {
    // Bu alanda güncelleme / farklı tabloya ekleme gibi işlemleri yapılacak. Örnek kod aşağıda:
    $query = $db->prepare("INSERT INTO otherdatatable (`type`,`userid`,`value`) VALUES ('email', {$user['userid']},1");
    $insert = $query->execute();
    $last_id = $db->lastInsertId();
}

if ($page < $totalpages) {
    // Sayfalama parametreleriyle sayfayı yeniden çağırır.
    $page++;
    header("Refresh:1; url= insert-datas-paging.php?page=$page&limit=$limit");
}
