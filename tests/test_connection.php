<?php

require_once __DIR__ . '/../config/database.php';

$database = new CConexion();
$conn = $database->conexionBD();

if ($conn !== null) {
    echo 'Conexión exitosa a PostgreSQL';
}

?>