<?php
if (isset($_GET['consulta'])) {
    $cnpj = $_GET['consulta'];
    $api_url = "https://www.receitaws.com.br/v1/cnpj/{$cnpj}";

    // Obtendo dados da API
    $response = file_get_contents($api_url);
    if ($response === false) {
        die("Erro ao acessar a API da ReceitaWS.");
    }

    // Decodificando a resposta JSON para um array associativo
    $data = json_decode($response, true);
    if ($data === null || !isset($data['status']) || $data['status'] !== 'OK') {
        die("Erro ao obter dados do CNPJ.");
    }

    // Função para remover caracteres especiais do JSON
    function limparJson($json) {
        $json = preg_replace('/[{}\"\[\]\\\\]/', '', $json);
        return $json;
    }

    // Convertendo o array para JSON e removendo caracteres especiais
    $json_data = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    $json_data_limpo = limparJson($json_data);

    // Exibindo o resultado limpo como JSON
    header('Content-Type: application/json');
    echo $json_data_limpo;
} else {
    echo json_encode(["erro" => "Parâmetro de consulta não fornecido."]);
}
?>