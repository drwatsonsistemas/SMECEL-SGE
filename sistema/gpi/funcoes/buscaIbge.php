<?php
$pdo = new PDO("mysql:host=186.202.152.242; dbname=smecel1; charset=utf8;", "smecel1", "r@f@el");
//$pdo = new PDO("mysql:host=localhost; dbname=smecel1; charset=utf8;", "root", "");
$dados = $pdo->prepare("SELECT municipio_nome FROM smc_municipio");
$dados->execute();
echo json_encode($dados->fetchAll(PDO::FETCH_ASSOC));
?>