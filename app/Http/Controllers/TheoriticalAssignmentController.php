<?php

namespace App\Http\Controllers;

use App\Models\TheoriticalAssignment;
use Illuminate\Http\Request;
use App\Models\Subject;
use App\Models\SectionAdmin;
use App\Models\ClassesAdmin;
use App\Models\SubjectClass;
use App\Models\SubjectSection;
use App\Models\Classes;
use App\Models\Collage;
use App\Models\PracticalAssignment;
use App\Models\Section;
use App\Models\theoriticalAssignmentSubject;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Tymon\JWTAuth\Facades\JWTAuth;

class TheoriticalAssignmentController extends Controller
{
    //TheoriticalAssignment
    public function showAssignment($id)
    {
        $practical_assignment = TheoriticalAssignment::find($id);
        if (!$practical_assignment) {
            $msg = [];
            return response($msg);
        } else {
            return response($practical_assignment);
        }
    }

    public function showSubjectAssignment($subject_id)
    {
        $practical_assignment = theoriticalAssignmentSubject::where('subject_id', $subject_id)->get();
        if (sizeof($practical_assignment) == 0) {
            $data=[];
            $msg = [];
            return response($data);
        } else {
            $array = [];
            for ($i = 0; $i < sizeof($practical_assignment); $i++) {
                $array[$i] = TheoriticalAssignment::find ($practical_assignment[$i]->theoritical_assignment_id);
            }
            return response($array);
        }
    }

    public function createAssignment(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        if ($user->jobtype == "مسؤول عام" || $user->jobtype == "مسؤول قسم" || $user->jobtype == "مسؤول شعبة") {
            $practical_assignment = new TheoriticalAssignment();
            $practical_assignment->type = $request->type;
            $practical_assignment->description = $request->description;
            $practical_assignment->save();
            for ($i = 0; $i < sizeof($request->subjects); $i++) {
                $assignment_subject = new theoriticalAssignmentSubject();
                $assignment_subject->subject_id = $request->subjects[$i];
                $assignment_subject->theoritical_assignment_id = $practical_assignment->id;
                $assignment_subject->save();
            }
            return response($practical_assignment);
        }
        else {
            $msg = ['msg' => "Unuthenticateddd"];
            return response($msg);
        }
    }

    public function updateAssignment($id, Request $request)
    {
        $practical_assignment = TheoriticalAssignment::find($id);
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
        $practical_assignment = TheoriticalAssignment::find($id);
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
