<?php
// Conexión a la base de datos
$conn = new mysqli('localhost', 'jesus', '#^SG2Deecyianp02', 'bd_lorahidric', 3306);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Recibir los datos de TTN (esperamos un payload en JSON)
$data = file_get_contents('php://input');
$json = json_decode($data, true);

// Registrar el payload recibido para debug
$log_file = 'errores.log';
file_put_contents($log_file, date('Y-m-d H:i:s') . " - Payload received: " . print_r($json, true) . "\n", FILE_APPEND);

// Extraer los datos del payload
if (isset($json['uplink_message']['decoded_payload'])) {
    $decoded_payload = $json['uplink_message']['decoded_payload'];

    // Extraer los datos del payload decodificado
    $sensor_model = $decoded_payload['sensorID'] ?? '';
    $duracion_riego = $decoded_payload['duracionRiego'] ?? 0;
    $flujo_maximo = $decoded_payload['flujoMaximo'] ?? 0;
    $flujo_medio = $decoded_payload['flujoMedio'] ?? 0;
    $litros_totales = $decoded_payload['litros'] ?? 0;
    $device_id = $decoded_payload['deviceID'] ?? 0;

    // Obtener el timestamp actual
    $fecha_hora = date('Y-m-d H:i:s');

    // Verificar si los valores son correctos y no están vacíos
    if (!empty($sensor_model) && $duracion_riego > 0) {
        // Inserción en la base de datos
        $sql = "INSERT INTO sesion_riego (sensor_modelo, duracion_riego, flujo_maximo, flujo_medio, volumen_total, id_dispositivo, fecha_hora) 
                VALUES ('$sensor_model', $duracion_riego, $flujo_maximo, $flujo_medio, $litros_totales, $device_id, '$fecha_hora')";

        if ($conn->query($sql) === TRUE) {
            file_put_contents($log_file, date('Y-m-d H:i:s') . " - Datos insertados correctamente con timestamp\n", FILE_APPEND);
        } else {
            // Registrar el error de SQL en el log
            file_put_contents($log_file, date('Y-m-d H:i:s') . " - SQL Error: " . $conn->error . "\n", FILE_APPEND);
        }
    } else {
        // Registrar si los valores recibidos no son válidos
        file_put_contents($log_file, date('Y-m-d H:i:s') . " - Error: Datos recibidos vacíos o no válidos\n", FILE_APPEND);
    }
} else {
    // Registrar si no se encuentra la estructura correcta en el JSON
    file_put_contents($log_file, date('Y-m-d H:i:s') . " - Error: No se encontró decoded_payload en el JSON recibido\n", FILE_APPEND);
}

// Cerrar conexión
$conn->close();
?>
