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
            background-image: url('/img/textura_fondo.jpg'); /* Asegúrate de cambiar la ruta */
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

        .card-container {
            display: grid;
            grid-template-columns: 0.6fr 1.2fr 1.2fr;
            gap: 20px;
        }

        .custom-card {
            background-color: #30667A;
            color: white;
            border-radius: 15px;
            padding: 20px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 150px;
            text-align: center;
            white-space: nowrap;
            align-self: end;
            margin-bottom: 25px;
        }

        .custom-card h5 {
            font-size: 16px;
            margin-bottom: 5px;
        }

        .custom-card p {
            font-size: 24px;
            margin: 0;
        }

		.table-container {
			margin-top: 20px;
		}

		.table-container table {
			width: 100%;
			font-size: 20px; /* Reducción de dos puntos en el tamaño de fuente */
		}

		.table-container table th,
		.table-container table td {
			text-align: center; /* Centra horizontalmente los datos y encabezados */
		}

        /* Ajuste de la tarjeta 2 */
        .chart-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 20px;
        }

        .chart-container canvas {
            max-height: 150px;
            max-width: 150px;
        }

        .chart-legend {
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding-left: 10px; /* Ajuste para acercar leyenda al gráfico */
        }

        .legend-item {
            display: flex;
            align-items: center;
            margin-bottom: 5px;
        }

        .legend-color-box {
            width: 15px;
            height: 15px;
            margin-right: 10px;
        }

        /* Tarjeta 3: Ajuste de posición del gráfico de barras */
        .chart-bar-container {
            display: flex;
            justify-content: flex-start;
            align-items: flex-start;
            padding-top: 20px;
            padding-left: 20px;
        }

        .chart-bar-container canvas {
            max-height: 150px;
            max-width: 100%;
        }

        .card-title {
            text-align: center;
            margin-bottom: 10px;
        }

        /* Elimina los bordes de las tarjetas */
        .card {
            border: none;
            box-shadow: none;
        }

        .pagination {
            display: flex;
            justify-content: flex-end;
            margin-top: 10px;
        }

        .pagination button {
            background-color: #30667a; /* Color de fondo del botón */
            color: white; /* Color del texto */
            border: none;
            padding: 8px 16px; /* Tamaño del botón */
            border-radius: 5px; /* Bordes redondeados */
            cursor: pointer;
            font-family: 'Poppins', sans-serif; /* Asegúrate de usar la fuente correcta */
            margin-right: 5px; /* Espacio entre los botones */
            transition: background-color 0.3s ease;
        }

        .pagination button:hover {
            background-color: #1A1A1A; /* Color al pasar el mouse */
        }

        .pagination button:disabled {
            background-color: #cccccc; /* Color cuando el botón está deshabilitado */
            cursor: not-allowed;
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
        <div class="card-container">
            <!-- Tarjeta 1 sin cambios -->
            <div class="custom-card">
                <h5>Dispositivos Registrados</h5>
                <p id="totalDispositivos">Cargando...</p> <!-- Añadimos un id para actualizar el número de dispositivos -->
				<h5>Total litros procesados última semana</h5>
				<p id="totalLitros">Cargando...</p>
            </div>

            <!-- Tarjeta 2 con Doughnut Chart -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Sesiones último mes</h5> <!-- Título correcto -->
                    <div class="chart-container">
                        <canvas id="doughnutChart"></canvas>
                        <div class="chart-legend" id="chartLegend">
                            <div class="legend-item">
                                <div class="legend-color-box" style="background-color: #1A1A1A;"></div>
                                <span>Dispositivo 1</span>
                            </div>
                            <div class="legend-item">
                                <div class="legend-color-box" style="background-color: #30667a;"></div>
                                <span>Dispositivo 2</span>
                            </div>
                            <div class="legend-item">
                                <div class="legend-color-box" style="background-color: #8da1b9;"></div>
                                <span>Dispositivo 3</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tarjeta 3 con gráfico de barras ajustado -->
			<div class="card">
				<div class="card-body">
					<h5 class="card-title">Sesiones registradas última semana</h5>
					<div class="chart-bar-container">
						<canvas id="barChart"></canvas>
					</div>
				</div>
			</div>
        </div>

        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID Dispositivo</th>
                        <th>Duración</th>
                        <th>Litros</th>
                        <th>Fecha</th>
                        <th>Acciones sobre los datos</th>
                    </tr>
                </thead>
                <tbody id="sessionTableBody">
                    <!-- Las filas se llenarán dinámicamente -->
                </tbody>
            </table>

            <!-- Botones de paginación -->
            <div class="pagination" style="display: flex; justify-content: flex-end;">
                <button class="page-link" id="prevPage" style="margin-right: 5px;">Anterior</button>
                <button class="page-link" id="nextPage">Siguiente</button>
            </div>
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
		// Hacer una solicitud al archivo doughnut.php para obtener los datos
		fetch('datos_doughnut.php')
			.then(response => response.json())
			.then(data => {
				// Actualizar el gráfico con los datos recibidos
				const ctxDoughnut = document.getElementById('doughnutChart').getContext('2d');
				const doughnutChart = new Chart(ctxDoughnut, {
					type: 'doughnut',
					data: {
						labels: data.labels, // Usar los nombres de los dispositivos
						datasets: [{
							data: data.data, // Usar la cantidad de sesiones por dispositivo
							backgroundColor: ['#1A1A1A', '#30667a', '#8da1b9'],
							borderWidth: 1
						}]
					},
					options: {
						responsive: true,
						maintainAspectRatio: false,
						plugins: {
							legend: {
								display: false
							}
						}
					}
				});
			
				// Llenar dinámicamente la leyenda en el HTML
				const legendContainer = document.getElementById('chartLegend');
				legendContainer.innerHTML = '';  // Limpiar cualquier contenido anterior
				data.labels.forEach((label, index) => {
					const legendItem = `
						<div class="legend-item">
							<div class="legend-color-box" style="background-color: ${doughnutChart.data.datasets[0].backgroundColor[index]};">                             </div>
							<span>${label}</span>
						</div>
					`;
					legendContainer.innerHTML += legendItem;
				});
			})
			.catch(error => console.error('Error al obtener los datos:', error));

        fetch('datos_bar.php')
			.then(response => response.json())
			.then(data => {
				// Crear la gráfica de barras
				const ctxBar = document.getElementById('barChart').getContext('2d');
				const barChart = new Chart(ctxBar, {
					type: 'bar',
					data: {
						labels: data.labels, // Los días de la semana
						datasets: [{
							label: 'Sesiones',
							data: data.data, // Cantidad de sesiones por día
							backgroundColor: ['#1A1A1A', '#30667a', '#8da1b9', '#1A1A1A', '#30667a', '#8da1b9', '#1A1A1A'],
							borderWidth: 1
						}]
					},
					options: {
						responsive: true,
						maintainAspectRatio: false,
						scales: {
							x: {
								beginAtZero: true
							},
							y: {
								beginAtZero: true,
								max: Math.max(...data.data) + 2 // Asegurar que el eje y se ajusta
							}
						},
						plugins: {
							legend: {
								display: false
							}
						}
					}
				});
			})
			.catch(error => console.error('Error al obtener los datos:', error));
       
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
		
		// Hacer una solicitud al archivo card.php para obtener los datos
		fetch('card.php') // La ruta 'card.php' debe ser correcta para tu estructura de archivos
			.then(response => response.json())
			.then(data => {
				// Actualizar el contenido de la card con los valores recibidos
				document.getElementById('totalDispositivos').textContent = data.totalDispositivos;
				document.getElementById('totalLitros').textContent = parseFloat(data.totalLitros).toFixed(2) + ' litros';
			})
        .catch(error => console.error('Error al obtener los datos:', error));

		
		let sessions = [];
		let sessionToDelete = null; // Variable para guardar el ID de la sesión que se quiere borrar
		let currentPage = 1;
		const rowsPerPage = 20;

		// Hacer una petición a PHP para obtener los datos
		fetch('datos_tabla.php')
			.then(response => response.json())
			.then(data => {
				sessions = data;
				displaySessions(currentPage); // Mostrar la primera página de datos
			})
			.catch(error => console.error('Error al obtener los datos:', error));

		function displaySessions(page) {
			const start = (page - 1) * rowsPerPage;
			const end = start + rowsPerPage;
			const tableBody = document.getElementById('sessionTableBody');
			tableBody.innerHTML = '';

			const currentSessions = sessions.slice(start, end);
			currentSessions.forEach(session => {
				 let options = `<span style="color:blue;cursor:pointer;" onclick="window.location.href='sesion_riego.php?													id=${session.id}'">Visualizar</span>`;
        
        // Si el usuario es admin, agregar la opción "Borrar"
        <?php if ($is_admin == 1): ?>
            options += ` | <span style="color:blue;cursor:pointer;" onclick="showDeleteDialog(${session.id})">Borrar</span>`;
        <?php endif; ?>
				
				const row = `
					<tr>
						<td>${session.id_dispositivo}</td>
						<td>${session.duracion}</td>
						<td>${session.litros} litros</td>
						<td>${session.fecha}</td>
						<td>${options}</td>
						<td><i class="bi bi-info-circle"></i></td>
					</tr>
				`;
				tableBody.innerHTML += row;
		});
		}

		document.getElementById('prevPage').addEventListener('click', () => {
			if (currentPage > 1) {
				currentPage--;
				displaySessions(currentPage);
			}
		});

		document.getElementById('nextPage').addEventListener('click', () => {
			if (currentPage < Math.ceil(sessions.length / rowsPerPage)) {
				currentPage++;
				displaySessions(currentPage);
			}
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
						// Actualizar la tabla sin recargar la página
				
						sessions = sessions.filter(session => session.id !== parseInt(sessionToDelete));
						window.location.reload();  // Esta línea recarga la página
						displaySessions(currentPage);
						
						
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
