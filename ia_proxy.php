<?php
// ia_proxy.php - V9: Prompt Refinado (Sin Títulos) + Auto-Descubrimiento
ob_clean(); 
header('Content-Type: application/json; charset=utf-8');

$apiKey = 'AQUI VA TU API';

// Leer Texto
$input = json_decode(file_get_contents('php://input'), true);
$texto = $input['texto'] ?? '';

if(empty($texto)) { echo json_encode(['error' => 'Texto vacío']); exit; }

// --- PASO 1: Descubrir Modelo ---
function obtenerModeloReal($key) {
    $url = "https://generativelanguage.googleapis.com/v1beta/models?key=$key";
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); 
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200) return ['error' => "Error listando modelos (HTTP $httpCode)."];

    $data = json_decode($response, true);
    if (!isset($data['models'])) return ['error' => "Sin modelos disponibles."];

    $candidatos = [];
    foreach ($data['models'] as $m) {
        if (isset($m['supportedGenerationMethods']) && in_array('generateContent', $m['supportedGenerationMethods'])) {
            $nombre = $m['name']; 
            if (strpos($nombre, 'flash') !== false) return $nombre;
            if (strpos($nombre, 'pro') !== false) $candidatos[] = $nombre;
        }
    }

    if (!empty($candidatos)) return $candidatos[0];
    if (isset($data['models'][0]['name'])) return $data['models'][0]['name'];
    
    return ['error' => "No se encontró modelo compatible."];
}

$modeloAUsar = obtenerModeloReal($apiKey);

if (is_array($modeloAUsar) && isset($modeloAUsar['error'])) {
    echo json_encode(['error' => $modeloAUsar['error']]);
    exit;
}

// --- PASO 2: Generación (Prompt Ajustado) ---
$urlGeneracion = "https://generativelanguage.googleapis.com/v1beta/$modeloAUsar:generateContent?key=$apiKey";

// CAMBIO AQUÍ: Instrucción explícita para NO poner títulos
$prompt = "Actúa como Supervisor de Seguridad. Corrige redacción y ortografía del siguiente texto. 
REGLAS OBLIGATORIAS:
1. NO agregues títulos, encabezados, introducciones ni despedidas (ej. NO pongas 'Informe de Seguridad').
2. Devuelve SOLO el cuerpo del reporte corregido.
3. Sé técnico, objetivo y conciso.
Texto a corregir: '$texto'";

$payload = json_encode(["contents" => [["parts" => [["text" => $prompt]]]]]);

$ch = curl_init($urlGeneracion);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); 
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

if ($httpCode === 200) {
    $json = json_decode($response, true);
    if (isset($json['candidates'][0]['content']['parts'][0]['text'])) {
        echo json_encode(['texto_mejorado' => $json['candidates'][0]['content']['parts'][0]['text']]);
    } else {
        echo json_encode(['error' => 'IA respondió vacío.']);
    }
} else {
    $errBody = json_decode($response, true);
    $msg = $errBody['error']['message'] ?? 'Desconocido';
    echo json_encode(['error' => "Fallo ($modeloAUsar HTTP $httpCode): $msg"]);
}
?>