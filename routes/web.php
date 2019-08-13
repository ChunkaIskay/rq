<?php

Route::get('/', function () {
    return view('auth.login');		
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
//Route::get('/menu', array(	'as' => 'menuRol',	'middleware' => 'auth',	'uses' => 'HomeController@index'));

Route::get('/home',  array(	'as' => 'menuRol',	'middleware' => 'auth',	'uses' => 'MenuRolController@index'));





	//Route::get('/JefeOperaciones/home', 'HomeController@index')->name('home');
	//Route::get('/JefeOperaciones/rqx','Tasks@index');

	Route::middleware(['auth','JefeOperaciones'])->prefix('JefeOperaciones')->namespace('JefeOperaciones')->group(function () {
	// nota:se saco /JefeOperaciones/ de todas las rutas de products y se agrego el prefix(JefeOperaciones)
	Route::get('/home', 'ImportantTasksController@index')->name('home');
	//lista
	Route::get('/rq-pendientes','ImportantTasksController@rqPendientes')->name('rqPendientes');
	//detalle
	Route::get('/{id}/rq-detalle','ImportantTasksController@pendienteDetalle')->name('pendDetalle'); 

	Route::get('/{id}/download/','ImportantTasksController@download')->name('downloadFile');
	Route::post('/aprobar-pendiente','ImportantTasksController@aprobarRqPen')->name('aprobarPendiente');
	
	Route::post('/subir-archivo','ImportantTasksController@uploadFile')->name('subirArchivo');
	Route::post('/delete-file','ImportantTasksController@deleteFile')->name('deleteFile');

//Route::delete('/{id}', array('as' => 'destroyContract',	'middleware' => 'auth',	'uses' => 'ContractController@destroyContract' ));

	//lista
	Route::get('/rq-pendientes-instalar', 'ImportantTasksController@rqPendientesInstalar')->name('pendienteInstalar');
	//detalle
	Route::get('/{id}/rq-pend-inst-detalle', 'ImportantTasksController@rqPendInstDetalle')->name('pendInstalarDetelle');
	//lista
	Route::get('/rq-examinar','ImportantTasksController@rqExaminarList')->name('examinarList');
	//detalle
	Route::get('/{id}/rq-examinar-detalle', 'ImportantTasksController@rqExaminarDetalle')->name('examinarDetalle');	
	//editar
	Route::get('/{id}/rq-modificar','ImportantTasksController@rqEditar')->name('requerimientoEdit');
	//actulizar
	Route::post('/{id}/rq-actualizar','ImportantTasksController@rqActualizar')->name('requermientoActualizar');
	//lista
	Route::get('/rq-reasignar-listado','ImportantTasksController@rqReasignarList')->name('rqReasignar');
	//editar
	Route::get('/{id}/rq-reasignar-editar','ImportantTasksController@rqReasignarEditar')->name('reasignarEditar');
	//actulizar
	Route::post('/{id}/rq-reasignar-actualizar','ImportantTasksController@rqReasignarActualizar')->name('requermientoActualizar');

	//lista
	Route::get('/rq-prioridad-listado','ImportantTasksController@rqPrioridadListado')->name('rqPrioridadList');
	//editar
	Route::get('/{id}/rq-prioridad-editar','ImportantTasksController@rqPrioridadEditar')->name('prioridadEditar');
	//actulizar
	Route::post('/{id}/rq-prioridad-actualizar','ImportantTasksController@rqPrioridadActualizar')->name('prioridadEditar');

	//lista all
	Route::get('/rq-estado-all','ImportantTasksController@rqEstadoAll')->name('estadoAll');

	Route::post('/rq-estado-all','ImportantTasksController@rqEstadoAll')->name('estadoAll');
	//lista  asignados
	Route::get('/rq-estado-all-asig','ImportantTasksController@rqEstadoAsig')->name('estadoAsig');
	Route::post('/rq-estado-all-asig','ImportantTasksController@rqEstadoAsig')->name('estadoAsig');
	//lista desarrollo
	Route::get('/rq-estado-all-desa','ImportantTasksController@rqEstadoDesa')->name('estadoDesa');
	Route::post('/rq-estado-all-desa','ImportantTasksController@rqEstadoDesa')->name('estadoDesa');
	//lista Pruebas
	Route::get('/rq-estado-all-pruebas','ImportantTasksController@rqEstadoPruebas')->name('estadoPruebas');
	Route::post('/rq-estado-all-pruebas','ImportantTasksController@rqEstadoPruebas')->name('estadoPruebas');
	//lista Instalación
	Route::get('/rq-estado-all-inst','ImportantTasksController@rqEstadoInst')->name('estadoInst');
	Route::post('/rq-estado-all-inst','ImportantTasksController@rqEstadoInst')->name('estadoInst');
	//lista certificado
	Route::get('/rq-estado-all-cert','ImportantTasksController@rqEstadoCert')->name('estadoCert');
	Route::post('/rq-estado-all-cert','ImportantTasksController@rqEstadoCert')->name('estadoCert');

	//lista seguimiento
	Route::get('/rq-seguimiento','ImportantTasksController@rqSeguimiento')->name('seguimiento');
	Route::post('/rq-seguimiento','ImportantTasksController@rqSeguimiento')->name('seguimiento');
    //destalle seguimiento
    Route::get('/{id}/rq-seguimiento','ImportantTasksController@rqSegtoDetalle')->name('seguimientoDetalle');
    // download seguimiento genrico
    Route::get('/{id}/download-seg/','ImportantTasksController@downloadSeg')->name('downloadFileSeg');
	//Route::get('/search-contract', array('as' => 'searchContract','middleware' => 'auth',	'uses' => 'OperationalManagementController@search'));

	//Route::post('/search-contract', array('as' => 'searchList','middleware' => 'auth',	'uses' => 'OperationalManagementController@search'));



});

	Route::get('logout', '\App\Http\Controllers\Auth\LoginController@logout');


//Route::get('/menu', 'MenuRolController@index')->name('menu');

/*Route::get('/JefeOperaciones/rq', 'Tasks@index')->name('rq');

Route::group(['prefix' => 'JefeOperaciones'], function(){
   Route::resource('rq', 'Tasks@index');
});*/

//Route::get('/JefeOperaciones/rq-pendientes','ImportantTasksController@index');

//Route::get('/JefeOperaciones/important', array('as' => 'rqPendientes',	'middleware' => 'auth',	'uses' => 'ImportantTasksController@index'	));

//Route::middleware(['auth','Jefe_Operaciones'])->prefix('jefeOperaciones')->namespace('Admin')->group(function () {
/*
Route::middleware(['JefeOperaciones'])->group(function () {
// nota:se saco /admin/ de todas las rutas de products y se agrego el prefix(admin)
	Route::get('/rq-pendientes','ImportantTasksController@index'); // listado
	Route::get('/rq-pendientes/create','ImportantTasksController@create'); // formulario
	Route::post('/rq-pendientes','ImportantTasksController@store'); // registrar
});

*/
/*Route::get('admin/catalog', function() {
    // Solo se permite el acceso a usuarios autenticados
})->middleware('auth');

// Route::get('/rq-pendientes', array(	'as' => 'rqPendientes',	'middleware' => 'auth',	'uses' => 'ImportantTasksController@index'));