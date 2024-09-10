<?php
	session_start();

	// Destruir todas las sesiones
	session_unset();
	session_destroy();

	// Redirigir al login
	header("Location: index.html");
	exit();
?>