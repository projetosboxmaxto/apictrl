<?php 

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Service\ErrorsService;

use Illuminate\Http\Request;
use App\Eventos;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Dao\ImageDao;
use App\Http\Dao\PostsDao;
use Illuminate\Support\Facades\Artisan;

class MidiaClipController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index(Request $request)
	{
            $id_programa = $request->input("id_programa");
            $acao = $request->input("acao");
                       
            if ( $acao == "apresentador" || $acao == "apresentadores"){

                        $lista =  \App\Http\Service\MidiaClipService::getListaApresentador($id_programa);

                        return $this->sendResponse(array("data"=>$lista));
            }
            
            if ( $acao == "impacto" || $acao == "impactos"){
                 $lista =  \App\Http\Service\MidiaClipService::getListCadastroBasico(3);

                        return $this->sendResponse(array("data"=>$lista));
            }
            if ( $acao == "topicos" || $acao == "topico"){
                        $id_cliente = $request->input("id_cliente");
                        
                        if ( $id_cliente != ""){
                            $lista = \App\Http\Service\MidiaClipService::getListTopico($id_cliente);
                            
                            $obrigatorio  =  \App\Http\Service\MidiaClipService::getParameterByItem($id_cliente, "cliente", "topico_obrigatorio");
                            
                            return $this->sendResponse(array("data"=>$lista, "required" => $obrigatorio ));
                        }
                
            }
            
            return array("msg"=>"Indique a ação desejada");
        }
        
}