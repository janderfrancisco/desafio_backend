
# API

Api desenvolvida utilizando PHP e Laravel Framework.

--- 
### Documentação da api
[https://documenter.getpostman.com/view/928811/2s9XxzuCYN](https://documenter.getpostman.com/view/928811/2s9XxzuCYN)



### Passo a passo para subir a aplicação
Clone este repositório


acesse a pasta do projeto
 
```sh
cd desafio_backend/
```

Crie o Arquivo .env baseado no arquivo env.example
```sh
cp .env.example .env
```


Atualize as variáveis de ambiente do arquivo .env que está na raiz do projeto
```dosini
APP_NAME="API"
APP_URL=http://localhost:8989

DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=api
DB_USERNAME=root
DB_PASSWORD=root

CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379
```


Suba os containers do projeto
```sh
docker-compose up -d
```


Acessar o container
```sh
docker-compose exec app bash
```


Instalar as dependências do projeto
```sh
composer install
```


Gerar a key do projeto Laravel
```sh
php artisan key:generate
```


Acessar o projeto
[http://localhost:8989](http://localhost:8989)

Apartir daqui a api está rodando nos container

--- 


### Passo a passo para testar a aplicação

Para executar os testes da aplicação, os containers devem está rodando 

execute o comando abaixo para acessar o bash do container app
```sh
docker-compose exec app bash
```
Dentro do container utilize o comando:
```sh
php artisan test
```
Devera aparece algo semelhante a isso:

<img src="https://github.com/janderfrancisco/desafio_backend/blob/main/public/assets/tests.png">

 
--- 
### Proposta de Estrutura da aplicação 

<img src="https://github.com/janderfrancisco/desafio_backend/blob/main/public/assets/estrutura.jpg">
 
