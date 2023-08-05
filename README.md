
# API

Api desenvolvida utilizando PHP e Laravel Framework com Laravel Octane.
O motivo da utilização do Octane é que ele adiciona a aplicação em memória após o primeiro uso, fazendo assim com as próximas requisições sejam mais rápidas.


--- 
### Documentação da api
[https://documenter.getpostman.com/view/928811/2s9XxvTFE9](https://documenter.getpostman.com/view/928811/2s9XxvTFE9)



### Passo a passo para subir a aplicação
Clone este repositório


acesse a pasta do projeto
 
```sh
cd api_test/
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
DB_DATABASE=laravel
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

<img src="https://raw.githubusercontent.com/janderfrancisco/api_test/main/public/assets/test.png">



--- 
### Teste de Carga e LoadBalancer

Como foi descrito no desafio, a aplicação irá receber uma elevada quantidade de acessos em um determinado período. após o lançamento de um CD.

Para atender essa demanda foi criado um load balancer com um lauch templade de uma máquina ec2 com a imagem da aplicação já rodando. 
O loadbalance pode escalar até 4 máquinas se o uso de CPU passar de 70%, conforme imagem abaixo:

<img src="https://raw.githubusercontent.com/janderfrancisco/api_test/main/public/assets/autoscaling.png">

Através do Vegeta [https://github.com/tsenart/vegeta](https://github.com/tsenart/vegeta)

Para executar o testar, adicione 10% a mais de acessos que seria esperado

```
echo "GET http:///load-balancer-app-1821911745.us-east-2.elb.amazonaws.com/" | vegeta attack -durantion=60s -rate=3300
```
Esse loadbalance não está no disponivel mais, por questão de custo. Fiz os testes e o apaguei.



--- 
### Estrutura da aplicação

<img src="https://raw.githubusercontent.com/janderfrancisco/api_test/main/public/assets/estrutura.png">


Outra opção é utilizando Kubernetes 


