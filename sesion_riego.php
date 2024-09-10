
<?php
session_start(); // Inicia la sesión

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['username'])) {
    header("Location: login.php"); // Redirigir si no hay sesión activa
    exit();
}

// Acceso a la variable es_admin si el usuario tiene sesión
$is_admin = $_SESSION['es_admin'];
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>IoT Services - Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-size: cover;
            background-position: center;
            margin: 0;
            padding: 0;
            height: 100vh;
            overflow-x: hidden; 
        }

        .sidebar {
            width: 200px;
            background-color: #8da1b9;
            padding: 20px;
            color: white;
            position: fixed;
            top: 0;
            height: 100%;
            transition: left 0.3s ease;
        }

        .sidebar .app-title {
            text-align: center;
            color: #1A1A1A;
            font-size: 12px;
            margin-top: 75px;
            margin-bottom: 50px; /* Añade un espacio extra entre el título y la lista */
        }

        .sidebar .menu-list {
            list-style: none;
            padding: 0;
        }

        .sidebar .menu-list li {
            margin: 15px 0;
        }

        .sidebar .menu-list li a {
            color: white;
            text-decoration: none;
            font-size: 16px;
            display: block;
            padding: 10px;
            transition: background-color 0.3s;
        }

        .sidebar .menu-list li a:hover {
            background-color: #1A1A1A; /* Cambia el color de fondo al hacer hover */
            border-radius: 5px;
        }

        .sidebar .app-icon {
            position: absolute; /* Ancla la imagen al fondo de la barra lateral */
            bottom: 25px; /* Ajusta la distancia desde el fondo */
            left: 50%; /* Centra la imagen horizontalmente */
            transform: translateX(-50%); /* Centra el ícono correctamente */
            text-align: center;
        }

        .sidebar .app-icon img {
            width: 100px; /* Ajusta el tamaño del icono según sea necesario */
            height: auto;
        }

        .sidebar.hidden-sidebar {
            left: -250px;
        }

        .toggle-btn {
            position: fixed;
            left: 20px;
            top: 20px;
            font-size: 24px;
            cursor: pointer;
            color: #30667A;
            background-color: #f8f9fa;
            padding: 5px 10px;
            border-radius: 5px;
            z-index: 1000;
            box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
        }

        .content {
            margin-left: 0px;
            padding: 20px;
            background-color: #ffffff;
            transition: margin-left 0.3s ease;
        }

        .hidden-sidebar + .content {
            margin-left: 70px; /* Cuando la sidebar está oculta */
        }
		
		#sessionTitle {
			text-align: center;
			font-size: 50px;
			margin-top:50px;
		}
		
		.btn-danger {
			background-color: #30667A !important; 
			border-color: #30667A !important;
			color: white !important;
		}

		.btn-warning {
			background-color: #30667A !important; 
			border-color: #30667A !important;
			color: white !important;
		}

		.card-container {
			display: grid;
			grid-template-columns: repeat(3, 1fr);
			gap: 20px;
			padding: 100px 80px; /* Reduce el padding para aprovechar más el espacio */
			margin: 0 auto; /* Para centrar el contenido */
		}

		.card {
			padding: 50px;
			border-radius: 10px;
			text-align: center;
			width: auto; /* Permitir que la tarjeta se ajuste al contenido */
			height: auto; /* Permitir que la tarjeta se ajuste al contenido */
		}

		.action-buttons {
			display: flex;
			justify-content: center; /* Para centrar horizontalmente */
			gap: 100px; /* Ajusta la distancia entre los botones */
		}

		.btn {
			padding: 10px 20px;
			font-size: 16px;
			background-color: #30667A;
			color: white; /* Si quieres que el texto sea blanco */
		}
		
		
		.card h4 {
			font-size: 30px; /* Tamaño de fuente para los títulos dentro de las tarjetas */
		}

		.card p {
			font-size:30px; /* Tamaño de fuente para el contenido de las tarjetas */
		}
		
		.watermark {
			margin-right: 100px; /* Ajusta el valor para mover la marca de agua */
			font-size: 25px;
		}
		
		.dialog-container {
			position: fixed;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			background: rgba(0, 0, 0, 0.5);
			display: flex;
			justify-content: center;
			align-items: center;
		}

		.dialog-box {
			background: white;
			padding: 20px;
			border-radius: 10px;
			text-align: center;
		}

		.dialog-box button {
			margin: 10px;
		}
		
		.hidden {
			display: none;
		}

    </style>
</head>
<body>
    <div class="sidebar hidden-sidebar" id="sidebar">
        <div class="app-title">
            <h3>MoonLight<br>IoT Services</h3>
        </div>
        <ul class="menu-list">
            <li><a href="#">Inicio</a></li>
				<?php if ($is_admin == 1): // Mostrar solo si el usuario es admin ?>
					<li><a href="usuarios.php">Usuarios</a></li> <!-- Redireccionar a usuarios.php -->
				<?php endif; ?>
        	<li><a href="javascript:void(0);" onclick="showLogoutDialog()">Cerrar Sesión</a></li>
        </ul>
        <div class="app-icon">
            <!-- Aquí colocas la imagen del icono -->
            <img src="img/logo.png" alt="App Icon">
        </div>
    </div>

    <div class="toggle-btn" onclick="toggleSidebar()">&#9776;</div>

    <div class="content" id="content">
	   <h3 id="sessionTitle">Datos recogidos para la sesión <span id="sessionId"></span></h3>
       <div class="card-container">
			<div class="card" style="background-color: #1A1A1A; color: white;">
				<h4>ID Dispositivo</h4>
				<p id="id_dispositivo"></p>
			</div>

			<div class="card" style="background-color: #30667A; color: white;">
				<h4>Modelo de Sensor</h4>
				<p id="modelo_sensor"></p>
			</div>

			<div class="card" style="background-color: #8DA1B9; color: white;">
				<h4>Duración del riego (s)</h4>
				<p id="duracion_riego"></p>
			</div>

			<div class="card" style="background-color:#8DA1B9; color: white;">
				<h4>Flujo máximo (L/min)</h4>
				<p id="flujo_maximo"></p>
			</div>

			<div class="card" style="background-color: #30667A; color: white;">
				<h4>Flujo Medio (L/min)</h4>
				<p id="flujo_medio"></p>
			</div>

			<div class="card" style="background-color: #1A1A1A; color: white;">
				<h4>Volumen total (L)</h4>
				<p id="volumen_total"></p>
			</div>
		</div>
		
		<div class="action-buttons">
			<button id="deleteButton" class="btn btn-danger <?php echo ($_SESSION['es_admin'] != 1) ? 'hidden' : ''; ?>">
				Eliminar Registro</button>
			<button id="editButton" class="btn btn-warning <?php echo ($_SESSION['es_admin'] != 1) ? 'hidden' : ''; ?>">Editar 								Registro</button>
		</div>
		
		<div id="adminBar" class="admin-bar" style="display: none;">
			<button id="deleteRecord" class="btn btn-danger">Borrar Registro</button>
			<button id="editRecord" class="btn btn-warning">Editar Registro</button>
		</div>
		
		<div class="watermark" style="position: absolute; bottom: 10px; right: 10px;">
			<p>Datos tomados: <span id="fecha_hora"></span></p>
		</div>
		
		
		<!-- Cuadro de diálogo flotante -->
		<div id="deleteConfirmation" class="dialog-container" style="display: none;">
			<div class="dialog-box">
				<p>¿Seguro que deseas eliminar esta sesión?</p>
				<button id="confirmDeleteBtn" class="btn btn-danger">Sí</button>
				<button id="cancelDeleteBtn" class="btn btn-secondary">No</button>
			</div>
		</div>
		
		<!-- Cuadro de diálogo flotante para cerrar sesión -->
		<div id="logoutDialog" class="dialog-container" style="display: none;">
			<div class="dialog-box">
				<p>¿Seguro que deseas cerrar la sesión actual?</p>
				<button id="confirmLogoutBtn" class="btn btn-danger">Sí</button>
				<button id="cancelLogoutBtn" class="btn btn-secondary">No</button>
			</div>
			
			
	</div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
    <script>
       
        function toggleSidebar() {
            const sidebar = document.getElementById("sidebar");
            const content = document.getElementById("content");
            
            if (sidebar.classList.contains("hidden-sidebar")) {
                sidebar.classList.remove("hidden-sidebar");
                content.style.marginLeft = "200px"; // Ajuste cuando la barra lateral esté visible
            } else {
                sidebar.classList.add("hidden-sidebar");
                content.style.marginLeft = "0"; // Ajuste cuando la barra lateral esté oculta
            }
        }
		
		document.addEventListener('DOMContentLoaded', function() {
			// Obtener el ID de la sesión desde la URL
			const urlParams = new URLSearchParams(window.location.search);
			const sessionId = urlParams.get('id'); // El ID de la sesión está en la URL
			
			document.getElementById('deleteButton').onclick = function() {
            	showDeleteDialog(sessionId);
        	};

			// Hacer una solicitud para obtener los datos de la sesión
			fetch(`datos_sesion.php?id=${sessionId}`)
				.then(response => response.json())
				.then(data => {
					if (data.error) {
						console.error('Error al obtener datos:', data.error);
					} else {
						// Llenar las tarjetas con los datos obtenidos
						document.getElementById('id_dispositivo').textContent = data.id_dispositivo;
						document.getElementById('modelo_sensor').textContent = data.sensor_modelo;
						document.getElementById('duracion_riego').textContent = data.duracion_riego + ' s';
						document.getElementById('flujo_maximo').textContent = data.flujo_maximo + ' L/min';
						document.getElementById('flujo_medio').textContent = data.flujo_medio + ' L/min';
						document.getElementById('volumen_total').textContent = data.volumen_total + ' L';
						document.getElementById('fecha_hora').textContent = data.fecha_hora;
					}
				})
				.catch(error => console.error('Error:', error));
		});
       
		// Función para mostrar el cuadro de diálogo flotante
		function showDeleteDialog(sessionId) {
			sessionToDelete = parseInt(sessionId);  // Guardar el ID de la sesión a borrar
			console.log("ID de la sesión a borrar (en showDeleteDialog):", sessionToDelete); // Para verificar
			document.getElementById('deleteConfirmation').style.display = 'flex'; // Mostrar el cuadro de diálogo

		}
		
		// Función para ocultar el cuadro de diálogo
		function hideDeleteDialog() {
			document.getElementById('deleteConfirmation').style.display = 'none';
		}
		
		// Función para confirmar la eliminación de la sesión
		document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
			if (sessionToDelete !== null) {
				
				console.log("ID de la sesión a borrar:", sessionToDelete); // Añadido aquí

				// Hacer la petición para borrar la sesión
				fetch(`borrar_sesion.php?id=${sessionToDelete}`, {
					method: 'DELETE',
				})
			
				.then(response => response.json())
				.then(data => {
					if (data.success) {
						alert("Sesión borrada con éxito.");
						window.location.href = 'dashboard.php';
					} else {
						alert("Error al borrar la sesión.");
					}
					hideDeleteDialog(); // Ocultar el cuadro de diálogo
				})
				.catch(error => {
					console.error("Error:", error);
					hideDeleteDialog(); // Ocultar el cuadro de diálogo
				});
			}
		});

		// Botón para cancelar la eliminación
		document.getElementById('cancelDeleteBtn').addEventListener('click', function() {
			hideDeleteDialog();
		});
		
		// Función para mostrar el cuadro de diálogo de cerrar sesión
		function showLogoutDialog() {
			document.getElementById('logoutDialog').style.display = 'flex'; // Mostrar el cuadro de diálogo
		}

		// Función para ocultar el cuadro de diálogo de cerrar sesión
		function hideLogoutDialog() {
			document.getElementById('logoutDialog').style.display = 'none'; // Ocultar el cuadro de diálogo
		}

		// Botón para confirmar el cierre de sesión
		document.getElementById('confirmLogoutBtn').addEventListener('click', function() {
			window.location.href = 'logout.php'; // Redirigir a logout.php para cerrar la sesión
		});

		// Botón para cancelar el cierre de sesión
		document.getElementById('cancelLogoutBtn').addEventListener('click', function() {
			hideLogoutDialog(); // Ocultar el cuadro de diálogo sin cerrar la sesión
		});

    </script>
</body>
</html>
