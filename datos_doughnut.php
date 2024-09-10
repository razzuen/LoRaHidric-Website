<?php
// Conectar a la base de datos
$conn = new mysqli('localhost', 'jesus', '#^SG2Deecyianp02', 'bd_lorahidric', 3306);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener las sesiones de los tres dispositivos con más sesiones en el último mes
$sql = "SELECT id_dispositivo, COUNT(*) AS sesiones FROM sesion_riego 
        WHERE fecha_hora >= DATE_SUB(NOW(), INTERVAL 1 MONTH) 
        GROUP BY id_dispositivo 
        ORDER BY sesiones DESC 
        LIMIT 3";
$result = $conn->query($sql);

$dispositivos = [];
$sesiones = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $dispositivos[] = "Dispositivo " . $row['id_dispositivo'];
        $sesiones[] = $row['sesiones'];
    }
} else {
    $dispositivos = ["Sin datos"];
    $sesiones = [0];
}

// Cerrar la conexión
$conn->close();

// Devolver los datos como JSON
echo json_encode([
    'labels' => $dispositivos,
    'data' => $sesiones
]);
?>
