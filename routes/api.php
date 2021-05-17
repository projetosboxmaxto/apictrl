<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::group([
        'middleware' => [ 'api']
    ], function ($router) {

                Route::get('/servers', 'TranscricaoController@getServers');
                Route::get('/events', 'EventosController@index');
                Route::post('/arquivo', 'EventosController@salvarArquivo');
                Route::get('/clear', 'EventosController@clear');


                Route::get('/clientes/lista', 'ClientesController@get');
                Route::get('/pracas/lista', 'PracasController@get');
                Route::get('/emissoras/lista', 'EmissorasController@get');


                Route::get('/eventos/{id}', 'EventosController@show');
                Route::get('/eventos2/{id}', 'EventosController@show2');
                Route::get('/eventos', 'EventosController@index2');
                Route::get('/eventos_filhos', 'EventosController@getEventoFilho');
                Route::get('/eventos_filhos2', 'EventosController@getEventoFilho2');
                Route::post('/eventos_status', 'EventosController@indicastatus');

                Route::get('/clientes', 'TranscricaoController@getClientes');
                Route::get('/programas', 'TranscricaoController@getProgramsFiltro');
                Route::get('/emissoras', 'TranscricaoController@getListEmissoras');
                
                
                Route::post('/project/{id}', 'EventosController@createFilho');
                Route::post('/project_hasfilho/{id}', 'EventosController@hasfilho');
                Route::post('/project_corte', 'EventosArquivosController@gerarRecorte');
                Route::post('/project_delete', 'ProjetoController@destroy');
                
                
                Route::get('/midiaclip_cadastros', 'MidiaClipController@index');
                Route::post('/materia_new', 'EventosArquivosController@gerarMateria');
                Route::post('/recorte_delete', 'EventosArquivosController@destroy');
                Route::get('/materia_gerada/{id}', 'MateriaRascunhoController@show_materia_gerada');
                Route::get('/materia_rascunho_del/{id}', 'MateriaRascunhoController@destroy');
                
                
                //Route::post('/project_corte', 'EventosArquivosController@gerarRecorte');
                
                
                Route::get('/search_arquivo/{id}', 'EventosArquivosController@temp_search');
                Route::get('/search_evento/{id}', 'EventosArquivosController@temp_search_programa');
             
                
                Route::get('/eventos_arquivos', 'EventosArquivosController@index');
                Route::get('/eventos_arquivos/{id}', 'EventosArquivosController@show');
                Route::get('/eventos_arquivos_simples/{id}', 'EventosArquivosController@show_simples');
                Route::get('/eventos_arquivos2', 'EventosArquivosController@index2');
                Route::get('/eventos_arquivos_byid', 'EventosController@show_byid');
                
                
	        Route::get('/eventos_arquivos_palavras', 'EventosArquivosPalavrasController@index');
                
                Route::get('/eventos_arquivos_palavras/{id}', 'EventosArquivosPalavrasController@show');
                Route::put('/eventos_arquivos_palavras_status', 'EventosArquivosPalavrasController@indicastatus');
                Route::post('/eventos_arquivos_palavras_status', 'EventosArquivosPalavrasController@indicastatus');
                
                Route::get('/agrupamento_notificacoes/{id}', 'AgrupamentoNotificacoesController@show');
                Route::put('/agrupamento_notificacoes_status', 'AgrupamentoNotificacoesController@indicastatus');
                Route::post('/agrupamento_notificacoes_status', 'AgrupamentoNotificacoesController@indicastatus');
                Route::post('/agrupamento_notificacoes_status_evento', 'AgrupamentoNotificacoesController@indicastatusEventoGlobal');
                
                
                
                Route::post('/materia_rascunho', 'MateriaRascunhoController@create');
                Route::get('/materia_rascunho', 'MateriaRascunhoController@index');
                Route::post('/materia_rascunho_filtro', 'MateriaRascunhoController@index');
                Route::post('/materia_rascunho_filtro2', 'MateriaRascunhoController@index2');
                Route::get('/materia_rascunho/{id}', 'MateriaRascunhoController@show');
                
                Route::get('/ajustapasta', 'ProjetoController@ajustapasta');
	        Route::post('/agrupamento_notificacoes_filtro', 'AgrupamentoNotificacoesController@index');
	        Route::post('/agrupamento_notificacoes_filtro2', 'AgrupamentoNotificacoesController@index2');
	        Route::post('/agrupamento_status', 'AgrupamentoNotificacoesController@getStatus');
                
                Route::get('/search_queries', 'SearchQueriesController@index');
                Route::get('/search_queries_edit/{id}', 'SearchQueriesController@show');
                Route::post('/search_querie_grid', 'SearchQueriesController@salvargrid');
                
                Route::get('/elastic_queries', 'ElasticQueriesController@index');
                Route::get('/elastic_queries_edit/{id}', 'ElasticQueriesController@show');
                Route::post('/elastic_querie_grid', 'ElasticQueriesController@salvargrid');

                Route::get('/cliente_configuracao', 'ClienteConfiguracaoController@index');
                Route::get('/cliente_configuracao/{id}', 'ClienteConfiguracaoController@show');
                Route::put('/cliente_configuracao/{id}', 'ClienteConfiguracaoController@update');
                Route::post('/cliente_configuracao', 'ClienteConfiguracaoController@create');
                Route::delete('/cliente_configuracao/{id}', 'ClienteConfiguracaoController@destroy');
                
                
                //Route::put('/eventos_arquivos_palavras/{id}', 'EventosArquivosPalavrasController@update');
                //Route::post('/eventos_arquivos_palavras', 'EventosArquivosPalavrasController@create');
                //Route::delete('/eventos_arquivos_palavras/{id}', 'EventosArquivosPalavrasController@destroy');
                
		//Route::resource('eventos', 'EventosController');
     });
     
     
