## Instalação

1 - Clonando o projeto
```bash
  git clone https://github.com/DiiOliiver/gpe-api.git
  cd gpe-api
```

2 - Levantar ambiente PHP com docker
```bash
  docker compose up -d
```

3 - Acessando e levantando o projeto
```bash
  docker compose exec app bash
  cd api

  composer install
  cp .env.example .env

  php artisan key:generate
```

4 - Configurando o .env

    Abra o arquivo .env em sua IDE e adicione o usuário e senha do seu banco de dados MySQL
    DB_USERNAME=username
    DB_PASSWORD=password
    
    E altere SEEDER para true

5 - Gerando e populando as tabelas

```bash
  php artisan migrate --seed
```

6 - Acessos

    PhpMyAdmin: http://localhost:8080
    Endpoints do projeto: http://localhost:8000/api/v1
