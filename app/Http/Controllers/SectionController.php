<?php

namespace App\Http\Controllers;

use App\Models\Classes;
use App\Models\ClassesAdmin;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Models\Section;
use App\Models\Collage;
use App\Models\CollageAdmin;
use App\Models\SectionAdmin;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;


class SectionController extends Controller
{
    public function showAllSections($id)
    {
        $section = Section::where('collage_id', $id)->get();
        if (sizeof($section) == 0) {
            $data = [];
            return response($data);
        }

        return response($section);
    }
    public function showSearchSection($name)
    {
        $coll = Collage::where('coll_name', $name)->get()->first();
        $section = Section::where('collage_id', $coll->coll_id)->get();
        if (sizeof($section) == 0) {
            $data = null;
            return response($data);
        }

        return response($section);
    }
    public function showSection($id)
    {
        $section = Section::find($id);
        if (!$section) {
            $msg = [];
            return response($msg);
        }
        return response($section);
    }
    protected function destroy($id)
    {
        $userr = JWTAuth::parseToken()->authenticate();
        
        $section = Section::find($id);
        if (!$section) {
            $myarray1 = [];
            return response($myarray1, 404);
        } else {
            
            $myarray1 = [
                'data' => null,
                'message' => "تم حذف القسم"
            ];
            if ($userr->jobtype=="مسؤول عام"){
            $section_admin=SectionAdmin::where('section_id',$id)->get();
            for($i=0;$i<sizeof($section_admin);$i++){
                $user=User::find($section_admin[$i]->admin_id);
                $user->delete();
            }
            $classes=Classes::where('section_id',$id)->get();
            //////
            for($i=0;$i<sizeof($classes);$i++){
            $class_admin=ClassesAdmin::where('class_id',$classes[$i]->class_id)->get();
            for($j=0;$j<sizeof($class_admin);$j++){
                $user=User::find($class_admin[$j]->admin_id);
                $user->delete();
            }
        }
            //////
            $section->delete();
            return response($myarray1, 404);
    }
    else if($userr->jobtype=="مسؤول كلية"){
        $mycollage=CollageAdmin::where('admin_id',$userr->id)->get()->first();
        if($mycollage->collage_id==$section->collage_id){
            $section_admin=SectionAdmin::where('section_id',$id)->get();
            for($i=0;$i<sizeof($section_admin);$i++){
                $user=User::find($section_admin[$i]->admin_id);
                $user->delete();
            }
            $classes=Classes::where('section_id',$id)->get();
            //////
            for($i=0;$i<sizeof($classes);$i++){
            $class_admin=ClassesAdmin::where('class_id',$classes[$i]->class_id)->get();
            for($j=0;$j<sizeof($class_admin);$j++){
                $user=User::find($class_admin[$j]->admin_id);
                $user->delete();
            }
        }
            //////
            $section->delete();
            return response($myarray1, 404);

        }
    }
    else{
        $msg=['msg'=>"Unuthenticated"];
        return response($msg);
    }
}
 }
    protected function create(Request $request)
    {
        $user=JWTAuth::parseToken()->authenticate();
        if($user->jobtype=="مسؤول عام"){ 
        $section = new Section();
        $section->sec_name = $request->name;
        $section->collage_id = $request->collage_id;
        $section->save();
        $msg = [
            'data' => $section,
            'msg' => "تممت الإضافة بنجاح"
        ];
        return response($msg);
    }
    else if ($user->jobtype=="مسؤول كلية"){
        $mycollage=CollageAdmin::where('admin_id',$user->id)->get()->first();
        if($mycollage->collage_id==$request->collage_id){
            $section = new Section();
            $section->sec_name = $request->name;
            $section->collage_id = $request->collage_id;
            $section->save();
            $msg = [
                'data' => $section,
                'msg' => "تممت الإضافة بنجاح"
            ];
            return response($msg);
        }
    }
    $msg=['msg'=>"Unuthenticated"];
    return response($msg);
    }
    protected function update($id, Request $request)
    {
        $user=JWTAuth::parseToken()->authenticate();
        if ($user->jobtype=="مسؤول عام"){
        $section = Section::find($id);
        if (!$section) {
            $msg = [];
            return response($msg);
        }
        $section->sec_name = $request->name;
        $section->update($request->all());
        $msg = [
            'data' => $section,
            'msg' => "تم التعديل بنجاح"
        ];
        return response($msg);
    }
    else if ($user->jobtype=="مسؤول كلية"){
        $mycollage=CollageAdmin::where('admin_id',$user->id)->get()->first();
        $section = Section::find($id);
        if (!$section) {
            $msg = [];
            return response($msg);
        }
        if ($mycollage->collage_id==$section->collage_id){
            $section->sec_name = $request->name;
            $section->update($request->all());
            $msg = [
                'data' => $section,
                'msg' => "تم التعديل بنجاح"
            ];
            return response($msg);
        }
    }
    $msg=['msg'=>"Unuthenticated"];
    return response($msg);
    }
    protected function search($name)
    {
        $section = Section::where('sec_name', $name)->get();
        if (!$section) {
            $msg = [];
            return response($msg);
        }
        return response($section);
    }
}
