<?php
//mpago/core.php 'core'

/**
 * Gera uma data formatada no padrão ISO 8601 exigido pelo Mercado Pago.
 * Útil para definir prazos de expiração (Pix).
 *
 * @param int $minutesToAdd Minutos a serem adicionados à data atual. Padrão: 30.
 * @return string Data formatada ex: 2026-02-28T10:30:00.000-03:00
 */
function bmpago_generate_date($minutesToAdd = 30) {
    // Cria um objeto DateTime atual
    $date = new DateTime();
    
    // Adiciona os minutos de expiração (ideal para Pix)
    $date->modify("+$minutesToAdd minutes");
    
    // Formata para o padrão ISO 8601 exigido pelo MP
    // Exemplo: 2026-02-28T10:30:00.000-03:00
    return $date->format('Y-m-d\TH:i:s.vP');
}


/**
 * Função base para realizar requisições HTTP (cURL) para a API do Mercado Pago.
 *
 * @param string $mp_token Token de acesso (Bearer Token).
 * @param string $method   Método HTTP (GET, POST, PUT, DELETE).
 * @param string $url      URL do endpoint da API.
 * @param array|null $data Dados para enviar no corpo da requisição (JSON).
 * @return array|null      Resposta da API decodificada como array ou array de erro.
 */
function bmpago_request($mp_token, $method, $url, $data = null) {
    $curl = curl_init();

    $headers = [
        "Authorization: Bearer $mp_token",
        "Content-Type: application/json"
    ];

    // Se for POST, adiciona Idempotency Key para segurança
    if ($method == 'POST') {
        $headers[] = "X-Idempotency-Key: " . bin2hex(random_bytes(16));
    }

    $options = [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_HTTPHEADER => $headers,
    ];

    if ($data && $method == 'POST') {
        $options[CURLOPT_POSTFIELDS] = json_encode($data);
    }

    curl_setopt_array($curl, $options);
    $response = curl_exec($curl);
    $error = curl_error($curl);
    curl_close($curl);

    if ($error) return ['success' => false, 'error' => $error];
    
    return json_decode($response, true);
}



/**
 * Busca detalhes de um pagamento específico pelo ID.
 *
 * @param string $mp_token Token de acesso.
 * @param string $payment_id ID do pagamento a ser consultado.
 * @return array|null Resposta da API com os detalhes do pagamento.
 */
function bmpago_get_payment($mp_token, $payment_id) {
    $url = "https://api.mercadopago.com/v1/payments/" . $payment_id;
    // Chama a função base que você criou
    return bmpago_request($mp_token, 'GET', $url);
}

/**
 * Valida a resposta da API para detectar erros.
 * Entende diferentes formatos de erro (Pix/CheckoutPro).
 *
 * @param array|null $response Resposta crua da API.
 * @return array|false Retorna um array [código, mensagem] se houver erro, ou false se sucesso.
 */
function bmpago_check_for_errors($response) {
    // 1. Se não for array, o cURL falhou feio
    if (!is_array($response)) {
        return ["Invalid API", "API error: (check token or connection) NULL."];
    }

    // 2. Se tiver 'status', é a API de Pagamento (Pix)
    if (isset($response['status'])) {
        // Verifica se há erro (formato 1: 'error' e 'message')
        if (isset($response['error'])) {
            return [
                $response['error'],
                $response['message'] ?? 'Error message not found'
            ];
        }
        
        // Verifica se há erro (formato 2: 'code' e 'message') - SUA MELHORIA AQUI
        if (isset($response['code']) && isset($response['message']) ){
            return [
                $response['code'],
                $response['message']
            ];
        }
        // Se tem status mas não tem erro, está OK!
        return false;
    }

    // 3. Se não tem 'status', mas tem 'id' e 'init_point', é Checkout Pro de SUCESSO
    if (isset($response['id']) && isset($response['init_point'])) {
        return false;
    }

    // 4. Se chegou aqui, é um erro estrutural (geralmente erro de validação do MP)
    return [
        $response['code'] ?? $response['error'] ?? 'unknown_code',
        $response['message'] ?? 'Unknown error'
    ];
}
