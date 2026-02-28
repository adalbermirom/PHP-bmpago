<?php
require_once __DIR__ . '/core.php';


/**
 * Captura a notificação do Mercado Pago (Webhook) e valida se o pagamento foi aprovado.
 *
 * Esta função lê a notificação do 'php://input', obtém o ID do pagamento e faz uma consulta
 * direta à API do Mercado Pago para confirmar o status oficial, garantindo segurança.
 *
 * @param string $mp_token Token de acesso (Bearer Token).
 * @return array|false Retorna os dados do pagamento (array) se aprovado, ou false se inválido/rejeitado.
 */
function bmpago_webhook_confirm_payment($mp_token) {
    // 1. Lê os dados enviados pelo MP
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    // 2. Valida se a estrutura básica existe e é do tipo 'payment'
    if (!$data || !isset($data['type']) || $data['type'] !== 'payment') {
        return false;
    }

    // Pega o ID do pagamento que o MP enviou no corpo da requisição
    $payment_id = $data['data']['id'] ?? null;
    if (!$payment_id) return false;

    // 3. Usa a sua função existente para buscar os dados oficiais
    $payment_info = bmpago_get_payment($mp_token, $payment_id);

    // 4. Verifica se o pagamento está aprovado na fonte oficial
    if ($payment_info && isset($payment_info['status']) && $payment_info['status'] === 'approved') {
        return $payment_info; 
    }

    return false;
}

/* exemplo de confirmação no webhook:
// No seu arquivo webhook.php
if ($pagamento = bmpago_webhook_confirm_payment($token)) {
    $ref = $pagamento['external_reference'];
    // Sucesso! O dinheiro caiu e você tem a referência do seu banco de dados.
    // MarcarPedidoComoPago($ref); //código para liberar o recurso ou produto.
}
*/
