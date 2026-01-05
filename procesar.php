<?php
require_once 'conexion.php';

header('Content-Type: application/json');


if (isset($_GET['get_total'])) {
    $tabla = preg_replace('/[^a-zA-Z0-9_]/', '', $_GET['get_total']); 
    $res = $conn->query("SELECT COUNT(*) as total FROM $tabla");
    if($res) {
        $row = $res->fetch_assoc();
        echo json_encode(['total' => (int)$row['total']]);
    } else {
        echo json_encode(['total' => 0]);
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tabla = $_POST['tabla'] ?? '';
    $filtro = $_POST['filtro'] ?? '';
    $cantidadSolicitada = (int)($_POST['cantidad'] ?? 0);

    $url = "https://restcountries.com/v3.1/" . str_replace(' ', '%20', $filtro);
    $json = @file_get_contents($url);

    if (!$json) {
        echo json_encode(['success' => false, 'message' => 'Error: API inaccesible']);
        exit;
    }

    $paisesApi = json_decode($json, true);
    
    $yaExistentes = [];
    $resExistentes = $conn->query("SELECT nombre FROM $tabla");
    if($resExistentes) {
        while($row = $resExistentes->fetch_assoc()){
            $yaExistentes[] = $row['nombre'];
        }
    }

    $insertados = 0;

    foreach ($paisesApi as $p) {
        if ($insertados >= $cantidadSolicitada) break;

        $nombreOriginal = $p['name']['common'] ?? 'N/A';
        
        if (in_array($nombreOriginal, $yaExistentes)) {
            continue;
        }

        $nombre = $conn->real_escape_string($nombreOriginal);
        $capital = $conn->real_escape_string($p['capital'][0] ?? 'N/A');
        $poblacion = (int)($p['population'] ?? 0);
        $bandera = $conn->real_escape_string($p['flags']['png'] ?? '');
        $mapa = $conn->real_escape_string($p['maps']['googleMaps'] ?? '');

        $sql = "INSERT INTO $tabla (nombre, capital, poblacion, bandera, mapa) 
                VALUES ('$nombre', '$capital', $poblacion, '$bandera', '$mapa')";
        
        if ($conn->query($sql)) {
            $insertados++;
        }
    }

    if ($insertados > 0) {
        echo json_encode([
            'success' => true, 
            'message' => "✔ Inyectados: $insertados. Reiniciando..."
        ]);
    } else {
        echo json_encode([
            'success' => false, 
            'message' => "⚠ No hay registros nuevos disponibles"
        ]);
    }
}
?>