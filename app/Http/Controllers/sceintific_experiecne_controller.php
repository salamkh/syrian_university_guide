<?php

namespace App\Http\Controllers;

use App\Models\scientific_exp;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Http\Request;

class sceintific_experiecne_controller extends Controller
{
    public function show ($teacher_id){
        $experienc = scientific_exp::where('teacher_id',$teacher_id)->get();
        if (sizeof($experienc)==0){
            $msg=[];
            return response ($msg);
        }
        $array=[];
        for ($i=0;$i<sizeof($experienc);$i++){
            $array[$i]=$experienc[$i];
        }
        return response ($array);
    }
    public function add($teacher_id,Request $request){
        $user = JWTAuth::parseToken()->authenticate();
        $experienc = new scientific_exp();
        $experienc->description=$request->description;
        $experienc->teacher_id=$teacher_id;
        $experienc->save();
        return response ($experienc);
    }
    public function edit ($id , Request $request){
        $user = JWTAuth::parseToken()->authenticate();
        $experiene = scientific_exp::find($id);
        if ($experiene && $experiene->teacher_id==$user->id){
            $experiene->update($request->all());
            return response ($experiene);
        }
        else {
            $msg=['msg'=>"Unuthenticated"];
            return response ($msg);
        }
    }
    public function delete ($id){
        $user = JWTAuth::parseToken()->authenticate();
        $experiene = scientific_exp::find($id);
        if ($experiene && $experiene->teacher_id==$user->id){
            $experiene->delete();
            $msg=[];
            return response ($msg);
        }
        else {
            $msg=['msg'=>"Unuthenticated"];
            return response ($msg);
        }
    }
}
