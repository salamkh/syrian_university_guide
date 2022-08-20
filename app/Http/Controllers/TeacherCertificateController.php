<?php

namespace App\Http\Controllers;

use App\Models\TeacherCetification;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class TeacherCertificateController extends Controller
{

    public function index($teacher_id)
    {
        $cer = TeacherCetification::where('teacher_id', $teacher_id)->get();
        $myarray1 = [];
        if (sizeof($cer) == 0) {
            $data=null;

            return response($data);
        }
        if ($cer) {
            return response($cer, 200);
        }
    }

    public function store(Request $request, $teacher_id)
    {
        $user_auth = JWTAuth::parseToken()->authenticate();
        if ($user_auth->jobtype=="أستاذ" && $user_auth->id==$teacher_id){
        $validator = Validator::make($request->all(), [
            'degree' => 'required|string',
            'description' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()($validator->errors(), 400);
        }
        $cer = new TeacherCetification();
        $cer->teacher_id = $teacher_id;
        $cer->degree = $request->degree;
        $cer->description = $request->description;
        $cer->date = $request->date;
        $cer->save();

        $message = [
            'data' => $cer,
            'msg' => "تمت الإضافة بنجاح"
        ];
        return response($message);
    }
    }

    public function update(Request $request, $id)
    {
        $user_auth = JWTAuth::parseToken()->authenticate();
        $teacherCetification = TeacherCetification::find($id);
        if (!$teacherCetification) {
            $myarray1 = [
                'message' => "teacherCetification not found",
            ];
            return response($myarray1, 404);
        }
        else{
        if ($user_auth->jobtype=="أستاذ" && $user_auth->id== $teacherCetification->teacher_id){
        $teacherCetification->update($request->all());
        return response([
            'teacherCetification' => $teacherCetification,
            'message' => 'updated successfuly'
        ]);
    }
}
    }

    public function destroy($id)
    {
        $user_auth = JWTAuth::parseToken()->authenticate();
        $teacherCetification = TeacherCetification::find($id);
        if (!$teacherCetification) {
            $myarray1 = [];
            return response($myarray1, 404);
        }
        else{
        if ($user_auth->jobtype=="أستاذ" && $user_auth->id==$teacherCetification->teacher_id){
        $teacherCetification->delete($id);
        $myarray2 = [
            'message' => "teacherCetification deleted"
        ];
            return response($myarray2, 200);
    } 
    }
}
}
