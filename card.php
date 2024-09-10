<?php
// Conexión a la base de datos
$host = "localhost";
$usuario = "jesus";
$contrasena = "#^SG2Deecyianp02";
$bd = "bd_lorahidric";
$conn = new mysqli($host, $usuario, $contrasena, $bd);

// Verifica la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Consultar el número de dispositivos únicos (diferente id_dispositivo)
$queryDispositivos = "SELECT COUNT(DISTINCT id_dispositivo) AS total_dispositivos FROM sesion_riego";
$resultDispositivos = $conn->query($queryDispositivos);

if ($resultDispositivos->num_rows > 0) {
    $row = $resultDispositivos->fetch_assoc();
    $totalDispositivos = $row['total_dispositivos'];
} else {
    $totalDispositivos = 0;
}

// Consultar el total de litros procesados en el último mes
$queryLitros = "SELECT SUM(volumen_total) AS total_litros FROM sesion_riego WHERE fecha_hora >= DATE_SUB(NOW(), INTERVAL 1 MONTH)";
$resultLitros = $conn->query($queryLitros);

if ($resultLitros->num_rows > 0) {
    $row = $resultLitros->fetch_assoc();
    $totalLitros = $row['total_litros'];
} else {
    $totalLitros = 0;
}

// Cerrar la conexión
$conn->close();

// Devolver los datos como JSON
echo json_encode([
    'totalDispositivos' => $totalDispositivos,
    'totalLitros' => $totalLitros
]);
?>
