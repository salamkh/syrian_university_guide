<?php

namespace App\Http\Controllers;

use App\Models\Collage;
use App\Models\Subject;
use App\Models\SubjectClass;
use App\Models\SubjectDomian;
use App\Models\SubjectSection;
use App\Models\University;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use App\Models\Classes;
use App\Models\WorkExperiance;
use Illuminate\Http\Request;
use App\Models\Section;

class knowledg_domainController extends Controller
{
    public function showall()
    {
        $domain = SubjectDomian::get();
        if (sizeof($domain) == 0) {
            $msg = [];
            return response($msg);
        } else {
            return response($domain);
        }
    }
    public function showdomain($id)
    {
        $domain = SubjectDomian::find($id);
        if (!$domain) {
            $msg = [];
            return response($msg);
        } else {
            return response($domain);
        }
    }
    public function adddomain(Request $request)
    {
        $domain = new SubjectDomian();
        $domain->domain_name = $request->name;
        $domain->save();
        return response($domain);
    }
    public function updatedomain($id, Request $request)
    {
        $domain = SubjectDomian::find($id);
        if (!$domain) {
            $msg = [];
            return response($msg);
        } else {
            $domain->update($request->all());
            return response($domain);
        }
    }
    public function deletedomain($id)
    {
        $domain = SubjectDomian::find($id);
        if (!$domain) {
            $msg = [];
            return response($msg);
        } else {
            $domain->delete();
            $msg = ['msg' => "تم الحذف بنجاح"];
            return response($msg);
        }
    }
    public function search($name)
    {
        $domain = SubjectDomian::where('domain_name', 'like', '%' . $name . '%')->get();
        if (sizeof($domain) != 0) {
            return response($domain);
        } else {
            $msg = [];
            return response($msg);
        }
    }
    public function showSubjects($domain_id)
    {

        $subjects = Subject::where('domain_id', $domain_id)->get();
        return response($subjects);
    }
    public function getsubjectforteacher($domain_id)
    {
        $user = JWTAuth::parseToken()->authenticate();
        if ($user) {
            if ($user->jobtype == "أستاذ") {
                $subjects = Subject::where('domain_id', $domain_id)->get();
                $array = [];
                for ($i = 0; $i < sizeof($subjects); $i++) {
                    $university = null;
                    $collage = null;
                    $wex = WorkExperiance::where('subject_id', $subjects[$i]->subject_id)->where('teacher_id', $user->id)->get()->first();
                    $subjectclass = SubjectClass::where('subject_id', $subjects[$i]->subject_id)->get()->first();
                    if ($subjectclass) {
                        $class = Classes::where('class_id',$subjectclass->class_id)->get()->first();
                        $subjectsec = Section::where('sec_id', $class->section_id)->get()->first();
                        $collage = Collage::where('coll_id', $subjectsec->collage_id)->get()->first();
                        $university = University::where('id', $collage->university_id)->get()->first();
                    } else {
                        $subjectsec = SubjectSection::where('subject_id', $subjects[$i]->subject_id)->get()->first();
                        $section=Section::where('sec_id',$subjectsec->section_id)->get()->first();
                        $collage = Collage::where('coll_id', $section->collage_id)->get()->first();
                        $university = University::where('id', $collage->university_id)->get()->first();
                    }
                    if ($wex) {
                        $array[$i] = [
                            'subject' => $subjects[$i],
                            'experiance' => $wex->duration,
                            'university_name'=>$university->name,
                            'collage_name'=>$collage->coll_name
                        ];
                    } else if (!$wex) {
                        $array[$i] = [
                            'subject' => $subjects[$i],
                            'experiance' => 0,
                            'university_name'=>$university->name,
                            'collage_name'=>$collage->coll_name
                        ];
                    }
                }
                for ($i = 0; $i < sizeof($array) - 1; $i++) {
                    $swap = 0;
                    for ($j = 0; $j < sizeof($array) - 1 - $i; $j++) {
                        if ($array[$j]['experiance'] < $array[$j + 1]['experiance']) {
                            $temp = $array[$j + 1];
                            $array[$j + 1] = $array[$j];
                            $array[$j] = $temp;
                            $swap++;
                        }
                    }
                    if ($swap == 0) {
                        break;
                    }
                }
                return response($array);
            }
        }
    }
}
