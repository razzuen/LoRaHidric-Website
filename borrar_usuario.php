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

// Obtener el ID del usuario a borrar
$userId = $_GET['id'];

// Eliminar el usuario de la base de datos
$sql = "DELETE FROM usuario WHERE id_usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => "Error al borrar el usuario."]);
}

$stmt->close();
$conn->close();
?>
