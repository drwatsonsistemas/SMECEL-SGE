<?php

//CONEXAO
try { 
    $pdo = new PDO('mysql:host=186.202.152.242;dbname=smecel1', 'smecel1', 'Drw4atson@smec'); 
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
} catch(PDOException $e) { 
    echo 'Error: ' . $e->getMessage();
}