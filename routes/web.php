<?php
// Cargar clases

use App\Http\Middleware\ApiAuthMiddleware;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Metodos HTTP
|--------------------------------------------------------------------------
|
| * GET Obtener datos y recursos
| * POST Guardar datos o recursos o hacer logica desde un formulario
| * PUT  Actualizar datos o recursos
| * DELETE Eliminar datos o recursos
|
*/


//pruebas controladores
Route::get('/usuario/pruebas', 'UserController@pruebas');
Route::get('/post/pruebas', 'PostController@pruebas');
Route::get('/categoria/pruebas', 'CategoryController@pruebas');

    // Rutas del controlador de usuario
    Route::post('/api/user/register','UserController@register');
    Route::post('/api/user/login','UserController@login');
    Route::put('/api/user/update','UserController@update');
    Route::post('/api/user/upload','UserController@upload')->middleware(ApiAuthMiddleware::class);
    Route::get('/api/user/avatar/{filename}', 'UserController@getImage');