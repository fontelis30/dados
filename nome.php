<?php
header('Content-Type: text/html; charset=utf-8');

// Fun�0�4�0�0o para realizar a consulta �� API
function consultaNome($nome) {
    $api_url = 'https://pnsapis.online/api/consultasoff';
    $apitoken = 'Mgditalvip';

    // Codifica a query corretamente, substituindo espa�0�4os por %20
    $query = urlencode($nome);

    // Monta a URL de consulta
    $url = "$api_url?type=nome&query=$query&apitoken=$apitoken";

    // Inicializa a sess�0�0o cURL
    $ch = curl_init();

    // Configura as op�0�4�0�1es do cURL
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    // Executa a requisi�0�4�0�0o e armazena a resposta
    $response = curl_exec($ch);

    // Fecha a sess�0�0o cURL
    curl_close($ch);

    // Retorna a resposta decodificada como um array associativo
    return json_decode($response, true);
}

// Verifica se o par�0�9metro 'consulta' est�� presente na URL
if (isset($_GET['consulta'])) {
    // Codifica novamente a consulta recebida para garantir que os espa�0�4os sejam interpretados corretamente
    $nome = $_GET['consulta'];
    $resultado = consultaNome($nome);

    // Verifica se a consulta foi bem-sucedida
    if (isset($resultado['status']) && $resultado['status']) {
        echo "<h1>Resultados da Consulta para: " . htmlspecialchars($nome) . "</h1>";

        // Processa e exibe os resultados
        echo "<pre>";
        $resultado_texto = $resultado['resultado'];
        
        // Substitui @annonimobotchannel por @nexabuscas
        $resultado_texto = str_replace('@annonimobotchannel', '@nexabuscas', $resultado_texto);

        foreach (explode("\n", $resultado_texto) as $line) {
            // Verifica se a linha cont��m informa�0�4�0�1es dos resultados e imprime uma linha em branco para separa�0�4�0�0o
            if (strpos($line, 'RESULTADO') !== false) {
                echo "\n";
            }
            echo htmlspecialchars($line) . "\n";
        }
        echo "</pre>";
    } else {
        echo "<h1>Erro na Consulta</h1>";
        echo "<p>N�0�0o foi poss��vel realizar a consulta.</p>";
    }
} else {
    echo "<h1>Consulta de Nome</h1>";
    echo "<p>Adicione o par�0�9metro 'consulta' na URL para realizar a consulta. Exemplo: <code>?consulta=Maria%20da%20Silva%20Pereira</code></p>";
}
?>