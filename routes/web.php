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

//Asignación a Rq. Rq aprobados.
Route::get('JefeSistemas/rq-aprobados', 'JefeSistemas\ImportantTasksController@index')->name('rqAprob');
Route::get('JefeSistemas/{id}/rq-detalle-aprob', 'JefeSistemas\ImportantTasksController@rqDetalleAprob')->name('detalleAprob');
Route::post('JefeSistemas/{id}/rq-guardar-aprob','JefeSistemas\ImportantTasksController@rqGuadarAprob')->name('guadarAprobar');

// Rq certificados
Route::get('JefeSistemas/rq-certificados', 'JefeSistemas\ImportantTasksController@rqCertificados')->name('rqCertificados');
Route::get('JefeSistemas/{id}/rq-detalle-cert', 'JefeSistemas\ImportantTasksController@rqDetalleCert')->name('detalleCert');
Route::post('JefeSistemas/{id}/rq-guardar-cert','JefeSistemas\ImportantTasksController@rqGuadarCert')->name('guadarCertifidos');


// dowload
Route::get('JefeSistemas/{id}/download','JefeSistemas\ImportantTasksController@download')->name('downFileAprob');
Route::post('JefeSistemas/subir-archivo','JefeSistemas\ImportantTasksController@uploadFile')->name('subirArchivo');

	
// Jefe de Sistemas
/*Route::middleware(['auth','JefeSistemas'])->prefix('JefeSistemas')->namespace('JefeSistemas')->group(function () {
	//home
	Route::get('/rq-pendientes-aprob', 'TasksController@index')->name('rqPendientesAprob');
	//lista
//	Route::get('/rq-pendientes','ImportantTasksController@rqPendientes')->name('rqPendientes');
	//detalle
//	Route::get('/{id}/rq-detalle','ImportantTasksController@pendienteDetalle')->name('pendDetalle'); 

});
*/


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

    //listado de rq pendientes
	Route::get('/rq-lista-pendientes','ImportantTasksController@rqListaPendientes')->name('listaPendintes');
	Route::post('/rq-lista-pendientes','ImportantTasksController@searchReqPendientes')->name('searchRqPendientes');
	//Cliente
	Route::get('/lista-clientes','SpecialTasksController@listaClientes')->name('listaClientes');
	//Cliente nuevo
	Route::get('/nuevo-cliente','SpecialTasksController@nuevoCliente')->name('nuevoCliente');
	Route::post('/guardar-cliente','SpecialTasksController@guardarCliente')->name('guardarCliente');
	
	//CLiente modificar
	Route::get('/{id}/modificar-cliente','SpecialTasksController@modificarCliente')->name('modificarCliente');
	Route::post('/{id}/editar-cliente','SpecialTasksController@editarCliente')->name('editarCliente');

	//Operador
	Route::get('/lista-operador','SpecialTasksController@listaOperadores')->name('listaOperadores');
	//Operador nuevo
	Route::get('/nuevo-operador','SpecialTasksController@nuevoOperador')->name('nuevoOperador');
	Route::post('/guardar-operador','SpecialTasksController@guardarOperador')->name('guardarOperador');
	
	//Operador modificar
	Route::get('/{id}/modificar-operador','SpecialTasksController@modificarOperador')->name('modificarOperador');
	Route::post('/{id}/editar-operador','SpecialTasksController@editarOperador')->name('editarOperador');

	//Certificaciones lista
	Route::get('/lista-certificaciones','SpecialTasksController@listaCertificaciones')->name('listaCertificaciones');
	Route::get('/search-certificaciones','SpecialTasksController@listaCertificaciones')->name('listaCertificaciones');
	Route::post('/search-certificaciones','SpecialTasksController@searchCertificaciones')->name('searchCert');

	//Certificaciones Online lista
	Route::get('/lista-cert-online','SpecialTasksController@listaCertificacionesOnline')->name('listaCertOnline');
	Route::get('/search-cert-online','SpecialTasksController@listaCertificacionesOnline')->name('listaCertOnline');
	Route::post('/search-cert-online','SpecialTasksController@searchCertificacionesOnline')->name('searchCertOnline');

	// graficos y reportes
	Route::get('/graficos/reportes','SpecialTasksController@reportes')->name('reportes');
	Route::get('/graficos/graficos-act-ant','SpecialTasksController@reporteAntAct')->name('reporteAnteriorActual');

	Route::get('/graficos/graficos-any-date','SpecialTasksController@reporteAnyDate')->name('rpAnyDate');
	Route::get('/graficos/search-graficos','SpecialTasksController@reporteAnyDate')->name('rqSearchGraficos');
	Route::post('/graficos/search-graficos','SpecialTasksController@searchGraficos')->name('rqSearchGraficos');

	//tiepo fase requerimientos
	Route::get('/rq-fase-tiempo','SpecialTasksController@rqFaseTiempo')->name('faseTiempo');
	Route::get('/search-fase-tiempo','SpecialTasksController@rqSearchFaseTiempo')->name('searchFaseTiempo');
	Route::post('/search-fase-tiempo','SpecialTasksController@rqSearchFaseTiempo')->name('searchFaseTiempo');
});

//logout
Route::get('logout', '\App\Http\Controllers\Auth\LoginController@logout');

// graficos y reportes
Route::get('/graficos/reportes','GraficosEstadisticosController@reportes')->name('reportes');
Route::get('/graficos/graficos-act-ant','GraficosEstadisticosController@reporteAntAct')->name('reporteAnteriorActual');

Route::get('/graficos/graficos-any-date','GraficosEstadisticosController@reporteAnyDate')->name('rpAnyDate');
Route::get('/graficos/search-graficos','GraficosEstadisticosController@reporteAnyDate')->name('rqSearchGraficos');
Route::post('/graficos/search-graficos','GraficosEstadisticosController@searchGraficos')->name('rqSearchGraficos');



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