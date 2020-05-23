<?php

namespace App\Http\Middleware;

use Closure;

class ApiAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Comprobamos si el usuario esta identificado
        $token= $request->header('Authorization');
        $jwtAuth=new \JwtAuth();

        $checktoken=$jwtAuth->checkToken($token);
        if($checktoken ){
            return $next($request);
        }else{
            $data = array(
                'status'    =>  'error',
                'code'      =>  400,
                'message'   =>  'El Usuario no esta identificado'
            );
            return response()->json($data,$data['code']);

        }
        
    }
}
