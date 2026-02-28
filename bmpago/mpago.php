<?php
/**
 * BmPago - Biblioteca Minimalista Mercado Pago
 * Versão: 1.0.0
 * Licença: MIT
 * Author: Adalberto Amorim Felipe
 * 
 * modulo de carregamento total.
 */

// 1. Carrega o Core (Requisições cURL)
require_once __DIR__ . '/core.php';
//bmpago_get_payment();
//bmpago_generate_date();
//bmpago_request();
//bmpago_get_payment(); bmpago_check_for_errors();


// 2. Carrega os Módulos
require_once __DIR__ . '/pix.php';         
// Funções: bmpago_pix_simple, bmpago_pix_create() e mppago_ui_pix


require_once __DIR__ . '/checkoutpro.php'; // Funções: bmpago_checkoutpro_create();

require_once __DIR__ . '/webhook.php';     // Funções: bmpago_webhook_confirm_payment();



