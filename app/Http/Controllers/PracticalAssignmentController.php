<?php

namespace App\Http\Controllers;

use App\Models\PracticalAssignment;
use App\Models\Subject;
use App\Models\SectionAdmin;
use App\Models\ClassesAdmin;
use App\Models\SubjectClass;
use App\Models\SubjectSection;
use App\Models\Classes;
use App\Models\Collage;
use App\Models\practicalAssignmentSubject;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Tymon\JWTAuth\Facades\JWTAuth;

class PracticalAssignmentController extends Controller
{
    //PracticalAssignment
    public function showAssignment($id)
    {
        $practical_assignment = PracticalAssignment::find($id);
        if (!$practical_assignment) {
            $msg = [];
            return response($msg);
        } else {
            return response($practical_assignment);
        }
    }

    public function showSubjectAssignment($subject_id)
    {
        $practical_assignment = practicalAssignmentSubject::where('subject_id', $subject_id)->get();
        if (sizeof($practical_assignment) == 0) {
            $data=[];
            $msg = [];
            return response($data);
        } else {
            $array = [];
            for ($i = 0; $i < sizeof($practical_assignment); $i++) {
                $array[$i] = PracticalAssignment::find($practical_assignment[$i]->practical_assignment_id);
            }
            return response($array);
        }
    }

    public function createAssignment(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        if ($user->jobtype == "مسؤول عام" || $user->jobtype == "مسؤول شعبة" || $user->jobtype == "مسؤول قسم") {
            $practical_assignment = new PracticalAssignment();
            $practical_assignment->type = $request->type;
            $practical_assignment->description = $request->description;
            $practical_assignment->save();
            for ($i = 0; $i < sizeof($request->subjects); $i++) {
                $asignment_subject = new practicalAssignmentSubject();
                $asignment_subject->subject_id = $request->subjects[$i];
                $asignment_subject->practical_assignment_id = $practical_assignment->id;
                $asignment_subject->save();
            }
            return response($practical_assignment);
        }
        else {
            $msg = ['msg' => "Unuthenticated"];
            return response($msg);
        }
    }

    public function updateAssignment($id, Request $request)
    {
        $practical_assignment = PracticalAssignment::find($id);
        if (!$practical_assignment) {
            $msg = [];
            return response($msg);
        } else {
            $user = JWTAuth::parseToken()->authenticate();
            if ($user->jobtype == "مسؤول عام" || $user->jobtype == "مسؤول قسم" || $user->jobtype == "مسؤول شعبة") {
               if($request->description)
              $practical_assignment->description=$request->description;
                $practical_assignment->update($request->all());
                return response($practical_assignment);
            }
            else {
                $msg = ['msg' => "Unuthenticated"];
                return response($msg);
            }
        }
    }

    public function destroyAssignment($id)
    {
        $practical_assignment = PracticalAssignment::find($id);
        if (!$practical_assignment) {
            $msg = [];
            return response($msg);
        } else {
            $user = JWTAuth::parseToken()->authenticate();
            if ($user->jobtype == "مسؤول عام" || $user->jobtype == "مسؤول قسم" || $user->jobtype == "مسؤول شعبة") {
                $practical_assignment->delete();
                $msg = ['msg' => "تم الحذف بنجاح"];
                return response($msg);
            } else {
                $msg = ['msg' => "Unuthenticated"];
                return response($msg);
            }
        }
    }
}
