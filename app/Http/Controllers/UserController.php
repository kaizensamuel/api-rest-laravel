<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\User;

class UserController extends Controller
{
    //
    public function pruebas(Request $request){
        return "accion de pruebas user controller";
    }

    public function register(Request $request){
        // Recogemos datos del usuario por post
        $json = $request->input('json',null);
        $params= json_decode($json); // Devuelve los datos en un objeto
        $params_array = json_decode($json,true); // Devuelve los datos en un array

        if(!empty($params) && !empty($params_array)){
            // Limpiar datos
            $params_array = array_map('trim',$params_array);

            // Validar datos
            $validate = \Validator::make($params_array,[
                'name'          => 'required|alpha',
                'surname'       => 'required|alpha',
                'email'         => 'required|email|unique:users',
                'password'      => 'required'
            ]);
            if($validate->fails()){//validacion ha fallado
                $data = array(
                    'status'    =>  'error',
                    'code'      =>  404,
                    'message'   =>  'Usuario no creado',
                    'errors'    =>  $validate->errors()
                );
            }else{

                 // Cifrar la contraseña
                 $pwd = hash('sha256',$params->password);
                 // Crear el usuario 
                 $user= new User();
                 $user->name= $params_array['name'];
                 $user->surname= $params_array['surname'];
                 $user->email= $params_array['email'];
                 $user->password= $pwd;
                 $user->role= 'ROLE_USER';

                 $user->save();//Guardamos el usuario inser into datos en el objeto

                $data=array(
                    'status'    =>  'success',
                    'code'      =>  200,
                    'message'   =>  'Usuario creado correctamente',
                    'user'      =>  $user
                );
            }
        }else{
            $data=array(
                'status'    =>  'error',
                'code'      =>  404,
                'message'   =>  'Los datos enviados no son correctos'
            );
        }
        // Devolver una respuesta
        return  response()->json($data,$data['code']);
    }
    public function login(Request $request){
        $jwtAuth=new \JwtAuth();
        // Recibir datos post
        $json = $request->input('json',null);
        $params= json_decode($json); // Devuelve los datos en un objeto
        $params_array = json_decode($json,true); // Devuelve los datos en un array
        // Validar datos
        $validate = \Validator::make($params_array,[
            'email'         => 'required|email',
            'password'      => 'required'
        ]);
        if($validate->fails()){//validacion ha fallado
            $data = array(
                'status'    =>  'error',
                'code'      =>  404,
                'message'   =>  'El Usuario no se podido loguear',
                'errors'    =>  $validate->errors()
            );
        }else{

            // Cifrar la contraseña
            $pwd = hash('sha256',$params->password);
            if(!empty($params->gettokens)){
                $data = $jwtAuth->signup($email,$pwd,true);
            }else{
                $data = $jwtAuth->signup($params->email,$pwd);
            }     
       }
        // Devolver token o datos
        return response()->json($data,200);
    }
    public function update(Request $request){

        $token= $request->header('Authorization');
        $jwtAuth=new \JwtAuth();

        $checktoken=$jwtAuth->checkToken($token);

         // Recogemos datos por post
         $json = $request->input('json',null);
         $params_array = json_decode($json,true); // Devuelve los datos en un array

        if(!empty($params_array)){
            // Actualizamos usuario
            // Sacamos usuario identificado
            $user=$jwtAuth->checkToken($token,true);

            
            // Validamos los datos
            $validate = \Validator::make($params_array,[
                'name'          => 'required|alpha',
                'surname'       => 'required|alpha',
                'email'         => 'required|email|unique:users,'.$user->sub
            ]);

            // Quitamos los campos que no queremos que se actualize

            unset($params_array['id']);
            unset($params_array['role']);
            unset($params_array['password']);
            unset($params_array['create_at']);
            unset($params_array['remember_token']);

            // Actualizamos los datos del usuario en la base de datos
            $user_update=User::where('id',$user->sub)->update($params_array);

            // Devolvemos una respuesta
            $data = array(
                'status'    =>   'success',
                'code'      =>   200,
                'user'      =>   $user_update,
                'user'      =>   $params_array
            );

        }else{
            $data = array(
                'status'    =>  'error',
                'code'      =>  400,
                'message'   =>  'El Usuario no esta identificado'
            );
        }
        return response()->json($data,$data['code']);

    }
    public function upload( Request $request){
       
         // Recogemos datos por post
         $image= $request->file('file0');
         // Validamos los datos
         $validate = \Validator::make($request->all(),[
            'file0'          => 'required|image|mimes:jpg,jpeg,png,gif'
        ]);

         // Guardamos imagen
         if(!$image || $validate->fails()){
            $data = array(
                'status'    =>  'error',
                'code'      =>  400,
                'message'   =>  'Error al subir la imagen'
            );
         }else{
           
            $image_name = time() . $image->getClientOriginalName();
             \Storage::disk('users')->put($image_name,\File::get($image));
             $data = array(
                'status'    =>  'success',
                'code'      =>  200,
                'image'   =>  $image_name
            );
        
         }

         // Devolvemos resultado
       
        return response()->json($data,$data['code']);
    }
    public function getImage( $filename){
        $isset=\Storage::disk('users')->exists($filename);
        if($isset){
            $file=\Storage::disk('users')->get($filename);
            return new Response($file,200);
        } else {
            $data = array(
                'status'    =>  'error',
                'code'      =>  404,
                'message'   =>  'Error la imagen no existe'
            );
            return response()->json($data,$data['code']);
        }    
    }
    public function detail( $id){
        $user= User::find($id);
        if(is_object($user)){
            $data = array(
                'status'    =>  'succes',
                'code'      =>  200,
                'user'      =>  $user
            );
        } else {
            $data = array(
                'status'    =>  'error',
                'code'      =>  404,
                'message'   =>  'Error el usuario no existe'
            );
        }   
        return response()->json($data,$data['code']);
    }
}
