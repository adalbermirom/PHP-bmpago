<?php

/* Defina os tokens no arquivo .env e coloque numa pasta fora do acesso público da internet:

#.env substitua pelos seus tokens do mercado pago!
MP_TOKEN_PRO="APP_USR-************************************************";
MP_TOKEN_PIX="TEST****************************************************";
*/

require_once __DIR__ . "/butils/env.php"; //load_env();

//Não deixe o .env em uma pasta de acesso público (public_http, htdocs etc)
//Coloque fora, exemplo: ( etc/meuapp/.env, ou /home/meu_user/./meuapp/.env etc)
//passe o caminho completo do .env:
load_env('.env');

$mp_token = $_ENV['MP_TOKEN_PRO'];//para o checkout pro.
$mp_token_pix = $_ENV['MP_TOKEN_PIX'];//para gerar o pix.
