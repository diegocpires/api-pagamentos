# API PAGAMENTOS

## Tecnologias utilizadas
Para o desenvolvimento desse teste foi utilizado o micro-framework Lumen.

O ambiente de desenvolvimento conta com 4 containeres, sendo eles:
+ NGINX
+ PHP
+ Percona
+ RabbitMQ

## Instalação
Para realizar a instalação do projeto deve ser realizado os seguintes passos:

1 - Executar o comando `bin/install`
 
2 - Só isso :-)

## Funcionamento
O script de instalação já incluirá no arquivo hosts a url da API, que é http://api.pagamentos

Para iniciar o ambiente local, executar o comando `bin/dev start`

Para encerrar o ambiente local, executar o comando `bin/dev stop`

Para acessar o container do php, executar o comando `bin/php`
___ 
#### Cadastro de Clientes (Pessoas Físicas e Lojas)
Foi criado um seeder para preenchimento aleatório da tabela de customers (Físicas e Lojas). Caso queira utilizá-lo: 

`php artisan db:seed --class=CustomerTableSeeder`

Existem 4 endpoints para o CRUD de clientes.

```
GET     /customers
POST    /customers
PUT     /customers/{idCustomer}
DELETE  /customers/{idCustomer}
```

## Postman
No repositório existe uma coleção do postman com as requests disponíveis no projeto.

## RabbitMQ
Para acessar o painel de administração do RabbitMQ é necessário acessar no navegador a url `http://localhost:15672`

As credenciais são `user: guest` `password: guest`
## Qualidade
No github actions está rodando PHPStan, PHPMD, PHPCS e PHPUnit.

É possível realizar os testes localmente, para isso existe um script dentro do diretório bin.

`bin/quality`
____
##### PHP Unit
Dentro do container PHP, executar o comando `./vendor/bin/phpunit` na raiz do projeto.

## Considerações
Não foi realizada nenhuma forma de autenticação.

Foi desenvolvido somente a parte de envio de notificações que falharam para a fila do RabbitMQ. Não foi desenvolvido 
o consumer.

Não foi criado o swagger da API, visto que já inclui no repositório a coleção do Postman. Mas isso poderia facilmente 
ser adicionado ao projeto.

## Proposta de Melhorias
Como uma futura melhoria nessa arquitetura, eu quebraria a aplicação em dois microserviços.

Um para efetuar a transação e outro responsável por receber as requisições.

Dessa forma poderíamos colocar um serviço de gRPC no meio, cuidando da comunicação entre os dois microserviços.

Não desenvolvi o teste dessa forma por conta de tempo disponível. Estou na semana de entrega de projeto (migração de uma plataforma de ecommerce do Magento 1 para o Magento 2) e o tempo está bem escasso.