<?php

Route::get('/', function () {
    return view('auth.login');		
});

Auth::routes();

/******PARA AJAX********/
/*
Route::post('miJqueryAjax','AjaxController@index');

Route::post('/', function () {
   return view('master');
});
*/

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

// Fecha de entrega Planifiacda y horas desarrollo
Route::get('JefeSistemas/entrega-planificada', 'JefeSistemas\ImportantTasksController@entregaPlanificada')->name('entregaPlanificada');
Route::get('JefeSistemas/search-rq','JefeSistemas\ImportantTasksController@searchlistaRq')->name('searchlistRq');
Route::post('JefeSistemas/search-rq','JefeSistemas\ImportantTasksController@searchlistaRq')->name('searchlistRq');

Route::get('JefeSistemas/{id}/rq-detalle-entrega','JefeSistemas\ImportantTasksController@detalleEntregaDesarrollo')->name('entregaDesarrollo');

Route::post('JefeSistemas/{id}/rq-guardar-entrega','JefeSistemas\ImportantTasksController@rqGuadarEntrega')->name('guadarEntrega');


// Revisión estado requerimiento
//lista all
Route::get('JefeSistemas/rq-estado-all','JefeSistemas\ImportantTasksController@rqEstadoAll')->name('estadoAll');
Route::post('JefeSistemas/rq-estado-all','JefeSistemas\ImportantTasksController@rqEstadoAll')->name('estadoAll');
//lista  asignados
Route::get('JefeSistemas/rq-estado-all-asig','JefeSistemas\ImportantTasksController@rqEstadoAsig')->name('estadoAsig');
Route::post('JefeSistemas/rq-estado-all-asig','JefeSistemas\ImportantTasksController@rqEstadoAsig')->name('estadoAsig');
//lista desarrollo
Route::get('JefeSistemas/rq-estado-all-desa','JefeSistemas\ImportantTasksController@rqEstadoDesa')->name('estadoDesa');
Route::post('JefeSistemas/rq-estado-all-desa','JefeSistemas\ImportantTasksController@rqEstadoDesa')->name('estadoDesa');
//lista Pruebas
Route::get('JefeSistemas/rq-estado-all-pruebas','JefeSistemas\ImportantTasksController@rqEstadoPruebas')->name('estadoPruebas');
Route::post('JefeSistemas/rq-estado-all-pruebas','JefeSistemas\ImportantTasksController@rqEstadoPruebas')->name('estadoPruebas');
//lista Instalación
Route::get('JefeSistemas/rq-estado-all-inst','JefeSistemas\ImportantTasksController@rqEstadoInst')->name('estadoInst');
Route::post('JefeSistemas/rq-estado-all-inst','JefeSistemas\ImportantTasksController@rqEstadoInst')->name('estadoInst');
//lista certificado
Route::get('JefeSistemas/rq-estado-all-cert','JefeSistemas\ImportantTasksController@rqEstadoCert')->name('estadoCert');
Route::post('JefeSistemas/rq-estado-all-cert','JefeSistemas\ImportantTasksController@rqEstadoCert')->name('estadoCert');
// end estado rq

//lista seguimiento
Route::get('JefeSistemas/rq-seguimiento','JefeSistemas\ImportantTasksController@rqSeguimiento')->name('seguimiento');
Route::post('JefeSistemas/rq-seguimiento','JefeSistemas\ImportantTasksController@rqSeguimiento')->name('seguimiento');
//destalle seguimiento
Route::get('JefeSistemas/{id}/rq-seguimiento','JefeSistemas\ImportantTasksController@rqSegtoDetalle')->name('seguimientoDetalle');


// Cambiar Prioridad a rq 
//lista 
Route::get('JefeSistemas/rq-prioridad-listado','JefeSistemas\ImportantTasksController@rqPrioridadListado')->name('rqPrioridadList1');
//editar
Route::get('JefeSistemas/{id}/rq-prioridad-editar','JefeSistemas\ImportantTasksController@rqPrioridadEditar')->name('prioridadEditar');
//actulizar
Route::post('JefeSistemas/{id}/rq-prioridad-actualizar','JefeSistemas\ImportantTasksController@rqPrioridadActualizar')->name('prioridadEditar');

Route::get('JefeSistemas/{id}/download/','JefeSistemas\ImportantTasksController@download')->name('downloadFile');
Route::post('JefeSistemas/aprobar-pendiente','JefeSistemas\ImportantTasksController@aprobarRqPen')->name('aprobarPendiente');

//Desarrollador
Route::get('JefeSistemas/lista-desarrollador','JefeSistemas\ImportantTasksController@listaDesarrolladores')->name('listaDesa');
//Desarrollador nuevo
Route::get('JefeSistemas/nuevo-desarrollador','JefeSistemas\ImportantTasksController@nuevoDesarrollador')->name('nuevoDesa');
Route::post('JefeSistemas/guardar-desarrollador','JefeSistemas\ImportantTasksController@guardarDesarrollador')->name('guardarDesa');
//Desarrollador modificar
Route::get('JefeSistemas/{id}/modificar-desarrollador','JefeSistemas\ImportantTasksController@modificarDesarrollador')->name('modificarDesarrollador');
Route::post('JefeSistemas/{id}/editar-desarrollador','JefeSistemas\ImportantTasksController@editarDesarrollador')->name('editarDesarrollador');


//Asignación de Rq. a instlar
Route::get('JefeSistemas/rq-asignar-instalar', 'JefeSistemas\ImportantTasksController@listarAsigInstalar')->name('rqListarAsigInst');
Route::post('JefeSistemas/search-asignar-instalar','JefeSistemas\ImportantTasksController@searchAsigInstalar')->name('searchAsignarInstalar');

Route::get('JefeSistemas/{id}/rq-detalle-asig-inst', 'JefeSistemas\ImportantTasksController@rqDetalleAsigInstalar')->name('detalleAsigInst');

Route::post('JefeSistemas/{id}/rq-guardar-instalar','JefeSistemas\ImportantTasksController@rqGuadarInstalar')->name('guadarInstalar');

//Asignación de Rq. a SOLUCIONAR.
Route::get('JefeSistemas/rq-asignar-solucionar', 'JefeSistemas\ImportantTasksController@listarAsigSolucionar')->name('rqListarAsigSolu');
Route::post('JefeSistemas/search-asignar-solucionar','JefeSistemas\ImportantTasksController@searchAsigSolucionar')->name('searchAsignarSolucionar');

Route::get('JefeSistemas/{id}/rq-detalle-asig-solu', 'JefeSistemas\ImportantTasksController@rqDetalleAsigSolucionar')->name('detalleAsigSolu');

Route::post('JefeSistemas/{id}/rq-guardar-solu','JefeSistemas\ImportantTasksController@rqGuadarSolucionar')->name('guadarSolucionar');

// graficos y reportes
Route::get('JefeSistemas/graficos/reportes','GraficosEstadisticosController@reportes')->name('reportes');
Route::get('JefeSistemas/graficos/graficos-act-ant','GraficosEstadisticosController@reporteAntAct')->name('reporteAnteriorActual');

Route::get('JefeSistemas/graficos/graficos-any-date','GraficosEstadisticosController@reporteAnyDate')->name('rpAnyDate');

//Route::get('JefeefeSistemas/{id}/rq-detalle-cert', 'JefeSistemas\ImportantTasksController@rqDetalleCert')->name('detalleCert');
//Route::post('JefeSistemas/{id}/rq-guardar-cert','JefeSistemas\ImportantTasksController@rqGuadarCert')->name('guadarCertifidos');


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

/******** ROL OPERADOR ********/
//lista seguimiento
Route::get('Operador/rq-seguimiento','Operador\ImportantTasksController@rqSeguimiento')->name('seguimiento');

Route::post('Operador/rq-seguimiento','Operador\ImportantTasksController@rqSeguimiento')->name('seguimiento');
//destalle seguimiento
Route::get('Operador/{id}/rq-seguimiento','Operador\ImportantTasksController@rqSegtoDetalle')->name('seguimientoDetalle');


// Lista de depurar req
Route::get('Operador/rq-depurar','Operador\ImportantTasksController@rqDepurarReq')->name('rqDepurarRequeriemto'); 

Route::post('Operador/rq-depurar','Operador\ImportantTasksController@rqDepurarReq')->name('rqDepurarRequeriemto');

//detalle depurar
Route::get('Operador/{id}/rq-depurar-detalle','Operador\ImportantTasksController@rqDepurarDetalle')->name('depuradorDetalle');

Route::post('Operador/{id}/rq-depurar-guardar','Operador\ImportantTasksController@rqGuardarDepurar')->name('guardarDepurar');

//listado de rq pendientes

Route::get('Operador/rq-pendientes','Operador\ImportantTasksController@rqListaPendientes')->name('rqPendintes');
Route::post('Operador/rq-pendientes','Operador\ImportantTasksController@searchReqPendientes')->name('searchPendientes');


// Requerimientos

//lista
Route::get('Operador/rq-listado','Operador\ImportantTasksController@rqExaminarList')->name('rqList');
//detalle
Route::get('Operador/{id}/req-detalle', 'Operador\ImportantTasksController@rqExaminarDetalle')->name('reqDetalle');	
//editar
Route::get('Operador/{id}/rq-modificar','Operador\ImportantTasksController@rqEditar')->name('requerimientoEdit');
//actulizar
Route::post('Operador/{id}/rq-actualizar','Operador\ImportantTasksController@rqActualizar')->name('requermientoActualizar');
//Requerimiento nuevo
Route::get('Operador/nuevo-requerimiento','Operador\ImportantTasksController@nuevoRequerimiento')->name('nuevoReq');
Route::post('Operador/guardar-req','Operador\ImportantTasksController@guardarRequerimiento')->name('guardarReq');

// subir archivo
Route::post('Operador/subir-archivo','Operador\ImportantTasksController@uploadFile')->name('subirArchivo');
	Route::post('Operador/delete-file','Operador\ImportantTasksController@deleteFile')->name('deleteFileOpe');

/*************** Verificador **************/

// Lista de depurar req
Route::get('Verificador/rq-depurar','Verificador\ImportantTasksController@rqDepurarReq')->name('rqDepurarReqVeri');

Route::post('Verificador/rq-depurar','Verificador\ImportantTasksController@rqDepurarReq')->name('rqDepurarReqVeri');

//detalle depurar
Route::get('Verificador/{id}/rq-depurar-detalle','Verificador\ImportantTasksController@rqDepurarDetalle')->name('depuradorDetalleVeri');

Route::post('Verificador/{id}/rq-depurar-guardar','Verificador\ImportantTasksController@rqGuardarDepurar')->name('guardarDepurar');

//lista seguimiento
Route::get('Verificador/rq-seguimiento','Verificador\ImportantTasksController@rqSeguimiento')->name('seguimientoVeri');

Route::post('Verificador/rq-seguimiento','Verificador\ImportantTasksController@rqSeguimiento')->name('seguimientoVeri');
//destalle seguimiento
Route::get('Verificador/{id}/rq-seguimiento','Verificador\ImportantTasksController@rqSegtoDetalle')->name('seguimientoDetalleVeri');

//lista - rq
Route::get('Verificador/rq-listado','Verificador\ImportantTasksController@rqExaminarList')->name('reqListado');
//detalle - rq
Route::get('Verificador/{id}/req-detalle', 'Verificador\ImportantTasksController@rqExaminarDetalle')->name('reqDet');

// Revisar requerimiento
//lista - rq
Route::get('Verificador/rq-revisar','Verificador\ImportantTasksController@rqRevisarList')->name('reqRevisarListado');
// Detalle - rq
Route::get('Verificador/{id}/rq-revisar-detalle', 'Verificador\ImportantTasksController@rqRevisarDetalle')->name('reqRevisarDet');
Route::post('Verificador/rq-revisar-detalle','Verificador\ImportantTasksController@rqGuardarRevisar')->name('guardarRevisar');

/**** DESARROLLADOR *****/

Route::get('Desarrollador/rev-asig-inst', 'Desarrollador\ImportantTasksController@revListarAsigInstInstalar')->name('revListarAsigInst');
Route::post('Desarrollador/search-rev-asig-inst','Desarrollador\ImportantTasksController@searchRevAsigInst')->name('searchRevAsignarInst');

Route::get('Desarrollador/{id}/rev-detalle-asig-inst', 'Desarrollador\ImportantTasksController@revDetalleAsigInst')->name('detalleRevAsigInst');

Route::post('Desarrollador/{id}/rev-guardar-inst','Desarrollador\ImportantTasksController@revGuadarInstalar')->name('guadarRevInstalar');

// revisar requerimiento asignados
Route::get('Desarrollador/rev-req-asig', 'Desarrollador\ImportantTasksController@revListarReqAsig')->name('revListarReqAsig');
Route::post('Desarrollador/search-rev-req-asig','Desarrollador\ImportantTasksController@searchReqAsig')->name('searchRevReqAsig');

Route::get('Desarrollador/{id}/rev-req-detalle-asig', 'Desarrollador\ImportantTasksController@revDetalleReqAsig')->name('detalleReqAsig');
/****Ajax****/
Route::post('Desarrollador/rev-req-guardar-asig','Desarrollador\ImportantTasksController@revGuadarReqAsig')->name('guadarReqAsig');

Route::post('Desarrollador/rev-req-tiempo-asig','Desarrollador\ImportantTasksController@revAsigTiempoReq')->name('revAsigMostrarDet');








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
	/*
	Route::get('/graficos/search-graficos','SpecialTasksController@reporteAnyDate')->name('rqSearchGraficos');
	Route::post('/graficos/search-graficos','SpecialTasksController@searchGraficos')->name('rqSearchGraficos');*/

	//tiempo fase requerimientos
	Route::get('/rq-fase-tiempo','SpecialTasksController@rqFaseTiempo')->name('faseTiempo');
	Route::get('/search-fase-tiempo','SpecialTasksController@rqSearchFaseTiempo')->name('searchFaseTiempo');
	Route::post('/search-fase-tiempo','SpecialTasksController@rqSearchFaseTiempo')->name('searchFaseTiempo');
});

//logout
Route::get('logout', '\App\Http\Controllers\Auth\LoginController@logout');

// graficos y reportes
/*
Route::get('/graficos/reportes','GraficosEstadisticosController@reportes')->name('reportes');
Route::get('/graficos/graficos-act-ant','GraficosEstadisticosController@reporteAntAct')->name('reporteAnteriorActual');

Route::get('/graficos/graficos-any-date','GraficosEstadisticosController@reporteAnyDate')->name('rpAnyDate');*/


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