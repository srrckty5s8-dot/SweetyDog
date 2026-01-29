<?php
$host = 'localhost';
$db   = 'mon_salon';
$user = 'root';
$pass = 'root'; 

try {
    $bdd = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
// NE PAS METTRE DE BALISE DE FERMETURE ICI