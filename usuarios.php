<?php
session_start(); // Inicia la sesión

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['username'])) {
    header("Location: login.php"); // Redirigir si no hay sesión activa
    exit();
}

// Acceso a la variable es_admin si el usuario tiene sesión

$username = $_SESSION['username'];
$is_admin = $_SESSION['es_admin'];
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>IoT Services - Lista de Usuarios</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-image: url('back.jpg'); /* Asegúrate de cambiar la ruta */
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
            <li><a href="dashboard.php">Inicio</a></li>
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

        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID Usuario</th>
                        <th>Nombre de Usuario</th>
                        <th>Permisos</th>
                        <th>Acciones sobre los datos</th>
                    </tr>
                </thead>
                <tbody id="userTableBody">
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
       
        let users = [];
        let userToDelete = null; // Variable para guardar el ID de la sesión que se quiere borrar
        let currentPage = 1;
        const rowsPerPage = 20;

        // Hacer una petición a PHP para obtener los datos
        fetch('datos_usuarios.php')
            .then(response => response.json())
            .then(data => {
				console.log(data);
                users = data;
                displayUsers(currentPage); // Mostrar la primera página de datos
            })
            .catch(error => console.error('Error al obtener los datos:', error));

        function displayUsers(page) {
			const start = (page - 1) * rowsPerPage;
			const end = start + rowsPerPage;
			const tableBody = document.getElementById('userTableBody'); // Cambiamos el id para la tabla de usuarios
			tableBody.innerHTML = '';

			const currentUsers = users.slice(start, end); 
			currentUsers.forEach(user => {
				let options = ''; // Empezamos sin opciones

				// Si el usuario es admin, agregar la opción "Borrar"
				<?php if ($is_admin == 1): ?>
					options = `<span style="color:blue;cursor:pointer;" 				      									onclick="showDeleteDialog(${user.id})">Borrar</span>`;
				<?php endif; ?>

				const row = `
					<tr>
						<td>${user.id}</td>   <!-- ID de usuario -->
						<td>${user.nombre_usuario}</td>  <!-- Nombre del usuario -->
						<td>${user.es_admin}</td>  <!-- Es admin (Sí o No) -->
						<td>${options}</td>  <!-- Opciones para borrar (si es admin) -->
					</tr>
				`;
				tableBody.innerHTML += row;
			});
		}

        document.getElementById('prevPage').addEventListener('click', () => {
            if (currentPage > 1) {
                currentPage--;
                displayUsers(currentPage);
            }
        });

        document.getElementById('nextPage').addEventListener('click', () => {
            if (currentPage < Math.ceil(users.length / rowsPerPage)) {
                currentPage++;
                displayUsers(currentPage);
            }
        });
        
        // Función para mostrar el cuadro de diálogo flotante
        function showDeleteDialog(userId) {
            userToDelete = parseInt(userId);  // Guardar el ID de la sesión a borrar
            console.log("ID del usuario a borrar (en showDeleteDialog):", userToDelete); // Para verificar
            document.getElementById('deleteConfirmation').style.display = 'flex'; // Mostrar el cuadro de diálogo

        }
        
        // Función para ocultar el cuadro de diálogo
        function hideDeleteDialog() {
            document.getElementById('deleteConfirmation').style.display = 'none';
        }
        
        // Función para confirmar la eliminación de la sesión
        document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
            if (userToDelete !== null) {
				console.log("drefjrefuirehfuerfherufh", $username);
                console.log("ID del usuario a borrar:", userToDelete); // Añadido aquí

                // Hacer la petición para borrar la sesión
                fetch(`borrar_usuario.php?id=${userToDelete}`, {
                    method: 'DELETE',
                })
                
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert("Usuario borrada con éxito.");
                        // Actualizar la tabla sin recargar la página
                        users = users.filter(user => user.id !== parseInt(userToDelete));
                        window.location.reload();  // Esta línea recarga la página
                        displayUsers(currentPage);

                    } else {
                        alert("Error al borrar el usuario.");
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
