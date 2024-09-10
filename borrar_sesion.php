<?php
// Iniciar sesión
session_start();

// Verificar si el usuario es admin
if ($_SESSION['es_admin'] != 1) {
    echo json_encode(["success" => false, "message" => "No tienes permisos para realizar esta acción."]);
    exit();
}

// Conectar a la base de datos
$conn = new mysqli('localhost', 'jesus', '#^SG2Deecyianp02', 'bd_lorahidric', 3306);

if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Conexión fallida."]));
}

// Obtener el ID de la sesión a borrar
$sessionId = $_GET['id'];

// Eliminar la sesión de la base de datos
$sql = "DELETE FROM sesion_riego WHERE id_sesion = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $sessionId);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => "Error al borrar la sesión."]);
}

$stmt->close();
$conn->close();
?>
