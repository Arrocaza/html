<?php
$serverName = "abrahamio.database.windows.net";
$connectionOptions = array(
    "Database" => "Abraham",
    "Uid" => "Abraham",
    "PWD" => "Alonso05"
);

// Establecer la conexión
$conn = sqlsrv_connect($serverName, $connectionOptions);

// Verificar la conexión
if ($conn === false) {
    die(print_r(sqlsrv_errors(), true));
}
?>