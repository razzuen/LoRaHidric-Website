<?php
header('Content-Type: application/json');

// Conexión a la base de datos
$conn = new mysqli('localhost', 'jesus', '#^SG2Deecyianp02', 'bd_lorahidric');

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Consulta para obtener el número de sesiones por día de la última semana
$sql = "SELECT DAYNAME(fecha_hora) as dia, COUNT(*) as total
        FROM sesion_riego
        WHERE fecha_hora >= DATE_SUB(NOW(), INTERVAL 1 WEEK)
        GROUP BY dia
        ORDER BY FIELD(dia, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')";

$result = $conn->query($sql);

$labels = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
$data = [0, 0, 0, 0, 0, 0, 0]; // Inicializar datos en 0 para cada día

if ($result->num_rows > 0) {
    // Rellenar los datos con los resultados de la consulta
    while ($row = $result->fetch_assoc()) {
        $dayIndex = array_search($row['dia'], $labels); // Encontrar el índice del día
        if ($dayIndex !== false) {
            $data[$dayIndex] = (int)$row['total']; // Asignar la cantidad de sesiones
        }
    }
}

$conn->close();

// Devolver datos en formato JSON
echo json_encode([
    'labels' => ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'], // Traducir días al español
    'data' => $data
]);
?>
