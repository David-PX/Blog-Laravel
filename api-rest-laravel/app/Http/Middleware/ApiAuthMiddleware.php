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
         // comprobar si el usuario esta identificado
                $token = $request->header('Authorization');
                $JWTAuth = new \JWTAuth();
                $chektoken = $JWTAuth->checkToken($token);

                if($chektoken){
                    return $next($request);
                }else{
                     $data = array(
                    'code' => 400,
                    'status' => 'error',
                    'message' => 'El usuario no esta identificado'
                  );
                  return response()->json($data, $data['code']);
                }
        
    }
}
