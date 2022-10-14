//Detectando si existe conexión a internet:
self.addEventListener('fetch', event => {
	const offLineResp = new Response(`

		<!DOCTYPE html>
		<html>
		<head>
		    <meta charset="utf-8">
		    <meta http-equiv="X-UA-Compatible" content="IE=edge">
		    <title>ADN Admin Systems</title>
		    <link rel="stylesheet" href="">
		</head>
		<body>
		 	ADN Admin Systems
		 	<br><br>
			Necesitas una conexión a Internet para utilizar esta aplicación.   
		</body>
		</html>

	`, {
		headers: {
			'Content-Type':'text/html'
		}
	});

	const resp = fetch(event.request)
		.catch( () => offLineResp);

	event.respondWith(resp);
});

const CACHE_STATIC = 'static-v1';
const CACHE_DYNAMIC = 'dynamic-v1';
const CACHE_INMUTABLE = 'inmutable-v1';

const APP_SHELL = [
	'/',
	'index.php',
	'/css/app.css',
	'/css/web.css',
	'/js/custom-datatables.js',
	'/js/pikeadmin.js',
	'/js/app.js',
	'/js/custom.js'
];

const APP_SHELL_INMUTABLE = [
	'https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.1/css/bootstrap.css',
	'https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css',
	'https://cdn.datatables.net/buttons/1.5.2/css/buttons.dataTables.min.css',
	'/plugins/datetimepicker/css/daterangepicker.css',
	'/plugins/select2/css/select2.min.css',
	'/js/jquery-1.10.2.min.js',
	'/js/ui/jquery-ui.min.js',
	'/js/popper.min.js',
	'/js/bootstrap.min.js',
	'/js/detect.js',
	'/js/fastclick.js',
	'/js/jquery.blockUI.js',
	'/js/jquery.nicescroll.js',
	'/js/jquery.scrollTo.min.js',
	'/js/sweetalert.min.js',
	'/plugins/datetimepicker/js/moment.min.js',
	'/plugins/datetimepicker/js/daterangepicker.js',
	'/plugins/datatables/jquery.dataTables.min.js',
	'/plugins/datatables/dataTables.bootstrap4.min.js',
	'https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js',
	'https://cdn.datatables.net/buttons/1.5.2/js/buttons.flash.min.js',
	'https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js',
	'https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js',
	'https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js',
	'https://cdn.datatables.net/buttons/1.5.2/js/buttons.html5.min.js',
	'https://cdn.datatables.net/buttons/1.5.2/js/buttons.print.min.js',
	'/plugins/select2/js/select2.min.js'
];

//Limpiar caché:
function cleanCache(cacheName, numItems){
	caches.open(cacheName)
	.then(cache => {
		cache.keys()
		.then(keys => {
			if(keys.length > numItems){
				cache.delete(keys[0])
					.then(cleanCache(cacheName, numItems))
			}
		});
	});
}

//Instalando:
self.addEventListener('install', event => {
	//Grabamos caché estática:
	const cacheStatic = caches.open(CACHE_STATIC)
		.then( cache => {
			return cache.addAll(APP_SHELL);
		});

	//Grabamos caché inmutable:
	const cacheInmutable = caches.open(CACHE_INMUTABLE)
		.then( cache => {
			return cache.addAll(APP_SHELL_INMUTABLE)
		});

	event.waitUntil(Promise.all([cacheStatic, cacheInmutable]));
});

//Eliminando caché inservible:
self.addEventListener('activate', event => {
	const activacion = caches.keys().then(keys => {
		keys.forEach(key => {
			if(key !== CACHE_STATIC && key.includes('static')){
				return caches.delete(key);
			}
		})	
	});

	event.waitUntil(activacion);
});

//Estrategia de caché:
self.addEventListener('fetch', event => {

}); 