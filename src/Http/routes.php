<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/


Route::get('cart/sesional', function() {
return Session::get('cart');
});


Route::bind('product', function ($slug) {
return DigitalsiteSaaS\Carrito\Product::where('slug', $slug)->first();
});



Route::get('cart/detail', [
'middleware' => 'auth',
'as' => 'tienda-detail',
'uses' => 'Sitedigitalweb\Carrito\Http\CartController@orderDetail'
]);


Route::get('cart/detailsin', [
'middleware' => 'web',
'as' => 'tienda-detailsin',
'uses' => 'Sitedigitalweb\Carrito\Http\CartController@orderDetail'
]);

Route::get('sd/productos-online', 'Sitedigitalweb\Carrito\Http\CategoryController@productos_online')->middleware('web');



Route::any('/session/datosdfed', 'DigitalsiteSaaS\Carrito\Http\CartController@datosusuario');



Route::get('{$id}', [
'middleware' => 'web',
'as' => 'tienda-virtual',
'uses' => 'DigitalsiteSaaS\Pagina\Http\WebController@paginas'
]);

Route::post('{$id}', [
'middleware' => 'web',
'as' => 'tienda-virtual',
'uses' => 'DigitalsiteSaaS\Pagina\Http\WebController@paginas'
]);

Route::get('product/detail/{slug}', [
'middleware' => 'web',
'as' => 'product-detail',
'uses' => 'DigitalsiteSaaS\Carrito\Http\StoreController@show'
]);

Route::get('cart/shows', [
'middleware' => 'web',
'as' => 'cart-show',
'uses' => 'Sitedigitalweb\Carrito\Http\CartController@show'
]);

Route::get('cart/adds/{id}', [
'middleware' => 'web',
'as' => 'cart-add',
'uses' => 'Sitedigitalweb\Carrito\Http\CartController@add'
]);

Route::get('cart/addprice/{priceman}', [
'middleware' => 'web',
'as' => 'cart-addprice',
'uses' => 'DigitalsiteSaaS\Carrito\Http\CartController@addprice'
]);

Route::get('cart/update/{producto}/{cantidad?}', [
'middleware' => 'web',
'as' => 'cart-update',
'uses' => 'DigitalsiteSaaS\Carrito\Http\CartController@update'
]);

Route::get('cart/delete/{id}', [
'middleware' => 'web',
'as' => 'cart-delete',
'uses' => 'DigitalsiteSaaS\Carrito\Http\CartController@delete'
]);
Route::get('cart/trash', [
'middleware' => 'web',
'as' => 'cart-trash',
'uses' => 'DigitalsiteSaaS\Carrito\Http\CartController@trash'
]);


Route::get('cart/responsedaff', [
'middleware' => 'web',
'as' => 'cart-response',
]);



Route::get('cart/responseserver', array('uses' => 'DigitalsiteSaaS\Carrito\Http\CartController@responseserver', 'middleware' => 'web'));


Route::post('cart/confirmacion', array('uses' => 'DigitalsiteSaaS\Carrito\Http\CartController@confirmacion', 'middleware' => 'web'));

Route::post('cart/responseda/', [
  'middleware' => 'web',
'as' => 'cart/responsess',
'uses' => 'DigitalsiteSaaS\Carrito\Http\CartController@response'
]);

Route::get('cart/responseda/', [
'middleware' => 'web',
'as' => 'cart/responsess',
'uses' => 'DigitalsiteSaaS\Carrito\Http\CartController@response'
]);

Route::group(['middleware' => ['auth','administrador']], function (){

Route::resource('sd/ecommerce', 'Sitedigitalweb\Carrito\Http\UserController');
Route::get('sd/categorias', 'Sitedigitalweb\Carrito\Http\CategoryController@show');
Route::get('sd/crear-categoria', 'Sitedigitalweb\Carrito\Http\CategoryController@createca');
Route::post('sd/crear-categoria', 'Sitedigitalweb\Carrito\Http\CategoryController@crearcategoria');
Route::get('sd/subcategorias/{id}', 'Sitedigitalweb\Carrito\Http\CategoryController@versubcategorias');
Route::get('sd/cupon', 'Sitedigitalweb\Carrito\Http\CategoryController@cupon');
Route::get('/sd/crear-subcategoria/{id}', function(){
 return View::make('carrito::admin.crear-subcategoria');
});
Route::post('sd/createcategoria', 'Sitedigitalweb\Carrito\Http\CategoryController@createcategoria');
Route::get('sd/productos/{id}', 'Sitedigitalweb\Carrito\Http\ProductoController@digitales');
Route::get('sd/crear-producto/{id}', 'Sitedigitalweb\Carrito\Http\ProductoController@crear');
Route::get('sd/ordenes', 'Sitedigitalweb\Carrito\Http\CategoryController@ordenes');
Route::get('sd/detalle-orden/{id}', 'Sitedigitalweb\Carrito\Http\CategoryController@detalle_orden');


Route::get('gestion/productos/crearproducto', 'Sitedigitalweb\Carrito\Http\ProductoController@show');

Route::get('gestion/carrito/autores', 'DigitalsiteSaaS\Carrito\Http\CategoryController@webautores');

Route::get('gestion/carrito/verparametro', 'DigitalsiteSaaS\Carrito\Http\CategoryController@verparametros');

Route::get('gestion/carrito/subcategorias/{id}', 'DigitalsiteSaaS\Carrito\Http\CategoryController@versubcategorias');





Route::get('/gestion/carrito/parametro', function(){
 
    return View::make('carrito::admin.parametro');
});






Route::post('gestion/carrito/editarcategoriaweb/{id}', 'DigitalsiteSaaS\Carrito\Http\CategoryController@editarcategoriaweb');
Route::post('gestion/carrito/editarcategoriawebproducto/{id}', 'DigitalsiteSaaS\Carrito\Http\CategoryController@editarcategoriawebproducto');
Route::get('gestion/carrito/configuracion', 'DigitalsiteSaaS\Carrito\Http\CategoryController@configuracion');


Route::get('gestion/carrito/terminos', 'DigitalsiteSaaS\Carrito\Http\CategoryController@terminos');
Route::post('gestion/carrito/parametros', 'DigitalsiteSaaS\Carrito\Http\CategoryController@parametros');
Route::post('gestion/carrito/createcategoriaproductos', 'DigitalsiteSaaS\Carrito\Http\CategoryController@createcategoriaproductos');

Route::post('gestion/carrito/creacionautor', 'DigitalsiteSaaS\Carrito\Http\CategoryController@creacionautor');
Route::get('gestion/carrito/editarcategoria/{id}', 'DigitalsiteSaaS\Carrito\Http\CategoryController@editarcategoria');
Route::get('gestion/carrito/editarcategoriaproducto/{id}', 'DigitalsiteSaaS\Carrito\Http\CategoryController@editarcategoriaproducto');
Route::resource('gestion/carrito/actualizar', 'DigitalsiteSaaS\Carrito\Http\CategoryController@actualizar');
Route::post('gestion/carrito/actualizarautor/{id}', 'DigitalsiteSaaS\Carrito\Http\CategoryController@actualizarautor');
Route::post('gestion/carrito/actualizarparametro/{id}', 'DigitalsiteSaaS\Carrito\Http\CategoryController@actualizarparametro');
Route::get('gestion/carrito/eliminar/{id}', 'DigitalsiteSaaS\Carrito\Http\CategoryController@eliminar');
Route::get('gestion/carrito/eliminarproducto/{id}', 'DigitalsiteSaaS\Carrito\Http\CategoryController@eliminarproducto');
Route::get('gestion/carrito/epayco', 'DigitalsiteSaaS\Carrito\Http\CategoryController@epayco');

Route::get('gestion/carrito/crear-autor', 'DigitalsiteSaaS\Carrito\Http\CategoryController@createautor');
Route::post('gestion/carrito/actuterminos', 'DigitalsiteSaaS\Carrito\Http\CategoryController@update');
Route::get('gestion/carrito/dashboard', 'DigitalsiteSaaS\Carrito\Http\CategoryController@dashboard');
Route::get('gestion/carrito/crearconfiguracion', 'DigitalsiteSaaS\Carrito\Http\CategoryController@crearconfiguracion');
Route::get('gestion/carrito/crearconfiguraciontienda', 'DigitalsiteSaaS\Carrito\Http\CategoryController@crearconfiguraciontienda');
Route::post('gestion/carrito/crearconfiguracionepayco', 'DigitalsiteSaaS\Carrito\Http\CategoryController@crearconfiguracionepayco');
Route::post('gestion/carrito/crearconfiguracionplace', 'DigitalsiteSaaS\Carrito\Http\CategoryController@crearconfiguracionplace');
Route::get('gestion/autor/editar/{id}', 'DigitalsiteSaaS\Carrito\Http\CategoryController@autoreditar');
Route::get('gestion/parametro/editar/{id}', 'DigitalsiteSaaS\Carrito\Http\CategoryController@parametroeditar');
Route::get('gestion/carrito/crear-cupon', 'DigitalsiteSaaS\Carrito\Http\CategoryController@cupons');

Route::post('gestion/carrito/createcupon', 'DigitalsiteSaaS\Carrito\Http\CategoryController@createcupon');
Route::get('gestion/autor/eliminar/{id}', 'DigitalsiteSaaS\Carrito\Http\CategoryController@autoreliminar');

Route::get('gestion/parametro/eliminar/{id}', 'DigitalsiteSaaS\Carrito\Http\CategoryController@parametroeliminar');

Route::resource('gestion/productos', 'DigitalsiteSaaS\Carrito\Http\ProductoController');

Route::get('gestion/productos/programacion/{id}', 'DigitalsiteSaaS\Carrito\Http\ProductoController@programacion');
Route::get('gestion/productos/crear/{id}', 'DigitalsiteSaaS\Carrito\Http\ProductoController@crear');

Route::post('gestion/ruta/crearruta', 'DigitalsiteSaaS\Carrito\Http\ProductoController@showruta');
Route::get('gestion/productos/editarproducto/{id}', 'DigitalsiteSaaS\Carrito\Http\ProductoController@editarproducto');
Route::post('gestion/productos/actualizar/{id}', 'DigitalsiteSaaS\Carrito\Http\ProductoController@actualizar');
Route::get('gestion/productos/eliminar/{id}', 'DigitalsiteSaaS\Carrito\Http\ProductoController@eliminar');
Route::get('gestion/productos/imagenes/{id}', 'DigitalsiteSaaS\Carrito\Http\ProductoController@imagenes');
Route::get('gestion/ruta/programacion/{id}', 'DigitalsiteSaaS\Carrito\Http\ProductoController@crearprogramacion');









Route::resource('gestion/usuarios/ordenes', 'DigitalsiteSaaS\Carrito\Http\UserController@ordenes');
Route::resource('gestion/usuarios/editar', 'DigitalsiteSaaS\Carrito\Http\UserController@editar');
Route::resource('gestion/usuarios/actualizar', 'DigitalsiteSaaS\Carrito\Http\UserController@actualizar');
Route::resource('gestion/usuarios/eliminar', 'DigitalsiteSaaS\Carrito\Http\UserController@eliminar');
Route::resource('gestion/usuarios/pruebas', 'DigitalsiteSaaS\Carrito\Http\UserController@pruebas');



});
Route::post('gestion/usuarios/crear', 'DigitalsiteSaaS\Carrito\Http\UserController@crear');


Route::get('memo/ajax-subcatweb', 'DigitalsiteSaaS\Carrito\Http\CartController@webdepartamentos');
Route::get('mema/ajax-subcatweb', 'Sitedigitalweb\Carrito\Http\CartController@webmunicipios');


Route::get('memaproducts/ajax-subcatweb', 'DigitalsiteSaaS\Carrito\Http\CartController@filtrowe');






Route::get('auto/suma',function(){


         $suma = DB::table('orders')
       ->select(DB::raw('SUM(shipping) AS total_orders'))
        ->get();


       dd($suma);

       $count = DB::table('orders')->count();
       dd($count);



       $data = DB::table("order_items")
    ->select(DB::raw("count(product_id) as count"))
    ->groupBy(DB::raw("product_id"))
    ->get();
dd($data);



});

Route::any('gestion/costoenvio', 'DigitalsiteSaaS\Carrito\Http\CartController@costoenvio');

Route::group(['middleware' => ['comprador']], function (){
Route::post('placetopay/pagoweb', 'DigitalsiteSaaS\Carrito\Http\CartController@generaplace');
Route::post('placetopay/pagowebsite', 'DigitalsiteSaaS\Carrito\Http\CartController@responsesite');
Route::get('placetopay/pagowebrequest/{id}', 'DigitalsiteSaaS\Carrito\Http\CartController@ejecutaplace');
Route::get('gestion/detalle/usuario', 'DigitalsiteSaaS\Carrito\Http\CartController@detalleuser');

Route::get('gestion/datosesion', 'DigitalsiteSaaS\Carrito\Http\CartController@datosesion');


});



 Route::get('mensajes/mensajes', 'DigitalsiteSaaS\Carrito\Http\CartController@mensajes')->middleware('web');




Route::post('placetopay/placenotificacion', 'DigitalsiteSaaS\Carrito\Http\CartController@placenotificacion');
/*
Route::any('web/session', 'Digitalsite\Carrito\Http\CartController@actionIndex');
Route::any('web/session/filtro', 'Digitalsite\Carrito\Http\CartController@actionIndexweb');
Route::any('web/limpieza', 'Digitalsite\Carrito\Http\CartController@limpieza');
Route::any('web/limpiezaweb', 'Digitalsite\Carrito\Http\CartController@limpiezaweb');
*/

Route::get('validacion/email', 'DigitalsiteSaaS\Carrito\Http\UserController@valiemail');






Route::get('carrito/pruebas/importExport', 'DigitalsiteSaaS\Carrito\Http\CartController@importExport');
Route::get('carrito/pruebas/downloadExcel/{type}', 'DigitalsiteSaaS\Carrito\Http\CartController@downloadExcel');
Route::post('carrito/pruebas/importExcel', 'DigitalsiteSaaS\Carrito\Http\CartController@importExcel');

Route::get('carrito/productos/importExport', 'DigitalsiteSaaS\Carrito\Http\CartController@importExportpro');
Route::get('carrito/productos/downloadExcel/{type}', 'DigitalsiteSaaS\Carrito\Http\CartController@downloadExcelpro');
Route::post('carrito/productos/importExcel', 'DigitalsiteSaaS\Carrito\Http\CartController@importExcelpro');


Route::get('carrito/municipios/importExport', 'DigitalsiteSaaS\Carrito\Http\CartController@importExportmun');
Route::get('carrito/municipios/downloadExcel/{type}', 'DigitalsiteSaaS\Carrito\Http\CartController@downloadExcelmun');
Route::post('carrito/municipios/importExcel', 'DigitalsiteSaaS\Carrito\Http\CartController@importExcelmun');

Route::get('gestion/usuarios/registrar', 'DigitalsiteSaaS\Carrito\Http\CartController@registrar');





// Todas las rutas del package deben usar el middleware 'web' para que la sesión funcione
Route::middleware('web')->group(function () {

    // ==================== RUTAS DEL CARRITO ====================
    Route::prefix('cart')->group(function () {
        // Mostrar carrito
        Route::get('/show', [Sitedigitalweb\Carrito\Http\CartController::class, 'show'])->name('cart-show');
        
        // Rutas AJAX para actualización automática
        Route::get('/update-ajax/{slug}/{quantity}', [Sitedigitalweb\Carrito\Http\CartController::class, 'updateAjax']);
        Route::get('/delete-ajax/{slug}', [Sitedigitalweb\Carrito\Http\CartController::class, 'deleteAjax']);
        Route::get('/trash-ajax', [Sitedigitalweb\Carrito\Http\CartController::class, 'trashAjax']);
        
        // Rutas tradicionales (fallback)
        Route::get('/add/{slug}', [Sitedigitalweb\Carrito\Http\CartController::class, 'add'])->name('cart-add');
        Route::get('/update/{slug}/{quantity}', [Sitedigitalweb\Carrito\Http\CartController::class, 'update'])->name('cart-update');
        Route::get('/delete/{slug}', [Sitedigitalweb\Carrito\Http\CartController::class, 'delete'])->name('cart-delete');
        Route::get('/trash', [Sitedigitalweb\Carrito\Http\CartController::class, 'trash'])->name('cart-trash');
    });

    // ==================== RUTAS PARA ENVÍO Y CUPONES ====================
    Route::get('/get-municipios/{departamento_id}', [Sitedigitalweb\Carrito\Http\CartController::class, 'getMunicipios'])->name('get-municipios');
    Route::post('/calcular-envio', [Sitedigitalweb\Carrito\Http\CartController::class, 'calcularEnvio'])->name('calcular-envio');
    Route::post('/validar-cupon-ajax', [Sitedigitalweb\Carrito\Http\CartController::class, 'validarCupon'])->name('validar-cupon-ajax');

    // ==================== RUTAS DE CHECKOUT ====================
    Route::get('/checkout', [Sitedigitalweb\Carrito\Http\CheckoutController::class, 'index'])->name('checkout');
    Route::post('/checkout/save-step1', [Sitedigitalweb\Carrito\Http\CheckoutController::class, 'saveStep1']);
    Route::post('/checkout/save-step2', [Sitedigitalweb\Carrito\Http\CheckoutController::class, 'saveStep2']);
    Route::post('/checkout/create-order', [Sitedigitalweb\Carrito\Http\CheckoutController::class, 'createOrder']);

    // ==================== RUTAS DE EPayco (callbacks) ====================
    Route::any('/epayco/response', [Sitedigitalweb\Carrito\Http\CheckoutController::class, 'epaycoResponse'])->name('epayco.response');
    Route::any('/epayco/confirmation', [Sitedigitalweb\Carrito\Http\CheckoutController::class, 'epaycoConfirmation'])->name('epayco.confirmation');

    // ==================== RUTAS DE ÓRDENES ====================
    Route::get('/order', [Sitedigitalweb\Carrito\Http\OrderController::class, 'showOrder'])->name('order.show');

    // ==================== RUTA DE PRUEBA (webhook) ====================
    Route::get('/sd/test-webhook', function() {
        $data = [
            'x_cust_id_cliente' => '1578383',
            'x_ref_payco' => '351864324',
            'x_id_factura' => 'DNdXFR4DMJJWuyAYF',
            'x_amount' => '174700',
            'x_tax' => '24700',
            'x_amount_base' => '130000',
            'x_currency_code' => 'COP',
            'x_transaction_state' => 'Aceptada',
            'x_extra1' => '16',
            'x_signature' => '9fd088a3bb23b6d57cebb7d53808704be7b4ce5975cedd4a2437fc3fd3846702'
        ];
        
        $request = new \Illuminate\Http\Request($data);
        $controller = new App\Http\Controllers\CheckoutController();
        return $controller->epaycoConfirmation($request);
    });

}); // Cierre del middleware web





