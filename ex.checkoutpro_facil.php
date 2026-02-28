<?php

require_once __DIR__ . "/bmpago/checkoutpro.php";
require_once __DIR__ . "/config.php";//lê o .env

$mp_token = $_ENV['MP_TOKEN_PRO'];

$data = [
    "external_reference" => "PEDIDO-001",
    "items" => [
        [
            "title" => "Produto Teste",
            "quantity" => 1,
            "unit_price" => 49.90,
            "currency_id" => "BRL"
        ]
    ],
    "back_urls" => [
        "success" => "https://localhost/php/libmpago/verifica_retorno.php?page_status=sucesso",
        "failure" => "https://localhost/php/libmpago/verifica_retorno.php?page_status=falha",
        "pending" => "https://localhost/php/libmpago/verifica_retorno.php?page_status=pendente",
    ],
    "notification_url" => "https://site.com/webhook.php"
];

$response = bmpago_checkoutpro_create($mp_token, $data);
$erro = bmpago_check_for_errors($response);
if($erro){
	echo "Erro: " . $erro[0] . "\n";
	echo "msg: " . $erro[1]. "\n";
	exit;
}


if (!bmpago_check_for_errors($response)) {
	// $url = $response['init_point']; // modo produção / pagamento real!
	 $url = $response['sandbox_init_point']; // Use sandbox para testes
    // Retorna um card HTML estilizado para o botão de pagamento
    echo bmpago_checkoutpro_ui($url, "Finalizar","Produto: curso API MP FULL", "R$ 49,90");
}
