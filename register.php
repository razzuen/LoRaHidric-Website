<?php
// Conexión a la base de datos
$conn = new mysqli('localhost', 'jesus', '#^SG2Deecyianp02', 'bd_lorahidric', 3306);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Validar si el formulario ha sido enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener los valores del formulario
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Verificar si las contraseñas coinciden
    if ($password !== $confirm_password) {
		alert("El nombre de usuario ya está en uso. Por favor, elige otro.");
        exit();
    }

    // Verificar si el nombre de usuario ya existe en la base de datos
    $stmt = $conn->prepare("SELECT * FROM usuario WHERE nombre_usuario = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
		alert("El nombre de usuario ya está en uso. Por favor, elige otro.");
        exit();
    }

    // Insertar el nuevo usuario en la base de datos
    $stmt = $conn->prepare("INSERT INTO usuario (nombre_usuario, password, es_admin) VALUES (?, ?, 0)");
    $stmt->bind_param("ss", $username, $password);

    if ($stmt->execute()) {
        // Redirigir al login o mostrar mensaje
		header("Location: index.html");
    } else {
		 alert("Error al registrar el usuario."); 
    }

    // Cerrar la conexión
    $stmt->close();
    $conn->close();
}
?>
