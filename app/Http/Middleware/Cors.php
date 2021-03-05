<?php namespace App\Http\Middleware;

use Closure;

class Cors {

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
            ini_set('memory_limit', '1024M');
            
            
		 return $next($request)
        ->header('Access-Control-Allow-Origin', '*')
	->header('Access-Control-Allow-Headers', 'X-Requested-With, Application, Content-Type, Accept, Origin, Authorization, myauth, apiauth')
        ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
	}

}
