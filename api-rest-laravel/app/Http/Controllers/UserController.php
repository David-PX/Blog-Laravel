<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\User;


class UserController extends Controller
{
    public function pruebas(Request $request){
         return "Accion de pruebas de USER-CONTROLLER";

    }

    public function register(Request $request){


        //recojer los atos del usuario por post
           $json = $request->input('json',null);
           $params = json_decode($json); // datos en objeto
           $params_array = json_decode($json,true); // datos en array

        //limpiar datos
        $params_array = array_map('trim',$params_array);
           
       if(!empty($params_array) && !empty($params)){
        //validar datos
        $validate = \Validator::make($params_array, [
                  'name' => 'required|alpha',
                  'surname' => 'required|alpha',
                  'email' => 'required|email|unique:users', 
                  'password' => 'required'

        ]);

        if($validate->fails()){
            //validacion fallida
            $data = array(
              'status' => 'error',
              'code' => '404',
              'message' => 'El usuario no se ha creado',
              'errors' => $validate->errors()

        );
       
        }else {

            //validacion pasada correctamente

              //cifrar contraseña
                $pwd = hash('sha256',$params->password);




              //crear el usuario

                $user = new User();
                $user->name = $params_array['name'];
                 $user->surname = $params_array['surname'];
                  $user->email = $params_array['email'];
                   $user->password = $pwd;
                   $user->role= 'ROLE_USER'; 

                   //guardar el usuario
                   $user->save();

             $data = array(
              'status' => 'success',
              'code' => '200',
              'message' => 'El usuario  se ha creado',
              


             );}

             }else{
                  $data = array(
              'status' => 'error',
              'code' => '404',
              'message' => 'Datos invalidos',
              

        );

             }
 
        

        

        return response()->json($data, $data['code']);

    }
    public function login(Request $request){

        $JWTAuth = new \JWTAuth();

        // recibir datos por POST
        $json = $request->input('json',null);
        $params = json_decode($json);
        $params_array = \json_decode($json, true);


        // validar datos
        $validate = \Validator::make($params_array, [
                  
                  'email' => 'required|email', 
                  'password' => 'required'

        ]);

        if($validate->fails()){
            //validacion fallida
            $signup = array(
              'status' => 'error',
              'code' => '404',
              'message' => 'El usuario no se ha podido logear',
              'errors' => $validate->errors()

        );
       
        }else {
        // cifrar contraseña
         $pwd = hash('sha256',$params->password);
        // Devolver token o datos
        $signup = $JWTAuth->signup($params->email, $pwd);
        if(!empty($params->getToken)){
              $signup = $JWTAuth->signup($params->email, $pwd,true);
        }
        }

        

       

        return response()->json($signup, 200);
        
        

    }

      public function update(Request $request){


                // comprobar si el usuario esta identificado
                $token = $request->header('Authorization');
                $JWTAuth = new \JWTAuth();
                $chektoken = $JWTAuth->checkToken($token);

                // recojer datos por post
                  $json = $request->input('json' , null);
                  $params_array = json_decode($json, true);

                if($chektoken && !empty($params_array)){
                  
                  
                  // sacar usuario identificado
                  $user = $JWTAuth->checkToken($token,true);
                 
                  // validar datos 
                  $validate = \Validator::make($params_array, [
                  'name' => 'required|alpha',
                  'surname' => 'required|alpha',
                  'email' => 'required|email|unique:users,' . $user->sub
                  ]);
                  // quitar campos que no quiero actualizar
                  unset($params_array['id']);
                  unset($params_array['role']);
                  unset($params_array['password']);
                  unset($params_array['created_at']);
                  unset($params_array['remember_token']);
                  // actualizar usuario en bbdd
                  $user_update = User::where('id', $user->sub)->update($params_array);
                  // devolver array con resultado  
                  $data = array(
                    'code' => 200,
                    'status' => 'success',
                    'user' => $user,
                    'changes' => $params_array
                  ) ;              
                }else{
                  $data = array(
                    'code' => 400,
                    'status' => 'error',
                    'message' => 'El usuario no esta identificado'
                  );
                }

                return response()->json($data, $data['code']);





      }



      public function upload(Request $request){
        // recojer datos de la peticion
        $image = $request->file('file0');


        // Validacion de imagen
        $validate = \Validator::make($request->all(), [
            'file0' => 'required|image|mimes:jpg,jpeg,png,gif'
        ]);

        // guardar imagen
        if(!$image || $validate->fails()){
         $data = array(
                    'code' => 400,
                    'status' => 'error',
                    'message' => 'Error al subir imagen'
                  );
        }else{
          
           $image_name = time() . $image->getClientOriginalName();
          \Storage::disk('users')->put($image_name, \File::get($image));

           $data = array(
          'code' => 200,
          'status' => 'success',
          'image' => $image_name
        );

        }

       
        

        
        return response()->json($data, $data['code']);

      }

      public function getImage($filename){
        $isset = \Storage::disk('users')->exists($filename);
        if($isset){
          $file = \Storage::disk('users')->get($filename);
        return new Response($file, 200);
        }else{
          $data = array(
            'code' => 400,
            'status' => 'error',
            'message' => 'La imagen no existe.'

          );
        }
          return response()->json($data, $data['code']);
        

      }

      public function detail($id){
        $user = User::find($id);
        if(is_object($user)){
          $data = array(
            'code' => 200,
            'status' => 'success',
            'user' => $user

          );
        }else{
          $data = array(
            'code' => 404,
            'status' => 'error',
            'message' => 'El usuario no existe.'


          );

        }
        return response()->json($data, $data['code'] );
      }

} 
