<?php



                        Route::get('/', 'HomeController@index' ); 
                        Route::get ('/message/grid', 'MailController@grid');

    
                        Route::get('/clear', 'Auth\LoginController@defaultMessage'); 


                        Route::get('/password.request', function () {
                                    //return view('posts.list');
                                   return view('auth.passwords.email');
                            })->name('password.request');
                            
                            
                        Route::get('/login', function () {
                                    //return view('posts.list');
                                   return view('login.login',  [ "URL_API_INTEGRADOR" => "" ]);
                            })->name('login');
                            
                    Route::post('/login', 'UserController@login');
                    Route::get('/logout', 'UserController@logout');
    
    // Route::get('/login', 'Auth\LoginController@showLoginForm' )->name('admin.login');


                            Route::get('/midiamodal', function () {
                                    return view('images.modallib');
                            });
     
   //Route::get('/login', ['as' => 'frontend.admin.login', 'uses' => 'Auth\LoginController@showLoginForm']);
 
                            
                            Route::get('/completa_materia/{id}', 'MateriaRascunhoController@completa_materia');
                            Route::get('/automatico_search', 'EventosArquivosController@automatico_search');
                            Route::get('/automatico_palavras', 'EventosArquivosController@automatico_palavras');
                            Route::get('/automatico_palavras_ajuste', 'EventosArquivosController@ajustatempo_arquivopalavras');
                            Route::get('/buscaTempo', 'EventosArquivosController@buscaTempo');
                            Route::get('/remove_notificacoes/{id}', 'MateriaRascunhoController@remove_notificacoes');
                            
                            Route::get('/insere_elastic', 'EventosArquivosController@insere_elastic');
                            Route::get('/lista_programa_emissora/{id}', 'SearchQueriesController@listaprograma');
                          
                           // Auth::routes();

                            Route::group([
                                    'middleware' => [ 'auth']
                                ], function ($router) {


                                            Route::get('/midiamodal', function () {
                                                    return view('images.modallib');
                            });


                            /*
                             * //Vue já cuida disso. */
                            Route::get('/programas', 'HomeController@index' );
                            Route::get('/recortes', 'HomeController@index');
                            Route::get('/notificacoes', 'HomeController@index' );
                            Route::get('/notificacoes2', 'HomeController@index' );
                            Route::get('/notificacoes3', 'HomeController@index' );
                            Route::get('/materias', 'HomeController@index' );
                            Route::get('/materias_salvas', 'HomeController@index' );
                            Route::get('/materias_rascunho', 'HomeController@index' );
                            Route::get('/tinder', 'HomeController@index' );
                            Route::get('/tinderlist', 'HomeController@index' );
                            Route::get('/configurar', 'HomeController@index' );
                            Route::get('/home', 'HomeController@index' )->name("home");
                            
                            Route::get('/ajustapasta', 'ProjetoController@ajustapasta');
                            Route::get('/deletepasta', 'ProjetoController@removeOldDirs');
                            Route::get('/ajustapath', 'ProjetoController@ajustapath');
                            
                            Route::get('/ajustamateria', 'ProjetoController@ajustamateria');
                            
                             Route::get('/removeold', 'ProjetoController@removeRegistrosAntigos');
                           // Route::post('/user/login', 'UserController@login');
                            
                            



                            });
                            
                          
                            Route::get('/eventos_consulta', 'EventosController@index3');  
                             Route::get('/remove_todos_arquivos', 'ProjetoController@remove_todos_arquivos');
                            Route::get('/testetempoarquivo2', 'ProjetoController@testetempo'); 
                            Route::get('/agrupanotificacao/{id}', 'ProjetoController@agrupanotificacao'); 
                            
                             Route::get('/ajustaTempoEvento', 'EventosController@ajustaTempo');
                            Route::get('/search_arquivo/{id}', 'EventosArquivosController@temp_search');
                            Route::get('/search_evento/{id}', 'EventosArquivosController@temp_search_programa');
                           
                            Route::get('/materia_gerada_teste/{id}', 'MateriaRascunhoController@show_materia_gerada');
                            Route::get('/clona_dados', 'EventosArquivosController@clonaDadosTeste');

/*
  Route::group([
        'middleware' => [ 'cors']
    ], function ($router) {
         //Add you routes here, for example:
         //Route::apiResource('/posts','PostController');
		

     });
     */



