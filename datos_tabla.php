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
} else {
    //echo "Conexión exitosa<br>";
}

// Consultar la tabla sesion_riego
$sql = "SELECT id_sesion, duracion_riego, volumen_total, id_dispositivo, fecha_hora FROM sesion_riego";
$result = $conn->query($sql);

// Arreglo para almacenar los datos
$data = [];

if ($result->num_rows > 0) {
    // Procesar los datos y almacenarlos en el arreglo
    while($row = $result->fetch_assoc()) {
        $data[] = [
			'id' => $row['id_sesion'],
            'id_dispositivo' => $row["id_dispositivo"],
            'duracion' => gmdate("H:i:s", $row["duracion_riego"]),
            'litros' => $row["volumen_total"],
            'fecha' => date("d/m/Y H:i", strtotime($row["fecha_hora"]))
        ];
    }
    //echo "Datos obtenidos correctamente<br>";
} else {
   // echo "No hay datos<br>";
}

$conn->close();

// Devolver los datos en formato JSON
echo json_encode($data);
?>
