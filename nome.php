<?php
header('Content-Type: text/html; charset=utf-8');

// Função para realizar a consulta à API
function consultaNome($nome) {
    $api_url = 'https://pnsapis.online/api/consultasoff';
    $apitoken = 'Mgditalvip';

    // Codifica a query corretamente, substituindo espaços por %20
    $query = urlencode($nome);

    // Monta a URL de consulta
    $url = "$api_url?type=nome&query=$query&apitoken=$apitoken";

    // Inicializa a sessão cURL
    $ch = curl_init();

    // Configura as opções do cURL
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    // Executa a requisição e armazena a resposta
    $response = curl_exec($ch);

    // Fecha a sessão cURL
    curl_close($ch);

    // Retorna a resposta decodificada como um array associativo
    return json_decode($response, true);
}

// Verifica se o parâmetro 'consulta' está presente na URL
if (isset($_GET['consulta'])) {
    // Codifica novamente a consulta recebida para garantir que os espaços sejam interpretados corretamente
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
            // Verifica se a linha contém informações dos resultados e imprime uma linha em branco para separação
            if (strpos($line, 'RESULTADO') !== false) {
                echo "\n";
            }
            echo htmlspecialchars($line) . "\n";
        }
        echo "</pre>";
    } else {
        echo "<h1>Erro na Consulta</h1>";
        echo "<p>Não foi possível realizar a consulta.</p>";
    }
} else {
    echo "<h1>Consulta de Nome</h1>";
    echo "<p>Adicione o parâmetro 'consulta' na URL para realizar a consulta. Exemplo: <code>?consulta=Maria%20da%20Silva%20Pereira</code></p>";
}
?>