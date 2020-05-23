<?php

namespace App\Helpers;

use Firebase\JWT\JWT;
use Illuminate\Support\Facades\DB;//libreria de base de datos de laravel
use App\User;

class JwtAuth{

    public $key;
    
    public function __construct(){
        $this->key ='esto_prueba_secretClave123';
    }
    
    public function signup($email,$password,$getToken = null){
        // Buscar si existe usuario con sus credenciales

        $user = User::where([
            'email'     =>$email,
            'password'  =>$password
        ])->first();

        // Comprobar si son correctas
        $signup =false;
        if(is_object($user)){
             // Generar token con los datos del usuario identificado
            $signup = true;
            $token =  array(
                'sub'       =>  $user->id,
                'email'     =>  $user->email,
                'name'      =>  $user->name,
                'surname'   =>  $user->surname,
                'ial'       =>  time(),
                'exp'       =>  time()+(7*24*3600)
            );
            $jwt=JWT::encode($token,$this->key, 'HS256');
            if(!is_null($getToken)){
                $jwt= JWT::decode($jwt,$this->key, ['HS256']);
            }
            $data=$jwt;
        }else {
            $data=array(
                'status'    =>  'error',
                'message'   =>  'Login incorrecto',
            );
        }

        //Devolver los datos decodificados o el token, en funcion del parametro
        return $data;
    }
    public function checkToken($jwt,$getIdentity = false){
        $auth=false;
        try{
            $jwt = str_replace('"','',$jwt);
            $decode = JWT::decode($jwt,$this->key, ['HS256']);
            
            if(!empty($decode) && is_object($decode) && isset($decode->sub)){
                $auth=true;
            }
            if($getIdentity){
                $auth=  $decode;
            }
        }catch(\UnexpectedValueException $e){
            $auth= false;

        }catch(\DomainException $e){
            $auth= false;
        }
        
        return $auth;
        
    }

    
}