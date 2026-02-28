<?php
//mpgago/checkoutpro.php

// Reportar todos os tipos de erros
error_reporting(E_ALL);

// Forçar a exibição dos erros na tela (output)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

require_once __DIR__ . '/core.php';


/**
 * Cria uma preferência de pagamento (Checkout Pro) na API do Mercado Pago.
 *
 * Gera o link (`init_point`) que deve ser redirecionado ao cliente para finalizar a compra.
 *
 * @param string $mp_token Token de acesso (Bearer Token).
 * @param array $data Estrutura de dados da preferência conforme API do Mercado Pago.
 * @return array|null Resposta da API decodificada contendo o link de pagamento.
 */
function bmpago_checkoutpro_create($mp_token, $data) { 
    $url = 'https://api.mercadopago.com/checkout/preferences';
    return bmpago_request($mp_token, 'POST', $url, $data);
}



/**
 * Retorna o HTML de um Card com estilo próprio, sem depender de CSS externo.
 *
 * @param string $url     URL de redirecionamento (init_point ou sandbox_init_point).
 * @param string $titulo  Título do produto/serviço.
 * @param string $content Descrição do produto.
 * @param string $valor   Valor formatado do produto (ex: "R$ 49,90").
 * @return string HTML formatado.
 */
function bmpago_checkoutpro_ui($url, $titulo = "Finalizar", $content="", $valor = ""){
    
    // Container do Card com estilos inline
    $html = '<div style="
        width: 100%; 
        max-width: 300px; 
        margin: 20px auto; 
        text-align: center; 
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; 
        border: 1px solid #e0e0e0; 
        border-radius: 8px; 
        box-shadow: 0 2px 4px rgba(0,0,0,0.05); 
        background-color: #ffffff;
        overflow: hidden;
    ">';
    
    // Corpo do Card
    $html .= '  <div style="padding: 20px;">';
    
    // Título - Ajustado para 20px para não quebrar o layout
    $html .= '    <h1 style="margin: 0 0 10px 0; color: #333333; font-weight: 600; font-size: 20px; line-height: 1.2;">' . htmlspecialchars($titulo) . '</h1>';
    
    // Conteúdo (Descrição)
    if (!empty($content)){
         $html .= '    <p style="margin: 0 0 15px 0; color: #666666; font-size: 14px; line-height: 1.4;">' . htmlspecialchars($content) . '</p>';
    }
    
    // Valor
    if (!empty($valor)) {
        $html .= '    <p style="font-size: 24px; font-weight: bold; color: #009ee3; margin: 0 0 20px 0;">' . htmlspecialchars($valor) . '</p>';
    }
    
    // Botão estilo Mercado Pago
    $html .= '    <a href="' . $url . '" style="
        display: block;
        background-color: #009ee3;
        color: #ffffff;
        padding: 12px 20px;
        text-decoration: none;
        border-radius: 6px;
        font-weight: 600;
        font-size: 16px;
        transition: background-color 0.2s;
    ">Pagar com Mercado Pago</a>';
    
    $html .= '  </div>';
    $html .= '</div>';

    return $html;
}



