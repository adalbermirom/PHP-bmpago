# 🚀 BMPago - Library Mercado Pago PHP - Pix & Checkout Pro

Uma biblioteca PHP minimalista e poderosa para integrar pagamentos do Mercado Pago em segundos. Focada em simplicidade e produtividade.

### ✨ Funcionalidades Pix Simples: 

* Gere cobranças Pix com poucas linhas de comando.
* Auto UI: Retorna o HTML pronto com QR Code e Contador regressivo.
* Webhook Seguro: Validação automática de pagamentos aprovados.
* Checkout Pro: Integração completa para Cartão e Boleto.

### 🛠️ Como usar

#### Gerando um Pix
```PHP 
require_once __DIR__ . "/bmpago/pix.php"; //funções mercado pago pix
require_once __DIR__ . "/bmpago/core.php"; //funções utilitárias

$response = bmpago_pix_simple(
    "SEU_TOKEN_MP",
    15.90,                  // Valor
    "cliente@email.com",    // Email
    "12345678909",          // CPF
    "https://site.com/hook",// URL de Notificação
    "PEDIDO-001"            // Referência Única
);

if (!bmpago_check_for_errors($response)) {
    echo mppago_ui_pix($response, "Minha Venda Top");
}
```

#### Gerando um checkoutpro:
```PHP
require_once __DIR__ . "/bmpago/checkoutpro.php";
require_once __DIR__ . "/bmpago/core.php";

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
        "success" => "https://site.com/sucesso",
        "failure" => "https://site.com/erro",
        "pending" => "https://site.com/pending"
    ],
    "notification_url" => "https://site.com/webhook.php" //deve ser válido.
];

$response = bmpago_checkoutpro_create("SEU_TOKEN_MP", $data);

if (!bmpago_check_for_errors($response)) {
    // Retorna um card HTML estilizado para o botão de pagamento
    echo bmpago_checkoutpro_ui($response['init_point'], "Finalizar","Produto: curso API MP FULL", "R$ 49,90");
}
```


#### Processando o Webhook

Crie um arquivo webhook.php e coloque este código:

```PHP
require_once __DIR__ . "/bmpago/core.php";

if ($pagamento = bmpago_webhook_confirm_payment("SEU_TOKEN_MP")) {
    $ref = $pagamento['external_reference'];
    // Lógica para liberar o produto no seu banco de dados
    http_response_code(200);
}
```

## Exemplo de uso
Consulte os arquivos na pasta `raiz` para ver exemplos.

## Documentação Técnica

Abra o arquivo `docs/index.html` no seu navegador.


## 📚 Referência de Funções


### bmpago_check_for_errors()

Valida a resposta da API para detectar erros.
bmpago_check_for_errors(array<string|int, mixed>|null $response) : array<string|int, mixed>|false

Entende diferentes formatos de erro (Pix/CheckoutPro).

**Parameters**

$response : array<string|int, mixed>|null

    Resposta crua da API.

**Return values**

    array<string|int, mixed>|false —

Retorna um array [código, mensagem] se houver erro, ou false se sucesso.

---
 
### bmpago_checkoutpro_create()

Cria uma preferência de pagamento (Checkout Pro) na API do Mercado Pago.

`bmpago_checkoutpro_create(string $mp_token, array<string|int, mixed> $data) : array<string|int, mixed>|null`

Gera o link (init_point) que deve ser redirecionado ao cliente para finalizar a compra.

**Parameters**

$mp_token : string

    Token de acesso (Bearer Token).

$data : array<string|int, mixed>

    Estrutura de dados da preferência conforme API do Mercado Pago.

**Return values**

array<string|int, mixed>|null

Resposta da API decodificada contendo o link de pagamento.

---

### bmpago_checkoutpro_ui()



Retorna o HTML de um Card com estilo próprio, sem depender de CSS externo.

`bmpago_checkoutpro_ui(string $url[, string $titulo = "Finalizar" ][, string $content = "" ][, string $valor = "" ]) : string`

**Parameters**

1. $url : string

    URL de redirecionamento (init_point ou sandbox_init_point).

2. $titulo : string = "Finalizar"

    Título do produto/serviço.

3. $content : string = ""

    Descrição do produto.

4. $valor : string = ""

    Valor formatado do produto (ex: "R$ 49,90").

**Return values**

1. string

HTML formatado.

---

### bmpago_generate_date()

Gera uma data formatada no padrão ISO 8601 exigido pelo Mercado Pago.

`bmpago_generate_date([int $minutesToAdd = 30 ]) : string`

Útil para definir prazos de expiração (Pix).

**Parameters**

1. $minutesToAdd : int = 30

    Minutos a serem adicionados à data atual. Padrão: 30.

**Return values**
1. string

   Data formatada ex: 2026-02-28T10:30:00.000-03:00


---


###    bmpago_get_payment()

Busca detalhes de um pagamento específico pelo ID.

`bmpago_get_payment(string $mp_token, string $payment_id) : array<string|int, mixed>|null`

**Parameters**

1. $mp_token : string

    Token de acesso.
2. $payment_id : string

    ID do pagamento a ser consultado.

**Return values**

1. array<string|int, mixed>|null —

Resposta da API com os detalhes do pagamento.

---

### bmpago_pix_create()

Cria um pagamento Pix na API do Mercado Pago.

`bmpago_pix_create(string $mp_token, array<string|int, mixed> $data) : array<string|int, mixed>|null
Parameters`

1. $mp_token : string

    Token de acesso (Bearer Token).

2. $data : array<string|int, mixed>

    Estrutura de dados do pagamento conforme API do Mercado Pago.

**Return values**

1. array<string|int, mixed>|null —

    Resposta da API decodificada.



---


### bmpago_pix_simple()

Cria um pagamento Pix de forma simplificada.

`bmpago_pix_simple(string $mp_token, float $valor, string $email, string $cpf, string $notification_url, string $external_ref[, int $expiration = 30 ][, string $description = "" ][, string $first_name = "" ][, string $last_name = "" ]) : array<string|int, mixed>|null`

**Parameters**

1. $mp_token : string

    Token de acesso.

2. $valor : float

    Valor do pagamento.

3. $email : string

    E-mail do comprador.

4. $cpf : string

    CPF do comprador.

5. $notification_url : string

    URL para receber o webhook de notificação.

6. $external_ref : string

    Referência externa do seu sistema (ID do Pedido).

7. $expiration : int = 30

    Tempo de expiração em minutos. Padrão: 30.

8. $description : string = ""

    Descrição do pagamento.

9. $first_name : string = ""

    Nome do comprador.

10. $last_name : string = ""

    Sobrenome do comprador.


**Return values**

1. `array<string|int, mixed>|null`

    Resposta da API decodificada.

---
###  bmpago_pix_ui()

Gera uma string HTML com a interface do Pix (QR Code, Copia e Cola e Contador).

`bmpago_pix_ui(array<string|int, mixed> $response, string $titulo) : string`

**Parameters**

1. $response : array<string|int, mixed>

    Resposta da API do Mercado Pago (com status pendente).

2. $titulo : string

    Título do produto/pedido.

** Return values**

1. string

    HTML formatado da interface Pix.



---
### bmpago_request()

Função base para realizar requisições HTTP (cURL) para a API do Mercado Pago.

`bmpago_request(string $mp_token, string $method, string $url[, array<string|int, mixed>|null $data = null ]) : array<string|int, mixed>|null`

**Parameters**

1. $mp_token : string

    Token de acesso (Bearer Token).

2. $method : string

    Método HTTP (GET, POST, PUT, DELETE).

3. $url : string

    URL do endpoint da API.

4. $data : array<string|int, mixed>|null = null

    Dados para enviar no corpo da requisição (JSON).

**Return values**

1. array<string|int, mixed>|null

    Resposta da API decodificada como array ou array de erro.


---

### bmpago_webhook_confirm_payment()

Captura a notificação do Mercado Pago (Webhook) e valida se o pagamento foi aprovado.

`bmpago_webhook_confirm_payment(string $mp_token) : array<string|int, mixed>|false`

Esta função lê a notificação, obtém o ID do pagamento e faz uma consulta direta à API do Mercado Pago para confirmar o status oficial, garantindo segurança.

**Parameters**

1. $mp_token : string

    Token de acesso (Bearer Token).

**Return values**

1. array<string|int, mixed>|false

    Retorna os dados do pagamento (array) se aprovado, ou false se inválido/rejeitado.




### 📜 LicençaDistribuído sob a licença MIT. 

### Sinta-se à vontade para usar, modificar e distribuir.
