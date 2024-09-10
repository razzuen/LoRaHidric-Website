<?php
// Establecer la conexión con la base de datos
$servername = "localhost"; 
$username = "jesus"; 
$password = "#^SG2Deecyianp02"; 
$dbname = "bd_lorahidric"; 

// Crear la conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar si la conexión es exitosa
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Consultar la tabla usuario
$sql = "SELECT id_usuario, nombre_usuario, es_admin FROM usuario";
$result = $conn->query($sql);

// Arreglo para almacenar los datos
$data = [];

if ($result->num_rows > 0) {
    // Procesar los datos y almacenarlos en el arreglo
    while($row = $result->fetch_assoc()) {
        $data[] = [
            'id' => $row['id_usuario'],
            'nombre_usuario' => $row['nombre_usuario'],
            'es_admin' => ($row['es_admin'] == 1) ? 'Sí' : 'No' // Convertimos 1 en 'Sí' y 0 en 'No'
        ];
    }
}

$conn->close();

// Devolver los datos en formato JSON
echo json_encode($data);
?>
