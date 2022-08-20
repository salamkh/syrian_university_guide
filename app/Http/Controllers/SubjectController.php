<?php

namespace App\Http\Controllers;

use App\Models\Classes;
use App\Models\Subject;
use App\Models\SubjectClass;
use App\Models\SubjectSection;
use App\Models\SectionAdmin;
use App\Models\ClassesAdmin;
use App\Models\Collage;
use App\Models\Section;
use App\Models\University;
use App\Models\SubjectExperience;
use App\Models\SubjectDomian;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Tymon\JWTAuth\Facades\JWTAuth;

class SubjectController extends Controller
{
    public function show($id)
    {
        $subject = Subject::find($id);
        $subject->domain=SubjectDomian::find($subject->domain_id);
        $subject->exp=SubjectExperience::find($subject->subject_id);
        if ($subject) {
            return response($subject);
        } else {
            $msg = [];
            return response($msg);
        }
    }
    public function showInClass($class_id)
    {
        $subjectClass = SubjectClass::where('class_id', $class_id)->get();
       
        if (sizeof($subjectClass)) {
            $subjects = null;
            $domin=null;
            for ($i = 0; $i < sizeof($subjectClass); $i++) {
                $subjects[$i] = Subject::find($subjectClass[$i]->subject_id);
                $subjects[$i]->domain=SubjectDomian::find($subjects[$i]->domain_id);
                $subjects[$i]->exp=SubjectExperience::where('subject_id',$subjects[$i]->subject_id)->get()->first();
               
                  }
            return response($subjects);
        } else {
            $data=[];
            $msg=['msg'=>"لا يوجد مقررات مضافة"];
            return response ($data);
        }
    }
    public function showInSection($section_id)
    {
        $subjectSection = SubjectSection::where('section_id', $section_id)->get();
        if (sizeof($subjectSection) != 0) {
            $subjects = null;
            $domain=null;
            $exp=null;
            for ($i = 0; $i < sizeof($subjectSection); $i++) {
                $subjects[$i] = Subject::find($subjectSection[$i]->subject_id);
                $subjects[$i]->domain=SubjectDomian::find($subjects[$i]->domain_id);
                $subjects[$i]->exp=SubjectExperience::where('subject_id',$subjects[$i]->subject_id)->get()->first();
                          
            }
            return response($subjects);
        } else {
            $data=[];
            $msg=['msg'=>"لا يوجد مقررات مضافة"];
            return response ($data);
        }
    }
    public function addSubjectToSection($section_id, Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        if ($user->jobtype == "مسؤول عام") {
            $subject = new Subject();
            $subject->subject_name = $request->name;
            $subject->practical_availablity = $request->practical_availablity;
            $subject->theoritical_availablity = $request->theroritical_availablity;
            $subject->domain_id = $request->domain_id;
            $subject->save();
            $experience = new SubjectExperience();
            $experience->exp_years = $request->exp_years;
            $experience->subject_id = $subject->subject_id;
            $experience->save();
            $section = new SubjectSection();
            $section->section_id = $section_id;
            $section->subject_id = $subject->subject_id;
            $section->save();
            $domain = SubjectDomian::find($subject->domain_id);
            $array = [
                'subject' => $subject,
                'experience years' => $experience->exp_years,
                'domian name' => $domain->domain_name
            ];
            return response($array);
        } else if ($user->jobtype == "مسؤول قسم") {
            $mysection = SectionAdmin::where('admin_id', $user->id)->get()->first();
            if ($mysection && $mysection->section_id == $section_id) {
                $subject = new Subject();
                $subject->subject_name = $request->name;
                $subject->practical_availablity = $request->practical_availablity;
                $subject->theoritical_availablity = $request->theoritical_availablity;
                $subject->domain_id = $request->domain_id;
                $subject->save();
                $experience = new SubjectExperience();
                $experience->exp_years = $request->exp_years;
                $experience->subject_id = $subject->subject_id;
                $experience->save();
                $section = new SubjectSection();
                $section->section_id = $section_id;
                $section->subject_id = $subject->subject_id;
                $section->save();
                $domain = SubjectDomian::find($subject->domain_id);
                $array = [
                    'subject' => $subject,
                    'experience years' => $experience->exp_years,
                    'domian name' => $domain->domain_name
                ];
                return response($array);
            } else {
                $msg = ['msg' => "Unutheticated"];
                return response($msg);
            }
        } else {
            $msg = ['msg' => "Unutheticated"];
            return response($msg);
        }
    }
    public function addSubjectToClass($class_id, Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        if ($user->jobtype == "مسؤول عام") {
            $subject = new Subject();
            $subject->subject_name = $request->name;
            $subject->practical_availablity = $request->practical_availablity;
            $subject->theoritical_availablity = $request->theoritical_availablity;
            $subject->domain_id = $request->domain_id;
            $subject->save();
            $experience = new SubjectExperience();
            $experience->exp_years = $request->exp_years;
            $experience->subject_id = $subject->subject_id;
            $experience->save();
            $class = new SubjectClass();
            $class->class_id = $class_id;
            $class->subject_id = $subject->subject_id;
            $class->save();
            $domain = SubjectDomian::find($subject->domain_id);
            $array = [
                'subject' => $subject,
                'experience years' => $experience->exp_years,
                'domian name' => $domain->domain_name
            ];
            return response($array);
        } else if ($user->jobtype == "مسؤول شعبة") {
            $myclass = ClassesAdmin::where('admin_id', $user->id)->get()->first();
            if ($myclass != null && $myclass->class_id == $class_id) {
                $subject = new Subject();
                $subject->subject_name = $request->name;
                $subject->practical_availablity = $request->practical_availablity;
                $subject->theoritical_availablity = $request->theoritical_availablity;
                $subject->domain_id = $request->domain_id;
                $subject->save();
                $experience = new SubjectExperience();
                $experience->exp_years = $request->exp_years;
                $experience->subject_id = $subject->subject_id;
                $experience->save();
                $class = new SubjectClass();
                $class->class_id = $class_id;
                $class->subject_id = $subject->subject_id;
                $class->save();
                $domain = SubjectDomian::find($subject->domain_id);
                $array = [
                    'subject' => $subject,
                    'experience years' => $experience->exp_years,
                    'domian name' => $domain->domain_name
                ];
                return response ($array);
            } else {
                $msg = ['msg' => "Unutheticated"];
                return response($msg);
            }
        } else {
            $msg = ['msg' => "Unutheticated"];
            return response($msg);
        }
    }
    public function destory($id)
    {
        $subject = Subject::find($id);
        $user = JWTAuth::parseToken()->authenticate();
        if ($user->jobtype == "مسؤول عام") {
            if ($subject) {
                $subject->delete();
                $msg = ['msg' => "تم حذف المفرر بنجاح"];
                return response($msg);
            }
        } else if ($user->jobtype == "مسؤول قسم") {
            $section = SubjectSection::where('subject_id', $id)->get()->first();
            if ($section != null) {
                $mysection = SectionAdmin::where('admin_id', $user->id)->get()->first();
                if ($mysection != null && $mysection->section_id == $section->section_id) {
                    $subject->delete();
                    $msg = ['msg' => "تم حذف المفرر بنجاح"];
                    return response($msg);
                }
                else {
                    $msg = ['msg' => "Unuthenticated"];
                    return response($msg);
                }
            }
            else {
                $msg = ['msg' => "Unuthenticated"];
                return response($msg);
            }
        } else if ($user->jobtype == "مسؤول شعبة") {
            $class = SubjectClass::where('subject_id', $id)->get()->first();
            if ($class != null) {
                $myclass = ClassesAdmin::where('admin_id', $user->id)->get()->first();
                if ($myclass != null && $myclass->class_id == $class->class_id) {
                    $subject->delete();
                    $msg = ['msg' => "تم حذف المفرر بنجاح"];
                    return response($msg);
                }
                else {
                    $msg = ['msg' => "Unuthenticated"];
                    return response($msg);
                }
            }
            else {
                $msg = ['msg' => "Unuthenticated"];
                return response($msg);
            }
        } else {
            $msg = ['msg' => "Unuthenticated"];
            return response($msg);
        }
    }
    public function update($id, Request $request)
    {
        $subject = Subject::find($id);
        $user = JWTAuth::parseToken()->authenticate();
        if ($user->jobtype == "مسؤول عام") {
            $subject->update($request->all());
            if ($request->exp_years) {
                $subjectExp = SubjectExperience::where('subject_id', $id)->get()->first();
                if( $subjectExp){
                $subjectExp->exp_years = $request->exp_years;
                $subjectExp->update();
                }
                else{
                    $experience = new SubjectExperience();
                    $experience->exp_years = $request->exp_years;
                    $experience->subject_id = $id;
                    $experience->save();
                }
            }
           $array=[
            'subject'=>$subject,
            'exp_years'=>$subjectExp->exp_years
           ];
           return response($array);
        } else if ($user->jobtype == "مسؤول قسم") {
            $section = SubjectSection::where('subject_id', $id)->get()->first();
            if ($section != null) {
                $mysection = SectionAdmin::where('admin_id', $user->id)->get()->first();
                if ($mysection != null && $mysection->section_id == $section->section_id) {
                    $subject->update($request->all());
                    $subjectExp=null;
                    if ($request->exp_years != null) {
                        $subjectExp = SubjectExperience::where('subject_id', $id)->get()->first();
                        $subjectExp->exp_years = $request->exp_years;
                        $subjectExp->update();
                    }
                    $array=[
                        'subject'=>$subject,
                        'exp_years'=>$subjectExp->exp_years
                       ];
                       return response($array);
                }
                else{
                    $msg=['msg'=>"Unuthenticated"];
                    return response ($msg);
                }
            }
        } else if ($user->jobtype == "مسؤول شعبة") {
            $class = SubjectClass::where('subject_id', $id)->get()->first();
            if ($class != null) {
                $myclass = ClassesAdmin::where('admin_id', $user->id)->get()->first();
                if ($myclass != null && $myclass->class_id == $class->class_id) {
                    $subject->update($request->all());
                    if ($request->exp_years != null) {
                        $subjectExp = SubjectExperience::where('subject_id', $id)->get()->first();
                        $subjectExp->exp_years = $request->exp_years;
                        $subjectExp->update();
                    }
                    $array=[
                        'subject'=>$subject,
                        'exp_years'=>$subjectExp->exp_years
                       ];
                       return response($array);
                }
                else{
                    $msg=['msg'=>"Unuthenticated"];
                    return response ($msg);
                }
            }
        }
        else{
            $msg=['msg'=>"Unuthenticated"];
            return response ($msg);
        }
    }
    public function search($name)
    {
        $subjects = Subject::where('subject_name', 'like', '%' . $name . '%')->get();
        if (sizeof($subjects) != 0) {
            $array = null;
            $domain_name="";
            for ($i = 0; $i < sizeof($subjects); $i++) {
                 $subjects[$i]->domain=SubjectDomian::find($subjects[$i]->domain_id);
                 $subjects[$i]->exp=SubjectExperience::where('subject_id',$subjects[$i]->subject_id)->get()->first();
                 $subjectclass = SubjectClass::where('subject_id', $subjects[$i]->subject_id)->get()->first();
                 if ($subjectclass) {
                    $class = Classes::where('class_id',$subjectclass->class_id)->get()->first();
                    $subjectsec = Section::where('sec_id', $class->section_id)->get()->first();
                    $subjects[$i]->collage = Collage::where('coll_id', $subjectsec->collage_id)->get()->first();
                    $subjects[$i]->university = University::where('id', $subjects[$i]->collage->university_id)->get()->first();
                } else {
                    $subjectsec = SubjectSection::where('subject_id', $subjects[$i]->subject_id)->get()->first();
                    $section=Section::where('sec_id',$subjectsec->section_id)->get()->first();
                    $subjects[$i]->collage = Collage::where('coll_id', $section->collage_id)->get()->first();
                    $subjects[$i]->university = University::where('id', $subjects[$i]->collage->university_id)->get()->first();
                }
                
                   
                }
             
            return response($subjects);
        } else {
            $msg = [];
            return response($msg);
        }
    }
}

