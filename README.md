## Production
admin.weareadn.com
server: 164.92.251.87
db: larave8_admin

## Previewpc
preview-admin.weareadn.com
server: 164.92.251.87
db: preview_admin_laravel8

## INSTALACIÓN PROYECTO:
- composer create-project laravel/laravel --prefer-dist laravel8_admin
- cd laravel8_admin
- composer require laravel/ui
- Instalando sistema de login y registro: php artisan ui bootstrap --auth
- npm install && npm run dev
- Instalando Livewire: composer require livewire/livewire
- Instalación Spatie (Roles y permisos): https://spatie.be/docs/laravel-permission/v4/introduction
	- composer require spatie/laravel-permission
	- php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
	- Incluir el role de Super Admin en app/Providers/AuthServiceProvider.php, en el método boot():
		Gate::before(function ($user, $ability) {
            return $user->hasRole('Super Admin') ? true : null;
        });	
        
- Migración inicial de tablas: php artisan migrate

- Migración de tabla específica:
php artisan migrate --path=/database/migrations/table_name.php


- Descomentar la línea protected $namespace = 'App\\Http\\Controllers'; en app\Providers\RouteServiceProvider.php para utilizar el sistema de enrutamiento antiguo.

## CONSTANTS:
En el archivo config/constants.php se definen constantes personalizadas.

## LIBRARIES:
- PDF Library: https://github.com/barryvdh/laravel-dompdf
	- composer require barryvdh/laravel-dompdf
	- php artisan vendor:publish --provider="Barryvdh\DomPDF\ServiceProvider"
	- ¡Atención! no utilizar unserialize en las vistas de .pdf. Deserializar previamente en el método del controller.

- Browser Agent: https://github.com/jenssegers/agent
	- composer require jenssegers/agent
	- Incluir Provider en config/app.php: Jenssegers\Agent\AgentServiceProvider::class,
	- Incluir Alias en config/app.php: 'Agent' => Jenssegers\Agent\Facades\Agent::class,

- DataTables:
	- Para usar junto a Bootstrap: npm i datatables --save
	- Instalar Yajra Laravel Datatables: https://github.com/yajra/laravel-datatables
		- composer require yajra/laravel-datatables-oracle:"~9.0"

- Extender Auth y Session a API Controllers:
	En config/auth.php:
	'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],

        'api' => [
            'driver' => ['token', 'session'],
            'provider' => 'users',
            'hash' => false,
        ],
    ],

    En app/Http/Kernel.php:
    'api' => [
        \App\Http\Middleware\EncryptCookies::class,
        'throttle:api',
        \Illuminate\Routing\Middleware\SubstituteBindings::class,
        \Illuminate\Session\Middleware\StartSession::class,
        'throttle:60,1',
    ],


- EXCEL Library: https://laravel-excel.com/
	- composer require maatwebsite/excel
	- En php.ini se actualiza el valor de memory_limit a -1 para poder utilizar esta librería.

- Sweet Alert 2:
	- npm install sweetalert2
	- Incluir en el archivo resources/js/bootstrap.js: window.swal = require('sweetalert2');
	
- Notificaciones por DataBase:
	- php artisan notifications:table
	- php artisan migrate

- Cron Jobs:
	- php artisan schedule:run -> ejecuta las tareas programadas en ese mismo momento.
	- php artisan schedule:work -> ejecuta las tareas programadas en el momento indicado en el Kernel.

	SERVER CRONS. En la consola del Servidor de Producción ejecutar:
	crontab -e

	Agregar :
	*/1 * * * * php /var/www/directorio_proyecto/artisan schedule:run >> /dev/null 2>&1
	Cada min ejecuta el schedule. 

- Laravolt Avatars: librería para la generación automática de avatar de usuario.
	- https://github.com/laravolt/avatar
	- composer require laravolt/avatar
	- php artisan vendor:publish --provider="Laravolt\Avatar\ServiceProvider"  Genera un archivo de configuración en config/laravolt/avatar.php

- Pusher App:
	- https://pusher.com/
	- email cuenta: luisbgall@gmail.com
	- pwd cuenta: l53SbG1lL
	- API Key: J_oS3RvWzBZGmVzGLLaqsWHJbSTaQ_aOxwAW5qG-3H4
	- app name: laravel8_admin
	- environments: 
		    laravel8_admin-development
		    laravel8_admin-staging
		    laravel8_admin-production
	- app keys:
			app_id = "1324521"
			key = "7b913367afbfdcd7c0fe"
			secret = "b91f814b10b72d3b52c4"
			cluster = "eu"
	- instalar pusher: composer require pusher/pusher-php-server
	- En .env modificar valor de la variable BROADCAST_DRIVER = pusher
	- Incluir cdn .js en el head: "https://js.pusher.com/7.0/pusher.min.js"

- Factura Electrónica E-Factura:  PARA INVESTIGAR
	- https://github.com/martinlaregina/Facturae-PHP

- CORS:
	- https://github.com/fruitcake/laravel-cors
	- instalar: composer require fruitcake/laravel-cors

- Emails:
	- SMTP ADN: erp@weareadn.com      
	- pwd en gmail: ErpAdn2024*
	- mail host: smtp.gmail.com
	- pwd email: bwducgbggazgiolz
	- pwd app (password que utiliza la app para el SMTP): rukjilbsjmylwxzj
	- tlf. doble verificación: 600 578 602 (Lluis Benito)

## HELPERS:
Desde la versión 5.8 desaparece por defecto el fichero app/Helpers/helpers.php, por lo que hay que crearlo manualmente incorporándolo en el fichero composer.json:

	"autoload": {
        "files": [
            "app/Helpers/helpers.php"
        ]
    },

A continuación se ejecuta en consola: composer dump-autoload


## IDIOMAS:
Se definen en el método languages, en App/Helpers/helpers.php. Sigue las indicaciones descritas en el método (2.1. Idiomas).

## NOTAS:
- En config/database.php se modifica en el objeto 'mysql' el parámetro 'strict' de true a false. De este modo logramos que funcionen las sentencias groupBy en las consultas mysql.

## QUERIES:
- Truncate table evitando foreign key:
SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE table;
SET FOREIGN_KEY_CHECKS = 1;

- Truncate presupuestos - pedidos- facturas:
SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE invoices;
TRUNCATE invoice_orders;
TRUNCATE invoice_payments;
TRUNCATE orders;
TRUNCATE items;
TRUNCATE deliveries;
TRUNCATE delivery_orders;
TRUNCATE order_previsions;
SET FOREIGN_KEY_CHECKS = 1;

- Contar nº de tablas de la BD:
SELECT COUNT(*) from information_schema.tables where TABLE_TYPE = 'BASE TABLE' and table_schema = 'admin8_db';


- Usuarios ADN:
select u.id, u.name, u.surname, u.isAdmin, u.status from users u
inner join user_companies uc
on u.id = uc.user_id
where uc.company_id in (3,4,8,202,227,275)
group by u.id

- Establecer líneas de pedido como facturadas:
update items i
inner join orders o
on i.order_id = o.id
set i.invoiced = true
where o.invoiced = true;

- Seleccionar registros repetidos:
SELECT field, count(*) FROM table
    GROUP BY field
    HAVING COUNT(*)>1;

- Eliminar registros repetidos:
DELETE FROM table
WHERE company_id = xxx
and id NOT IN (
    SELECT MAX(id)
    FROM table
    WHERE company_id = xxx
    GROUP BY field
    HAVING COUNT(*) > 1
);

- Listado de configuración datos de pago por clientes:
SELECT companies.name, customer_providers.pay_after_days, customer_providers.payday
FROM customer_providers
INNER JOIN companies 
ON customer_providers.customer_id = companies.id
WHERE customer_providers.provider_id = 4;

- Facturas por proveedor en intervalo de fechas:
select c.name AS provider, cc.name AS customer, o.order_num, i.invoice_num, i.fiscal_date, i.amount, i.amount_with_iva
from invoices i
inner join orders o
on i.order_id = o.id
inner join companies c
on o.provider_id = c.id
inner join companies cc
on o.customer_id = cc.id
where o.provider_id in (227,8)
and i.fiscal_date >= '2023-07-01'
and i.fiscal_date <= '2023-07-31'

- Líneas de pedidos por cliente:
select o.order_num, o.created_at, p.name, i.concept, CONCAT(u.name, ' ', u.surname) AS manager, i.qty, i.price, i.discount, 
(i.qty * i.price) - COALESCE((i.qty * i.price * i.discount) / 100, 0) AS amount
from items i
inner join products p
on i.product_id = p.id
inner join orders o
on i.order_id = o.id
left join users u
on o.customer_manager_id = u.id
where o.customer_id = 2
and o.deleted_at is null
and o.provider_id = 4
and o.is_order = 1
and o.is_budget < 1;

- Comparativa importes pedidos y facturas:
SELECT
    o.order_num,
    c.name AS company,
    CONCAT(u.name, ' ', u.surname) as manager,
    FORMAT(
    	(SELECT SUM(amount) FROM invoices i WHERE i.order_id = o.id AND i.deleted_at IS NULL), 2 
    ) AS importe_factura,
    FORMAT(
        (SELECT SUM((it.qty * it.price) - COALESCE((it.qty * it.price * it.discount) / 100, 0)) 
         FROM items it 
         WHERE it.order_id = o.id AND it.deleted_at IS NULL), 2
    ) AS importe_pedido
FROM
    orders o
    INNER JOIN companies c ON o.customer_id = c.id
    LEFT JOIN users u ON o.provider_manager_id = u.id
WHERE
    o.provider_id = 4
    AND o.deleted_at IS NULL
    AND o.is_order = 1
    AND YEAR(o.created_at) = 2023
    AND o.invoiced = 1
ORDER BY
    o.order_num ASC;

- Consulta pedidos por cliente por intervalo de tiempo:
SELECT c.name as company, o.order_num, o.name as title, o.created_at
FROM orders o
INNER JOIN companies c
ON o.customer_id = c.id
WHERE o.is_order = 1
AND o.provider_id = 4
AND o.deleted_at IS NULL
AND o.created_at BETWEEN '2021-01-01' AND '2023-12-31'
ORDER BY o.created_at ASC

- Items de pedidos:
SELECT 
c.name AS provider, o.provider_id,
c1.name AS customer, o.customer_id,  
c2.name AS final_customer, o.final_customer_id, 
o.is_budget, o.is_order, o.id AS order_id, o.order_num, o.name AS title, o.budget_num, 
bf.name AS form, o.form_id,
o.accepted_date,
o.description,
o.delivery_date,
o.order_status,
CONCAT(u.name, ' ', u.surname) AS customer_manager,
cc.name AS customer_cost_center,
CONCAT(u1.name, ' ', u1.surname) AS provider_manager,
cc1.name AS provider_cost_center,
CONCAT(u2.name, ' ', u2.surname) AS commercial,
CONCAT(u3.name, ' ', u3.surname) AS final_customer_manager,
o.bag_amount,
o.invoiced,
o.created_at,
o.updated_at,
i.id AS item_id,
p.name AS product,
i.product_id,
pp.name AS producto_contable,
bfi.name AS budget_form_block,
bfi1.name AS budget_form_item,
i.concept,
i.qty,
i.price,
i.item_no_price,
i.iva,
i.equivalence_surcharge,
i.irpf,
i.discount,
i.calculated_by_percent,
round( COALESCE(i.qty * i.price, 0) - COALESCE((i.qty * i.price * i.discount) / 100, 0), 2) AS amount,
i.days,
i.days_continue,
i.days_continue_discount,
cc2.name AS item_provider_cost_center
FROM items i
INNER JOIN orders o
ON i.order_id = o.id
INNER JOIN companies c
ON o.provider_id = c.id
INNER JOIN companies c1
ON o.customer_id = c1.id
LEFT JOIN companies c2
ON o.final_customer_id = c2.id
LEFT JOIN budget_forms bf
ON o.form_id = bf.id
LEFT JOIN users u
ON o.customer_manager_id = u.id
LEFT JOIN users u1
ON o.provider_manager_id = u1.id
LEFT JOIN users u2
ON o.commercial_id = u2.id
LEFT JOIN cost_centers cc
ON o.customer_cost_center_id = cc.id
LEFT JOIN cost_centers cc1
ON o.provider_cost_center_id = cc1.id
LEFT JOIN users u3
ON o.final_customer_manager_id = u3.id
LEFT JOIN products p
ON i.product_id = p.id
LEFT JOIN purchase_products pp
ON i.purchase_product_id = pp.id
LEFT JOIN budget_form_items bfi
ON i.budget_form_block_id = bfi.id
LEFT JOIN budget_form_items bfi1
ON i.budget_form_block_id = bfi1.id
LEFT JOIN cost_centers cc2
ON i.provider_cost_center_id = cc2.id
WHERE o.provider_id IN (4,8,202,227,275)
AND i.deleted_at IS NULL
ORDER BY o.provider_id ASC

- Listado clientes de ADN
SELECT c.name AS customer, c1.name AS provider FROM customer_providers cp
JOIN companies c
ON cp.customer_id = c.id
JOIN companies c1
ON cp.provider_id = c1.id
WHERE cp.provider_id IN (4,8,202,227,275)
AND cp.customer_id NOT IN (4,8,202,227,275)
ORDER BY c.name ASC
   

### Instalación de Sentry

- Puedes Consultar la configuración. [Ir A sentry](https://docs.sentry.io/platforms/php/guides/laravel/)


## GUÍA RÁPIDA DE CONFIGURACIÓN INICIAL: se enumeran los pasos a seguir para una correcta configuración de la aplicación en su puesta en marcha.
1. Acceder a la aplicación con las credenciales proporcionadas.
2. Menú: Empresas / Empresas -> Editar empresa. Completar los datos de la empresa. Es necesario introducir un NIF válido.
3. Desde la pantalla donde nos encontramos editando los datos de la empresa, completar la información de la empresa relativa a Áreas de negocio, Centros de coste y principalmente Centro de trabajo. En este último concepto se guardará la dirección, población,... de la sede o sedes de la empresa.
4. Menú: Configuración / Monedas. Determinar las monedas activas que utilizará la aplicación. En caso de no marcarse monedas, el valor por defecto será el €.
5. Menú: Configuración / Módulos. Determinar los módulos activos en la aplicación. ¡Ojo! Posteriormente debemos configurar los módulos que deseamos para nuestra/s empresas, a partir de los módulos que se encuentren activos.
6. Menú: Mi cuenta / Módulos. Aquí seleccionaremos los módulos para la empresa que se encuentre activa en ese momento.
7. Menú: Empresas / Configuración. Establecer los valores predeterminados para diferentes funcionalidades de la empresa habilitada: moneda, gestiones, colores corporativos, iva general de la empresa, ip's de acceso a la aplicación,...
8. Menú: Configuración / Contenidos. Simplemente acceder para cargar los contenidos guardados en .json. 
9. Menú: Configuración / Roles. Administrar Roles y Permisos. 

## IMPORTACIÓN DESDE ERP LARAVEL6_ADMIN
- Ir a url: http://laravel8_admin.test/admin/import_db
- Ejecutar acción Importar.











<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains over 1500 video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the Laravel [Patreon page](https://patreon.com/taylorotwell).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Cubet Techno Labs](https://cubettech.com)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[Many](https://www.many.co.uk)**
- **[Webdock, Fast VPS Hosting](https://www.webdock.io/en)**
- **[DevSquad](https://devsquad.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[OP.GG](https://op.gg)**
- **[CMS Max](https://www.cmsmax.com/)**
- **[WebReinvent](https://webreinvent.com/?utm_source=laravel&utm_medium=github&utm_campaign=patreon-sponsors)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
