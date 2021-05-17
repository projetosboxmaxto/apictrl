<?php namespace App\Http\Controllers;

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
use Illuminate\Support\Facades\Cache;
use DateTime;


class EmissorasController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function get(Request $request)
	{
        $DB_MIDIACLIP = \App\Http\Dao\ConfigDao::getSchemaMidiaClip();
        
        $sql = " SELECT 
                    id, nome, servidor, id_veiculo, id_praca, uf, transcricao_url, ativo
                FROM boxintegra.emissora";


        $lista2 = DB::select($sql);
    
        return $this->sendResponse($lista2);  
    }
}