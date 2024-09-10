<?php
// Iniciar sesión (si es necesario)
session_start();

// Verificar si se ha pasado el ID de la sesión en la URL
if (!isset($_GET['id'])) {
    die("Error: No se ha proporcionado ningún ID de sesión.");
}

// Conectar a la base de datos
$conn = new mysqli('localhost', 'jesus', '#^SG2Deecyianp02', 'bd_lorahidric', 3306);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Obtener el ID de la sesión desde la URL
$sessionId = $_GET['id'];

// Consultar la base de datos para obtener los detalles de la sesión
$sql = "SELECT id_sesion, duracion_riego, volumen_total, id_dispositivo, fecha_hora, sensor_modelo, flujo_maximo, flujo_medio FROM sesion_riego WHERE id_sesion = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $sessionId);
$stmt->execute();
$result = $stmt->get_result();

// Verificar si se encontró la sesión
if ($result->num_rows > 0) {
    // Obtener los datos de la sesión
    $row = $result->fetch_assoc();
    
    // Mostrar los datos de la sesión (puedes modificar este HTML según el formato que desees)
   // Mostrar los datos de la sesión
   // Devolver los datos como JSON
    echo json_encode([
        "id_sesion" => $row['id_sesion'],
        "duracion_riego" => $row['duracion_riego'],
        "volumen_total" => $row['volumen_total'],
        "id_dispositivo" => $row['id_dispositivo'],
        "fecha_hora" => date("d/m/Y H:i", strtotime($row['fecha_hora'])),
        "sensor_modelo" => $row['sensor_modelo'],
        "flujo_maximo" => $row['flujo_maximo'],
        "flujo_medio" => $row['flujo_medio']
    ]);
	
} else {
    echo "Error: No se encontró ninguna sesión con el ID proporcionado.";
}

$stmt->close();
$conn->close();
?>
