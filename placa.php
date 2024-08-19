<?php
// Obter a consulta da URL
if (!isset($_GET['consulta'])) {
    die('Consulta não fornecida.');
}

$placa = $_GET['consulta'];
$apiToken = 'Mgditalvip';
$apiUrl = "https://pnsapis.online/api/consultasoff?type=placa&query={$placa}&apitoken={$apiToken}";

// Inicializar cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

// Obter resposta da API
$response = curl_exec($ch);
curl_close($ch);

if (!$response) {
    die('Erro ao consultar API.');
}

// Decodificar a resposta JSON
$data = json_decode($response, true);

if (!isset($data['status']) || !$data['status']) {
    die('Consulta falhou.');
}

// Obter o resultado da consulta
$result = $data['resultado'];

// Remover créditos
$result = preg_replace('/By:.*/', '', $result);
$result = preg_replace('/criador:.*/', '', $result);

// Formatar e exibir o resultado
echo "<pre>";
echo htmlspecialchars($result);
echo "</pre>";
?>