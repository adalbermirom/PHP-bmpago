<?php

require_once __DIR__ . "/bmpago/pix.php";
require_once __DIR__ . "/bmpago/core.php";
require_once __DIR__ . "/config.php"; //$mp_token_pix

//exemplo:

//echo "Response:\n", var_dump($response);
//echo $response['status'], "\n\n";
$pix_valor = 15.99; //valor do pix em BRL (pix é só no Brasil)
$pix_email = "user@test.com"; //email do comprador
$pix_cpf = "99999999999"; //cpf do comprador
$pix_notification_url = "https://seu_site/notification.hook"; //link válido de webhook para o mp notificar. 
$pix_id = "pix-00009999"; //id para saber quem pagou e liberar o produto
$pix_expiration = 10; //expira em 10 minutos.
$pix_description = "Pagamento produto: curso api"; //descrição do seu pix.

$response = bmpago_pix_simple(
    $mp_token_pix, 
    $pix_valor, 
    $pix_email, 
    $pix_cpf,
    $pix_notification_url, 
    $pix_id, 
    $pix_expiration, 
    $pix_description,
    "Joao",
    "Silva"
);

//echo var_dump($response);

// A leitura do código fica intuitiva
if ($erro = bmpago_check_for_errors($response)) {
    // Trata o erro usando $erro[0] (código) e $erro[1] (mensagem)
    echo "Erro: " . $erro[1];
} else {
    // Sucesso! Cria a interface gráfica do pix:
    echo bmpago_pix_ui($response, "Pedido 123");
}



