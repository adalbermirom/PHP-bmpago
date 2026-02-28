<?php
// Reportar todos os tipos de erros
error_reporting(E_ALL);
// Forçar a exibição dos erros na tela (output)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);


require_once __DIR__ . "/bmpago/checkoutpro.php";
require_once __DIR__ . "/config.php";
$mp_token = $_ENV['MP_TOKEN_PRO'];
echo "<h1>Mercado Pago checkoutpro Test</h1>";
echo "<h3>Produto teste</h3>";

echo "<h3>Valor: R$0,99</h3>";


//datajson = json_encode($data);
$data = [
    //"auto_return" => "approved",
    
    //urls de retornos:
    "back_urls" => [
        "success" => "https://localhost/php/libmpago/verifica_retorno.php?page_status=sucesso",
        "failure" => "https://localhost/php/libmpago/verifica_retorno.php?page_status=falha",
        "pending" => "https://localhost/php/libmpago/verifica_retorno.php?page_status=pendente",
    ],
    "statement_descriptor" => "TestStore",
    "binary_mode" => false,
    "external_reference" => "IWD1238971", //deve ser um identificador do cliente.
    
    //o produto a ser vendido, deve ser pelo menos 1.
    "items" => [
        [
            "id" => "010983098", //id único de seu produto.
            "title" => "Meu produto",
            "quantity" => 1,
            "unit_price" => (float)$preco_formatado = number_format(4.99, 2, '.', ''),
            "description" => "Descrição do Produto test",
            "category_id" => "Categoria produto"
        ]
    ],
    
    //pagador dados:
    "payer" => [
        "email" => "test_user_12398378192@testuser.com",
        "name" => "Juan",
        "surname" => "Lopez",
       
       /* "phone" => [
            "area_code" => "11",
            "number" => "1523164589"
        ],*/
        "identification" => [
            "type" => "CPF",
            "number" => "01950476138"
        ]
        
        /*,
        "address" => [
            "street_name" => "Street",
            "street_number" => 123,
            "zip_code" => "1406"
        ]*/
    ],
    
    //metodos de pagamentos:
    "payment_methods" => [
        "excluded_payment_types" => [],
        "excluded_payment_methods" => [],
        "installments" => 12,
        "default_payment_method_id" => "account_money"
    ],
    
    //url de notificação (webhook)
    "notification_url" => "https://br4.biz/mpago/notification.hook",
    //"expires" => true,
    //"expiration_date_from" => "2024-01-01T12:00:00.000-04:00",
    //"expiration_date_to" => "2024-12-31T12:00:00.000-04:00"
];

$response = bmpago_checkoutpro_create($mp_token, $data);


$erro = bmpago_check_for_errors($response);

if (!$erro) {
    $url = $response['sandbox_init_point']; // Use sandbox para testes
    $html = bmpago_checkoutpro_ui(
                                   $url, 
                                   "Finalizar pagamento",
                                   'Curso Pro api Mercado Pago.',
                                   'R$ 0,99' 
                                 );
    echo $html;
    exit;
}
else {
	echo "Erro: " . $erro[0] . "\n";
	echo "msg: " . $erro[1] . "\n\n";
	echo var_dump($response);
}



exit;
?>


