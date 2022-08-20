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
use App\Models\practical_portion_assignment;
use App\Models\Section;
use App\Models\SubjectExperience;
use App\Models\PracticalPortion;
use App\Models\theoritical_portion_assignment;
use App\Models\TheoriticalPortion;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Http\Request;
use App\Models\TheoriticalAssignment;
use App\Models\WorkExperiance;
use App\Models\practicalAssignmentSubject;
use App\Models\theoriticalAssignmentSubject;

class AssignmentController extends Controller
{
    public function getsuitableAssignment($subject_id)
    {
        $subject = Subject::find($subject_id);
        if ($subject != null) {
            $theoritical_assignment=[];
            $practical_assignment = [];
            $practical_assignment_subject = practicalAssignmentSubject::where('subject_id', $subject_id)->get();
         if (sizeof($practical_assignment_subject)!=0) {
            for ($i = 0; $i < sizeof($practical_assignment_subject); $i++) {
                $practical_assignment[$i] = PracticalAssignment::find($practical_assignment_subject[$i]->practical_assignment_id);
            }
        }
            $theoritical_assignment_subject = theoriticalAssignmentSubject::where('subject_id', $subject_id)->get();
        if (sizeof($theoritical_assignment_subject) != 0) {
            for ($i = 0; $i < sizeof($theoritical_assignment_subject); $i++) {
                $theoritical_assignment[$i] = TheoriticalAssignment::find ($theoritical_assignment_subject[$i]->theoritical_assignment_id);
        }  
    }
            $array = [
                'subject_id' => $subject->subject_id,
                'practical_assignments' => $practical_assignment,
                'practical_Count' => sizeof($practical_assignment),
                'practical_availablity' => $subject->practical_availablity,
                'theoritical_assignments' => $theoritical_assignment,
                'theoritical_Count' => sizeof($theoritical_assignment),
                'theoritical_availablity' => $subject->theoritical_availablity
            ];
            return response($array);
        } else {
            $data = [];
            return response($data);
        }
    }
    public function calculatePortion($subject_id, Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $practical_portion = PracticalPortion::where('subject_id', $subject_id)->where('teacher_id', $user->id)->get()->first();

        $theoritical_portion = TheoriticalPortion::where('subject_id', $subject_id)->where('teacher_id', $user->id)->get()->first();

        if ($request->practical_number != 0) {

            if ($request->total_practical_number != 0 && $practical_portion == null) {
                $practical_portion = new PracticalPortion();
                $practical_portion->portion = ($request->practical_number) / ($request->total_practical_number) * 100;
                $practical_portion->teacher_id = $user->id;
                $practical_portion->subject_id = $subject_id;
                $practical_portion->save();
                if (sizeof($request->practical_assignment) > 0) {
                    for ($i = 0; $i < sizeof($request->practical_assignment); $i++) {
                        $practical_portion_assignment = new practical_portion_assignment();
                        $practical_portion_assignment->portion_id = $practical_portion->id;
                        $practical_portion_assignment->assignment_id = $request->practical_assignment[$i];
                        $practical_portion_assignment->save();
                    }
                }
            } else {
                if ($request->total_practical_number != 0) {
                    $practical_assignment_portion = practical_portion_assignment::where('portion_id', $practical_portion->id)->get();
                    if (sizeof($request->practical_assignment) > 0) {
                        for ($i = 0; $i < sizeof($practical_assignment_portion); $i++) {
                            $practical_assignment_portion[$i]->delete();
                        }
                        for ($i = 0; $i < sizeof($request->practical_assignment); $i++) {
                            $practical_assignment_portion = new practical_portion_assignment();
                            $practical_assignment_portion->portion_id = $practical_portion->id;
                            $practical_assignment_portion->assignment_id = $request->practical_assignment[$i];
                            $practical_assignment_portion->save();
                        }
                    }
                    $practical_portion->portion = ($request->practical_number) / ($request->total_practical_number) * 100;
                    $practical_portion->update();
                }
            }
        }
        if ($request->theoritical_number != 0) {

            if ($request->total_theoritical_number != 0 && $theoritical_portion == null) {
                $theoritical_portion = new TheoriticalPortion();
                $theoritical_portion->portion = ($request->theoritical_number) / ($request->total_theoritical_number) * 100;
                $theoritical_portion->teacher_id = $user->id;
                $theoritical_portion->subject_id = $subject_id;
                $theoritical_portion->save();
                if (sizeof($request->theoritical_assignment) > 0) {
                    for ($i = 0; $i < sizeof($request->theoritical_assignment); $i++) {
                        $theoritical_assignment_portion = new theoritical_portion_assignment();
                        $theoritical_assignment_portion->portion_id = $theoritical_portion->id;
                        $theoritical_assignment_portion->assignment_id = $request->theoritical_assignment[$i];
                        $theoritical_assignment_portion->save();
                    }
                }
            } else {
                if ($request->total_theoritical_number != 0) {
                    ////////////////////////
                    $theoritical_assignment_portion = theoritical_portion_assignment::where('portion_id', $theoritical_portion->id)->get();

                    if (sizeof($request->theoritical_assignment) > 0) {
                        for ($i = 0; $i < sizeof($theoritical_assignment_portion); $i++) {
                            $theoritical_assignment_portion[$i]->delete();
                        }
                        for ($i = 0; $i < sizeof($request->theoritical_assignment); $i++) {
                            $theoritical_assignment_portion = new theoritical_portion_assignment();
                            $theoritical_assignment_portion->portion_id = $theoritical_portion->id;
                            $theoritical_assignment_portion->assignment_id = $request->theoritical_assignment[$i];
                            $theoritical_assignment_portion->save();
                        }
                    }
                    ////////////////////////
                    $theoritical_portion->portion = ($request->theoritical_number) / ($request->total_theoritical_number) * 100;
                    $theoritical_portion->update();
                }
            }
        }

        $array = [
            'practical_protion' => $practical_portion,
            'theoritical_protion' => $theoritical_portion,
        ];
        return response($array);
    }
    public function showmypracticalportions()
    {
        $user = JWTAuth::parseToken()->authenticate();
        $practical_portion = PracticalPortion::where('teacher_id', $user->id)->get();
        if (sizeof($practical_portion) == 0) {
            $msg = ['msg' => "لا يوجد نسب لعرضها"];
            $data = [];
            return response($data);
        } else {
            $array = null;
            for ($i = 0; $i < sizeof($practical_portion); $i++) {
                $subject = Subject::find($practical_portion[$i]->subject_id);
                $array[$i] = [
                    'subject_name' => $subject->subject_name,
                    'portion_id' => $practical_portion[$i]->id,
                    'pprtion' => $practical_portion[$i]->portion

                ];
            }
            return response($array);
        }
    }


    public function showmytheoriticalportions()
    {
        $user = JWTAuth::parseToken()->authenticate();
        $theoritical_portion = TheoriticalPortion::where('teacher_id', $user->id)->get();
        if (sizeof($theoritical_portion) == 0) {
            $msg = ['msg' => "لا يوجد نسب لعرضها"];
            $data = [];
            return response($data);
        } else {
            $array = null;
            for ($i = 0; $i < sizeof($theoritical_portion); $i++) {
                $subject = Subject::find($theoritical_portion[$i]->subject_id);
                $array[$i] = [
                    'subject_name' => $subject->subject_name,
                    'portion_id' => $theoritical_portion[$i]->id,
                    'pprtion' => $theoritical_portion[$i]->portion
                ];
            }
            return response($array);
        }
    }

    public function deletePportion($id)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $practical_portion = PracticalPortion::where('id', $id)->where('teacher_id', $user->id)->get()->first();
        if ($practical_portion) {
            $practical_portion->delete();
            $msg = ['msg' => "تم الحذف بنجاح"];
            return response($msg);
        } else {
            $msg = ['msg' => "لا يمكن حذف العنصر"];
            return response($msg);
        }
    }
    public function deleteTportion($id)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $theoritical_portion = TheoriticalPortion::where('id', $id)->where('teacher_id', $user->id)->get()->first();
        if ($theoritical_portion) {
            $theoritical_portion->delete();
            $msg = ['msg' => "تم الحذف بنجاح"];
            return response($msg);
        } else {
            $msg = ['msg' => "لا يمكن حذف العنصر"];
            return response($msg);
        }
    }
    public function getteacher($subject_id)
    {
        $practical_portion = PracticalPortion::where('subject_id', $subject_id)->orderBy("portion", "DESC")->get();
        $theoritical_portion = TheoriticalPortion::where('subject_id', $subject_id)->orderBy("portion", "DESC")->get();
        $array = null;
        if (sizeof($practical_portion) == 0) {
            $array['practical'] = null;
        } else {
            for ($i = 0; $i < sizeof($practical_portion); $i++) {
                $user = User::where('id', $practical_portion[$i]->teacher_id)->get()->first();
                $array['practical'][$i] = [
                    'portion' => $practical_portion[$i]->portion,
                    'portion_id' => $practical_portion[$i]->id,
                    'usre_id' => $user->id,
                    'teacher_name' => $user->name,
                    'teacher_image' => $user->image
                ];
            }
        }
        if (sizeof($theoritical_portion) == 0) {
            $array['theoritical'] = null;
        } else {
            for ($i = 0; $i < sizeof($theoritical_portion); $i++) {
                $user = User::where('id', $theoritical_portion[$i]->teacher_id)->get()->first();
                $array['theoritical'][$i] = [
                    'portion' => $theoritical_portion[$i]->portion,
                    'portion_id' => $theoritical_portion[$i]->id,
                    'usre_id' => $user->id,
                    'teacher_name' => $user->name,
                    'teacher_image' => $user->image
                ];
            }
        }
        return response($array);
    }
    public function getselectedPassignment($portion_id)
    {
        $practical_assignment = practical_portion_assignment::where('portion_id', $portion_id)->get();
        if (sizeof($practical_assignment) > 0) {
            $array = null;
            for ($i = 0; $i < sizeof($practical_assignment); $i++) {
                $assignments = PracticalAssignment::find($practical_assignment[$i]->assignment_id);
                $array[$i] = [
                    "type" => $assignments->type,
                    "description" => $assignments->description
                ];
            }
            return response($array);
        } else {
            $data=[];
            $msg = ['msg' => "لا يوجد تكاليف"];
            return response($data);
        }
    }
    public function getselectedTassignment($portion_id)
    {
        $theoritical_assignment = theoritical_portion_assignment::where('portion_id', $portion_id)->get();

        if (sizeof($theoritical_assignment) > 0) {
            $array = null;
            for ($i = 0; $i < sizeof($theoritical_assignment); $i++) {
                $assignments = TheoriticalAssignment::find($theoritical_assignment[$i]->assignment_id);
                $array[$i] = [
                    "type" => $assignments->type,
                    "description" => $assignments->description
                ];
            }
            return response($array);
        } else {
            $data=[];
            $msg = ['msg' => "لا يوجد تكاليف"];
            return response($data);
        }
    }
}
