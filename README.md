
# API de simulação de contas bancárias

Aplicação Laravel com MySQL conteinerizada em Docker

Esta API permite que o usuário:
  
- consulte saldo;

	- através do endpoint: `/api/contas/{number}/saldo`

- realize um saque;

	- através do endpoint: `/api/contas/{number}/sacar/{value}`

- realize um depósito;

	- através do endpoint: `/api/contas/{number}/depositar/{value}`

 Há também um endpoint adicional para consultar as contas cadastradas no banco, de modo que o usuário possa escolher uma conta para usar nos endpoints anteriormente listados, a listagem de todas as contas cadastradas no sistema pode ser feita através do endpoint: `/api/contas`

## Instalação
**Atenção:** Se você for optar por ter um banco de dados local rodando em sua máquina, recomendo que troque a senha padrão que está no arquivo docker-compose.yml no parâmetro MYSQL_ROOT_PASSWORD, mas você pode optar por usar um banco remoto e ignorar este aviso.

 - Após clonar o repositório em seu computador, com o terminal, navegue até a raiz do repositório e execute o comando `docker compose up --build -d` para que os containers sejam criados e iniciados. 
 - Execute o comando `docker-compose exec app composer install` para  baixar e instalar todas as dependências necessárias

### Configuração base
Crie um arquivo .env, você pode criar manualmente ou utilizar o comando `cp .env.example .env` para gerar um arquivo .env utilizando o .env.example como base.

### Configurando o Laravel

 - Execute o comando `docker-compose exec app php artisan key:generate   `
 - -Execute o comando `docker-compose exec app php artisan config:cache`

### Configurando banco de dados local (opcional)
Esta etapa é opcional, você pode optar por usar um banco de dados remoto previamente configurado, mas caso tenha optado por seguir com o banco de dados local, siga os seguintes passos:

 - Com o terminal na raiz do repositório, entre no terminal do container 'db' utilizando o comando `docker-compose exec db bash`
 - logue no MySQL com o comando `mysql -u root -p` e digitando a senha definida no arquivo docker-compose.yml
 - Você precisará criar um usuário para que o Laravel tenha acesso ao banco de dados, isso pode ser feito com o comando `GRANT ALL ON laravel.* TO 'laraveluser'@'%' IDENTIFIED BY 'your_laravel_db_password';`
	 - É recomendado usar outro nome e senha neste comando
	 - O usuário e senha criados com este comando devem ser colocados no arquivo .env em DB_USERNAME e DB_PASSWORD respectivamente
 - depois rode o comando `FLUSH PRIVILEGES;` para que atualizar o MySQL com os privilégios setados anteriormente
 - Agora você pode sair do MySQL com o comando `EXIT;`
 - E sair do terminal do container com o comando `exit`
 - Execute o comando `docker-compose exec app php artisan migrate` para que a tabela de contas seja criada em seu banco
 - Popule a tabela com o comando  `docker-compose exec app php artisan db:seed`

## Testes 
Para rodar os testes unitários presente no repositório, utilize o comando `docker-compose exec app php  artisan  test`
