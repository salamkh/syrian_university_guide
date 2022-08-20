<?php
namespace App\Traits;
trait generalTrait {
public function returnErrors ($errNum , $msg){
    return response()-> json ([
        'status'=>false ,
        'errNum'=>$errNum,
        'msg'=>$msg
    ]);
}
public function returnSuccessMsg ($msg="",$errNUm="s000"){
    return ['status' => true  , 'errNum' => $errNum , 'msg'=> $msg];
}
public function returnData ($key , $value , $msg=""){
    return response()->json( ['status' => true  , 'errNum' => 's000' , 'msg'=> $msg , $key => $value]);
}

}
