<?php
//essa página é usada quando o usuário retorna do checkoutpro. (back_urls)
// Reportar todos os tipos de erros
error_reporting(E_ALL);

// Forçar a exibição dos erros na tela (output)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

require_once __DIR__ . "/bmpago/checkoutpro.php";
require_once __DIR__ . "/bmpago/core.php"; //core common functions
require_once __DIR__ . "/config.php"; //$mp_token

// Lê os dados da URL (GET)
$status = $_GET['collection_status'] ?? 'desconhecido';

echo 'Page status: ' . $_GET['page_status'];

if ($status == '' || $status == 'null' || $status == null || $status == 'desconhecido'){
	$status = $_GET['page_status'];
}
$payment_id = $_GET['payment_id'] ?? 'não informado';
$preference_id = $_GET['preference_id'] ?? 'não informado';

// Para debug visual na página de sucesso/falha
echo "<h1>Resultado do Pagamento</h1>";
echo "<p><strong>Status:</strong> " . htmlspecialchars($status) . "</p>";
echo "<p><strong>ID do Pagamento:</strong> " . htmlspecialchars($payment_id) . "</p>";

// DICA: Use o $payment_id para fazer uma consulta na API
// e confirmar se ele foi aprovado de verdade.

// No seu verifica_retorno.php
$payment_id = $_GET['payment_id'] ?? null;


if ($payment_id) {
    $payment_info = bmpago_get_payment($mp_token, $payment_id);
    
    // Debug: ver o que o MP respondeu
    // echo "<pre>"; print_r($payment_info); echo "</pre>";

    if (isset($payment_info['status']) && $payment_info['status'] == 'approved') {
        echo "<h1>✅ Pagamento Validado na API!</h1>";
    } else {
        echo "<h1>❌ Pagamento não aprovado ou inválido na API.</h1>";
        echo "Status recebido: " . ($payment_info['status'] ?? 'desconhecido');
    }
}
