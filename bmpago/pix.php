<?php
//mpgago/pix.php
/**
 * BMPago - Beto Mercado Pago PHP Lib
 *
 * Biblioteca PHP minimalista para integrar pagamentos do Mercado Pago.
 *
 * @package    BMPago
 * @author     Adalberto
 * @version    1.0.0
 * @license    MIT
 */
 
//descomente caso queira ver os erros.
//error_reporting(E_ALL);
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);

require_once __DIR__ . '/core.php';


/**
 * Cria um pagamento Pix na API do Mercado Pago.
 *
 * @param string $mp_token Token de acesso (Bearer Token).
 * @param array $data Estrutura de dados do pagamento conforme API do Mercado Pago.
 * @return array|null Resposta da API decodificada.
 */
function bmpago_pix_create($mp_token, $data) {
    $url = 'https://api.mercadopago.com/v1/payments';
    return bmpago_request($mp_token, 'POST', $url, $data);
}


/**
 * Cria um pagamento Pix de forma simplificada.
 *
 * @param string $mp_token Token de acesso.
 * @param float $valor Valor do pagamento.
 * @param string $email E-mail do comprador.
 * @param string $cpf CPF do comprador.
 * @param string $notification_url URL para receber o webhook de notificação.
 * @param string $external_ref Referência externa do seu sistema (ID do Pedido).
 * @param int $expiration Tempo de expiração em minutos. Padrão: 30.
 * @param string $description Descrição do pagamento.
 * @param string $first_name Nome do comprador.
 * @param string $last_name Sobrenome do comprador.
 * @return array|null Resposta da API decodificada.
 */
function bmpago_pix_simple(
    string $mp_token, 
    float $valor, 
    string $email, 
    string $cpf, 
    string $notification_url, 
    string $external_ref, 
    int $expiration = 30, // minutos
    string $description = "", 
    string $first_name = "", 
    string $last_name = ""
) {
    // Definir descrição padrão se estiver vazia
    if (empty($description)) {
        $description = "Pix: {$cpf} | Ref: {$external_ref}";
    }

    // Estrutura do payer com verificação de campos vazios
    $payer = [
        "email" => $email,
        "identification" => [
            "type" => "CPF",
            "number" => $cpf
        ]
    ];

    // Adiciona nome apenas se informado
    if (!empty($first_name)) $payer["first_name"] = $first_name;
    if (!empty($last_name)) $payer["last_name"] = $last_name;

    // Estrutura de dados para o Mercado Pago
    $data = [
        "transaction_amount" => $valor,
        "date_of_expiration" => bmpago_generate_date($expiration),
        "payment_method_id" => "pix",
        "external_reference" => $external_ref,
        "notification_url" => $notification_url,
        "description" => $description,
        "payer" => $payer
    ];

    return bmpago_pix_create($mp_token, $data);
}

/**
 * Gera uma string HTML com a interface do Pix (QR Code, Copia e Cola e Contador).
 *
 * @param array $response Resposta da API do Mercado Pago (com status pendente).
 * @param string $titulo Título do produto/pedido.
 * @return string HTML formatado da interface Pix.
 */
function bmpago_pix_ui($response, $titulo) {                
    if (!isset($response['point_of_interaction']['transaction_data'])) {
        return "<div class='mp-error' style='color:red;'>Erro ao gerar o Pix. Resposta inválida.</div>";
    }

    $transaction_data = $response['point_of_interaction']['transaction_data'];
    $qr_code = $transaction_data['qr_code'];
    $qr_image = $transaction_data['qr_code_base64'];
    $valor = number_format($response['transaction_amount'], 2, ',', '.');
    
    // ⚠️ Pega a data de expiração retornada pela API
    $data_expiracao = $response['date_of_expiration']; 
    
    $html = "<div class='mp-pix-container' style='border: 1px solid #ccc; padding: 20px; border-radius: 8px; max-width: 400px; text-align: center; font-family: sans-serif; box-shadow: 2px 2px 10px rgba(0,0,0,0.1);'>";
    
    $html .= "  <h3 style='margin-top: 0; color: #333;'>{$titulo}</h3>";
    $html .= "  <p style='font-size: 1.2em; font-weight: bold; color: #009ee3;'>Valor: R$ {$valor}</p>";
    
    // Imagem do QR Code
    $html .= "  <div class='mp-qr-code' style='margin: 20px 0;'>";
    $html .= "      <img src='data:image/jpeg;base64,{$qr_image}' alt='QR Code Pix' style='max-width: 200px; height: auto;' />";
    $html .= "  </div>";
    
    // ⚠️ Adiciona o elemento do contador
    $html .= "  <p style='font-size: 0.9em; color: #333; font-weight: bold;'>Tempo restante: <span id='pix-timer' style='color: #e65100;'>--:--</span></p>";
    
    // Código Copy-Paste
    $html .= "  <p style='font-size: 0.9em; color: #666;'>Ou copie o código abaixo:</p>";
    $html .= "  <div class='mp-copy-paste' style='background: #f4f4f4; padding: 10px; border-radius: 4px; word-break: break-all; font-family: monospace; font-size: 0.85em; cursor: pointer; border: 1px solid #eee;' onclick='navigator.clipboard.writeText(\"{$qr_code}\"); alert(\"Código Pix copiado!\");'>";
    $html .= "      {$qr_code}";
    $html .= "  </div>";
    
    // ⚠️ JavaScript para o contador
    $html .= "  <script>
        (function() {
            var countDownDate = new Date('{$data_expiracao}').getTime();
            var timerElement = document.getElementById('pix-timer');

            var x = setInterval(function() {
                var now = new Date().getTime();
                var distance = countDownDate - now;

                var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                var seconds = Math.floor((distance % (1000 * 60)) / 1000);

                timerElement.innerHTML = minutes + 'm ' + seconds + 's ';

                if (distance < 0) {
                    clearInterval(x);
                    timerElement.innerHTML = 'EXPIRADO';
                    timerElement.style.color = 'red';
                }
            }, 1000);
        })();
    </script>";

    $html .= "</div>";
    
    return $html;
}

/*
//exemplo completo: bmpago_pix_create();
$mp_token = "*********************************************"; //seu token Mercado Pago

$response = bmpago_pix_create($mp_token, [

    "transaction_amount" => 12.34,
    "date_of_expiration" => bmpago_generate_date(30), //30 minutos para expirar.
    "payment_method_id" => "pix",
    "external_reference" => "1234",
    "notification_url" => "https://seusite/notification.php", //deve ser válido
    "description" => "PEDIDO NOVO - VIDEOGAME",
    "payer" => [
        "first_name" => "Joao",
        "last_name" => "Silva",
        "email" => "teste@testeuser.com",
        "identification" => [
            "type" => "CPF",
            "number" => "99999999999"
        ]
    ],
    "additional_info" => [
        "items" => [
            [
                "id" => "1941",
                "title" => "Ingresso Antecipado",
                "description" => "Natal Iluminado 2019",
                "picture_url" => null,
                "category_id" => "Tickets",
                "quantity" => 1,
                "unit_price" => 12.34,
                "event_date" => bmpago_generate_date(0)
            ]
        ],
        "payer" => [
            "first_name" => "Nome",
            "last_name" => "Sobrenome",
            "is_prime_user" => "1",
            "is_first_purchase_online" => "1",
            "last_purchase" => "2019-10-25T19:30:00.000-03:00",
            "phone" => [
                "area_code" => "11",
                "number" => "987654321"
            ],
            
            "address" => [
                "zip_code" => "06233-200",
                "street_name" => "Av. das Nações Unidas",
                "street_number" => "3003"
            ],
            "registration_date" => "2013-08-06T09:25:04.000-03:00"
        ],
        
        //dados de entregas (opcional)
        "shipments" => [
            "express_shipment" => "0",
            "pick_up_on_seller" => "1",
            "receiver_address" => [
                "zip_code" => "95630000",
                "street_name" => "são Luiz",
                "street_number" => "15",
                "floor" => "12",
                "apartment" => "123"
            ]
        ]
    ] 


]);


// A leitura do código fica intuitiva
if ($erro = bmpago_check_for_errors($response)) {
    // Trata o erro usando $erro[0] (código) e $erro[1] (mensagem)
    echo "Erro: " . $erro[1];
} else {
    // Sucesso! Cria a interface gráfica do pix:
    echo bmpago_pix_ui($response, "Pedido 123");
}

echo var_dump($response);
 
*/


/*
 
//exemplo completo:  bmpago_pix_simple();


$pix_valor = 15.99; //valor do pix em BRL (pix é só no Brasil)
$pix_email = "user@test.com"; //email do comprador
$pix_cpf = "99999999999"; //cpf do comprador
$pix_notification_url = "https://seusite/notification.hook"; //link de webhook para o mp notificar.
$pix_id = "pix-00009999"; //id para saber quem pagou e liberar o produto
$pix_expiration = 10; //expira em 10 minutos.
$pix_description = "Pagamento produto: curso api"; //descrição do seu pix.

$response = bmpago_pix_simple(
    $mp_token, 
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


// A leitura do código fica intuitiva
if ($erro = bmpago_check_for_errors($response)) {
    // Trata o erro usando $erro[0] (código) e $erro[1] (mensagem)
    echo "Erro: " . $erro[1];
} else {
    // Sucesso! Cria a interface gráfica do pix:
    echo bmpago_pix_ui($response, "Pedido 123");
}

*/
