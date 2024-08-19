<?php
if (isset($_GET['consulta'])) {
    $telefone = urlencode($_GET['consulta']);
    $apitoken = 'Mgditalvip'; // Seu token de API
    $url = "https://pnsapis.online/api/consultasoff?type=telefone&query=$telefone&apitoken=$apitoken";

    // Inicializa cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60); // Define o timeout para 60 segundos
    
    // Executa a requisi�0�4�0�0o
    $response = curl_exec($ch);
    $curl_error = curl_error($ch);
    curl_close($ch);
    
    if ($response === false) {
        echo "status: false\nmessage: Erro na consulta. Erro cURL: " . $curl_error;
        exit;
    }
    
    // Decodifica a resposta JSON
    $data = json_decode($response, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        echo "status: false\nmessage: Erro na decodifica�0�4�0�0o JSON: " . json_last_error_msg();
        exit;
    }

    if (!isset($data['status']) || !$data['status']) {
        echo "status: false\nmessage: Erro na consulta. Resposta da API: " . $response;
        exit;
    }

    // Remove os cr��ditos originais e a contagem de requests
    unset($data['By']);
    unset($data['criador']);
    unset($data['Requests']);
    
    // Verifica se o resultado est�� vazio ou n�0�0o cont��m dados pessoais
    if (empty($data['resultado']) || strpos($data['resultado'], 'NENHUM RESULTADO') !== false || !preg_match('/NOME: |CPF: /', $data['resultado'])) {
        echo "status: true\nresultado: Telefone nao encontrado";
        exit;
    } else {
        // Fun�0�4�0�0o para limpar caracteres especiais
        function clean_text($text) {
            return preg_replace('/[^A-Za-z0-9\s\-:]/', '', $text);
        }

        // Formatar o resultado
        $resultados = explode("\n\n", $data['resultado']);
        $resultados_formatados = [];
        $resultado_index = 1;
        $resultados_encontrados = false;

        foreach ($resultados as $resultado) {
            $linhas = explode("\n", $resultado);
            $resultado_formatado = [];
            $add_resultado = false;

            foreach ($linhas as $linha) {
                $linha = trim($linha);
                if (empty($linha) || strpos($linha, 'SEM INFORMA�0�5�0�1O') !== false) {
                    continue;
                }

                $add_resultado = true;
                $resultados_encontrados = true;

                if (strpos($linha, 'NOME: ') !== false) {
                    $resultado_formatado[] = "RESULTADO: $resultado_index";
                    $resultado_formatado[] = "NOME: " . clean_text(trim(substr($linha, 6)));
                } elseif (strpos($linha, 'CPF: ') !== false) {
                    $resultado_formatado[] = "CPF/CNPJ: `" . clean_text(trim(substr($linha, 5))) . "`"; // Adiciona crases para facilitar a c��pia
                } elseif (strpos($linha, 'M�0�1E: ') !== false) {
                    $resultado_formatado[] = "M�0�1E: " . clean_text(trim(substr($linha, 5)));
                } elseif (strpos($linha, 'ENDERE�0�5O: ') !== false) {
                    $resultado_formatado[] = "ENDERE�0�5O:";
                } elseif (strpos($linha, 'TIPO: ') !== false) {
                    $resultado_formatado[] = "  TIPO: " . clean_text(trim(substr($linha, 6)));
                } elseif (strpos($linha, 'LOGRADOURO: ') !== false) {
                    $resultado_formatado[] = "  LOGRADOURO: " . clean_text(trim(substr($linha, 12)));
                } elseif (strpos($linha, 'N�0�3MERO: ') !== false) {
                    $resultado_formatado[] = "  N�0�3MERO: " . clean_text(trim(substr($linha, 9)));
                } elseif (strpos($linha, 'COMPLEMENTO: ') !== false) {
                    $resultado_formatado[] = "  COMPLEMENTO: " . clean_text(trim(substr($linha, 13)));
                } elseif (strpos($linha, 'BAIRRO: ') !== false) {
                    $resultado_formatado[] = "  BAIRRO: " . clean_text(trim(substr($linha, 8)));
                } elseif (strpos($linha, 'CIDADE: ') !== false) {
                    $resultado_formatado[] = "  CIDADE: " . clean_text(trim(substr($linha, 8)));
                } elseif (strpos($linha, 'CEP: ') !== false) {
                    $resultado_formatado[] = "  CEP: " . clean_text(trim(substr($linha, 5)));
                }
            }

            if ($add_resultado && !empty($resultado_formatado)) {
                $resultados_formatados[] = implode("\n", $resultado_formatado);
                $resultado_index++;
            }
        }

        if (!$resultados_encontrados) {
            $data['resultado'] = "Telefone nao encontrado";
        } else {
            // Reorganizar o texto para formato desejado
            $data['resultado'] = implode("\n\n", $resultados_formatados);
        }
    }
    
    // Exibe o resultado sem os cr��ditos originais
    header('Content-Type: text/plain; charset=UTF-8'); // Define o tipo de conte��do como texto simples com UTF-8
    echo "status: true\nresultado:\n" . $data['resultado'];
} else {
    header('Content-Type: text/plain'); // Define o tipo de conte��do como texto simples
    echo "status: false\nmessage: Par�0�9metro 'consulta' n�0�0o fornecido.";
}
?>