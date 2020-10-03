<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Category;

class CategoryController extends Controller
{

    public function __construct(){
        $this->middleware('api.auth' , ['except' => ['index' , 'show']]);

    }

    public function index(){
        $categories = Category::all();

        return response()->json([

            'code' => 200,
            'status' => 'success',
            'categories' => $categories


        ]);
    }


    public function show($id){
        $category = Category::find($id);

        if(is_object($category)){

        $data =    [

            'code' => 200,
            'status' => 'success',
            'categories' => $category


            ];



        }else{
        $data =    [

            'code' => 400,
            'status' => 'error',
            'categories' => 'La categoria no existe'


        ];
        }
         
        return response()->json($data, $data['code']);


    }
    public function store(Request $request){

        // recojer los datos por post
        $json = $request->input('json' , null);
        $params_array = json_decode($json, true);

        if(!empty($params_array)){
        // validar los atos
        $validate = \Validator::make($params_array, [

            'name' => 'required'
        ]);
        // guardar la categoria
        if($validate->fails()){
            $data = [
                'code'=>400,
                'status'=> 'error',
                'message'=> 'No se ha guardado la categoria'
            ];
        }else{
            $category = new Category();
            $category->name = $params_array['name'];
            $category->save();
            $data = [
                'code'=>200,
                'status'=> 'success',
                'message'=> $category
            ];
        }
        // devolver resultado
       
        }else{
             $data = [
                'code'=>400,
                'status'=> 'error',
                'message'=> 'No se has enviado ninguna categoria'
            ];
            
        }

         return response()->json($data, $data['code']);

    }


    public function update($id, Request $request){
        // recojer datos por post
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);
        if(!empty($params_array)){

        
        // Validar los datos
        $validate = \Validator::make($params_array, [
            'name' => 'required'
        ]);
        // Quitar lo que qno quiero actualizar
            unset($params_array['id']);
             unset($params_array['created_at']);

        // Actualizar el registro
        $category = Category::where('id', $id)->update($params_array);
        $data = [
            'code' => 200,
            'status' => 'success',
            'category' => $params_array
        ];
        }else{
             $data = [
                'code'=>400,
                'status'=> 'error',
                'message'=> 'No se has enviado ninguna categoria'
            ];
        }
        // Devolver los datos
        return response()->json($data, $data['code']);


    }
}