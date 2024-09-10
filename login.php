<?php
	// Iniciar sesión
	session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	// Conectar a la base de datos
	$conn = new mysqli('localhost', 'jesus', '#^SG2Deecyianp02', 'bd_lorahidric', 3306);

	// Verificar la conexión
	if ($conn->connect_error) {
		die("Conexión fallida: " . $conn->connect_error);
	}

	// Obtener los datos del formulario
	$username = $_POST['username'];
	$password = $_POST['password'];


	// Preparar la consulta
	$stmt = $conn->prepare("SELECT * FROM usuario WHERE nombre_usuario = ? AND password = ?");
	$stmt->bind_param("ss", $username, $password);
	$stmt->execute();
	$result = $stmt->get_result();

	if ($result->num_rows > 0) {
		 // Obtener los datos del usuario
    	$row = $result->fetch_assoc();

		// Iniciar sesión y recordar al usuario si marcó "Recuérdame"
		$_SESSION['username'] = $username;
    	$_SESSION['es_admin'] = $row['es_admin']; // Guardamos el valor de es_admin

		if (isset($_POST['rememberMe'])) {
			setcookie('username', $username, time() + (86400 * 30), "/"); // Guardar cookie por 30 días
		}

		// Redirigir a la página de inicio o dashboard
		header("Location: dashboard.php");
	} else {
		 // Usuario o contraseña incorrectos
		echo "<div class='alert alert-danger text-center'>
				Usuario o contraseña incorrectos.
			  </div>";
	}
	
	$stmt->close();
	$conn->close();
}
?>