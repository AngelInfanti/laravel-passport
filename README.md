# Prueba de servicios en Laravel con Passport
Se realizo la prueba requerida para completar la solicitud como desarrollador, la prueba consistía en desarrollar 5 servicios API Rest, los cuales
fueron desarrollados con el framework Laravel añadiéndole el módulo de Pasport que proporciona el protocolo OAuth2 para la generación de tokens.

### Estos son los servicios desarrollados:
##### Users
- Login(http://127.0.0.1:8000/api/user/login) POST
- Register(http://127.0.0.1:8000/api/user/register) POST


##### Wallets
- deposit(http://127.0.0.1:8000/api/user/deposit) POST
- assets(http://127.0.0.1:8000/api/user/assets) GET

##### Payments
- pay(http://127.0.0.1:8000/api/user/pay) POST
- confirmPay(http://127.0.0.1:8000/api/user/confirmPay) POST


Para mas informacion en cuanto a los servicios se incluira el archivo services para hacer
las pruebas desde postman.

## Para comenzar:
### Paso 1 Cree el archivo .env
En la raiz del proyecto siguiendo el archivo .env.example cree su archivo de configuracion y conecte la base de datos que utilizara en el proyecto.

### Paso 2 Instale los paquetes
```bash
composer install
```
### Paso 3 Crees las claves de la aplicacion
```bash
php artisan key:generate
```
### Paso 4 Importe la base de datos
Cree una base de datos es su configuracion local con el mismo nombre y datos que indica el archivo .env
```bash
php artisan migrate
```

### Paso 5 Cree los clientes de Passport
Cree una base de datos es su configuracion local con el mismo nombre y datos que indica el archivo .env
```bash
php artisan passport:install
```

### Paso 6 Ejecute el proyecto
```bash
php artisan serve
```