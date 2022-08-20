<?php

namespace App\Http\Controllers;

use App\Models\WorkExperiance;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class WorkExperianceController extends Controller
{
    public function index($teacher_id)
    {
        $workExperiance = WorkExperiance::where('teacher_id', $teacher_id)->get();
        $myarray1 = [ ];
        if (sizeof($workExperiance) == 0) {
            $data=null;
            return response($data);
        }
        for($i=0;$i<sizeof($workExperiance);$i++){
            $id=$workExperiance[$i]->subject_id;
            $subject = Subject::find($id);
            $workExperiance[$i]->subject_id=$subject->subject_name;
        }
        if ($workExperiance) {
            return response($workExperiance, 200);
        }
    }

    public function store(Request $request, $teacher_id)
    {
        $user_auth = JWTAuth::parseToken()->authenticate();
        if ($user_auth->jobtype=="أستاذ" && $user_auth->id==$teacher_id){
        $validator = Validator::make($request->all(), [
            'subject_id' => 'required|int',
            'duration' => 'int',
        ]);

        if ($validator->fails()) {
            return response($validator->errors(), 400);
        }

        $id=$request->subject_id;
        $subject = Subject::find($id);
        $myarray = [];
        if (!$subject) {
            return response($myarray, 404);
        }
        $workExperiance = new WorkExperiance();
        $workExperiance->teacher_id = $teacher_id;
        $workExperiance->subject_id = $request->subject_id;
        $workExperiance->duration = $request->duration;
        $workExperiance->save();

        $message = [
            'data' => $workExperiance,
            'msg' => "تمت الإضافة بنجاح"
        ];
        return response($message);
    }  
}

    public function update(Request $request, $id)
    { 
        $user_auth = JWTAuth::parseToken()->authenticate();
        
        $workExperiance = WorkExperiance::find($id);
        if (!$workExperiance) {
            $myarray1 = [];
            return response($myarray1, 404);
        }
        else{
        if ($user_auth->jobtype=="أستاذ" && $user_auth->id==$workExperiance->teacher_id){
        $workExperiance->update($request->all());

        return response([
            'workExperiance' => $workExperiance,
            'message' => 'updated successfuly'
        ]);
    }
    }
    }

    public function destroy($id)
    {
        $user_auth = JWTAuth::parseToken()->authenticate();
        $workExperiance = WorkExperiance::find($id);
        if (!$workExperiance) {
            $myarray1 = [];
            return response($myarray1, 404);
        }
        else{
            if ($user_auth->jobtype=="أستاذ" && $user_auth->id==$workExperiance->teacher_id){
        $workExperiance->delete($id);

        $myarray2 = [
            'message' => "workExperiance deleted"
        ];
       
            return response($myarray2, 200);
    }
    }
          
    }
}
