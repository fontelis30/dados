<?php
if (isset($_GET['consulta'])) {
    $ip = $_GET['consulta'];
    $api_url = "http://ip-api.com/json/{$ip}";

    $response = file_get_contents($api_url);
    $data = json_decode($response, true);

    if ($data['status'] === 'success') {
        $result = 
            "🔍 Consulta: {$data['query']} \n\n" .
            "✅ Status: sucesso \n\n" .
            "🌍 País: {$data['country']} \n\n" .
            "🇧🇷 Código do País: {$data['countryCode']} \n\n" .
            "📍 Região: {$data['region']} \n\n" .
            "🏞️ Nome da Região: {$data['regionName']} \n\n" .
            "🏙️ Cidade: {$data['city']} \n\n" .
            "📮 CEP: {$data['zip']} \n\n" .
            "🗺️ Latitude: {$data['lat']} \n\n" .
            "🗺️ Longitude: {$data['lon']} \n\n" .
            "🕰️ Fuso Horário: {$data['timezone']} \n\n" .
            "💼 ISP: {$data['isp']} \n\n" .
            "🏢 Organização: {$data['org']} \n\n" .
            "🌐 AS: {$data['as']}";

        header('Content-Type: text/plain; charset=utf-8');
        echo $result;
    } else {
        echo "❌ Status: falha \n\n💬 Mensagem: IP não encontrado";
    }
} else {
    echo "❌ Status: falha \n\n💬 Mensagem: Parâmetro de consulta não fornecido";
}
?>