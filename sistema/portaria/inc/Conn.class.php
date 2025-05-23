<?php

//CONEXAO
try { 
    $pdo = new PDO('mysql:host=localhost;dbname=smecel1', 'root', ''); 
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
} catch(PDOException $e) { 
    echo 'Error: ' . $e->getMessage();
}