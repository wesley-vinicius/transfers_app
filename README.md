

<br />
<p align="center">
  <h2 align="center">Sistema para realizar tranferencia </h2> 
</p>



<!-- TABLE OF CONTENTS -->


<!-- ABOUT THE PROJECT -->
## Requisitos:

Para ambos tipos de usuário, precisamos do Nome Completo, CPF, e-mail e Senha. CPF/CNPJ e e-mails devem ser únicos no sistema. Sendo assim, seu sistema deve permitir apenas um cadastro com o mesmo CPF ou endereço de e-mail.

Usuários podem enviar dinheiro (efetuar transferência) para lojistas e entre usuários.

Lojistas só recebem transferências, não enviam dinheiro para ninguém.

Validar se o usuário tem saldo antes da transferência.

Antes de finalizar a transferência, deve-se consultar um serviço autorizador externo, use este mock para simular (https://run.mocky.io/v3/8fafdd68-a090-496f-8c9a-3442cf30dae6).

A operação de transferência deve ser uma transação (ou seja, revertida em qualquer caso de inconsistência) e o dinheiro deve voltar para a carteira do usuário que envia.

No recebimento de pagamento, o usuário ou lojista precisa receber notificação (envio de email, sms) enviada por um serviço de terceiro e eventualmente este serviço pode estar indisponível/instável. Use este mock para simular o envio (http://o4d9z.mocklab.io/notify).


## Instalação

1. Clone do repositorio
   ```sh
   git clone https://github.com/wesley-vinicius/transfers_app.git
   ```
2. Criar aquivo .env
   ```sh
   cp .env.example .env
   ```
3. Setup inicial
   ### Ambiente docker
   ```sh
   docker-compose up -d
   ```
     ```sh
   docker exec -it transfers_app_php composer update
   ```
    ```sh
   docker exec -it transfers_app_php php artisan key:generate
   ```
    ```sh
   docker exec -it transfers_app_php php artisan migrate
   ```
   ```sh
   docker exec -it transfers_app_php php artisan db:seed
   ```
   ```sh
   docker exec -it transfers_app_php chmod -R 777 storage/
   ```
   ```sh
   docker exec -it transfers_app_php php artisan queue:work
   ```
   ```sh
   docker exec -it transfers_app_php php artisan test
   ```
   
   ### Ambiente docker com Make
   ```sh
   make install
   ```
   ```sh
   make test
   ```
   
   ### Ambiente local
    ```sh
   composer update
   ```
   ```sh
   php artisan key:generate
   ```
   ```sh
   php artisan migrate
   ```
   ```sh
   php artisan db:seed
   ```
    ```sh
   sudo chmod -R 777 storage/
   ```
   ```sh
   php artisan serve
   ```
   ```sh
   php artisan queue:work
   ```
   ```sh
   php artisan test
   ```
  
  

<!-- USAGE EXAMPLES -->
## Uso

#### Criar Usuário 
POST api/user

```json
{
    "name": "teste",
    "email" : "teste@teste.com",
    "document": "58622125610",
    "user_type_id": 1,
    "password" : "password",
    "password_confirmation" : "password"
}
```

#### Realizar transferência
##### Payload

POST api/transaction

```json
{
    "value" : 100.00,
    "payer" : 1,
    "payee" : 2
}
```

