<?php

namespace App\Http\Controllers;

use App\Models\Classes;
use App\Models\ClassesAdmin;
use App\Models\Collage;
use App\Models\CollageAdmin;
use App\Models\Section;
use App\Models\SectionAdmin;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Models\University;
use App\Models\UniversityAdmin;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class UniversityController extends Controller
{
    public function showAllUniversities()
    {
        $universities = University::get();
        if (!$universities) {
            $msg = [];
            return response()->json([
                'data' => $universities,
                'msg' => $msg
            ]);
        }

        return response($universities);
    }
    protected function showUniversity($id)
    {
        $universities = University::find($id);
        if (!$universities) {
            $msg = [];
            return response()->json([
                'data' => null,
                'msg' => $msg
            ]);
        }
        return response($universities);
    }
    protected function destroy($id)
    {
        $university = University::find($id);

        if (!$university) {
            $myarray1 = [];
            return response($myarray1, 404);
        } else {

            $myarray1 = [

                'message' => "تم حذف الجامعة"
            ];
            $uni_admin=UniversityAdmin::where('university_id',$id)->get();
            for ($i=0;$i<sizeof($uni_admin);$i++){
                $user=User::find($uni_admin[$i]->admin_id);
                $user->delete();
            }
            $collages=Collage::where('university_id',$id)->get();
            for ($i=0;$i<sizeof($collages);$i++){
                $collage_admin=CollageAdmin::where('collage_id',$collages[$i]->coll_id)->get();
                for($j=0;$j<sizeof($collage_admin);$j++){
                    $user=User::find($collage_admin[$j]->admin_id);
                    $user->delete();
                }
                $sections=Section::where('collage_id',$collages[$i]->coll_id)->get();
                for($j=0;$j<sizeof($sections);$j++){
                    $sec_admin=SectionAdmin::where('section_id',$sections[$j]->sec_id)->get();
                    for ($k=0;$k<sizeof($sec_admin);$k++){
                        $user=User::find($sec_admin[$k]->admin_id);
                        $user->delete();
                    }
                    for ($t=0;$t<sizeof($sections);$t++){
                        $classes=Classes::where('section_id',$sections[$t]->sec_id)->get();
                        for($d=0;$d<sizeof($classes);$d++){
                            $class_admin=ClassesAdmin::where('class_id',$classes[$d]->class_id)->get();
                            for($m=0;$m<sizeof($class_admin);$m++){
                                $user=User::find($class_admin[$m]->admin_id);
                                $user->delete();
                            }
                        }
                    }
                }
            }
            $university->delete();
            return response($myarray1);
        }
    }


    protected function create(Request $request)
    {
        $university = new University();
        $university->name = $request->name;
        $university->address = $request->address;
        if ($request->hasFile('image')) {
            $imageName = Str::random() . '.' . $request->image->getClientOriginalExtension();
            Storage::disk('public')->putFileAs('images', $request->image, $imageName);
            $request->image = $imageName;
        }
        $university->image = $request->image;
        $university->save();
        return response()->json($university);
    }
    protected function update($id, Request $request)
    {

        $university = University::find($id);
        if (!$university) {
            $array1 = [];
            return response($array1);
        } else {
            $user = JWTAuth::parseToken()->authenticate();
            if ($user->jobtype == "مسؤول جامعة") {
                $myuniversity = UniversityAdmin::where('admin_id', $user->id)->get()->first();
                if ($myuniversity != null && $myuniversity->university_id == $id) {
                    $university->fill($request->post())->update();
                    if ($request->hasFile('image')) {
                        if ($university->image) {
                            $exist =  Storage::disk('public')->exists("images/{$university->img}");
                            if ($exist) {
                                Storage::disk('public')->delete("images/{$university->img}");
                            }
                        }
                    }



                    if ($request->hasFile('image')) {
                        $imageName = Str::random() . '.' . $request->image->getClientOriginalExtension();
                        Storage::disk('public')->putFileAs('images', $request->image, $imageName);
                        $university->image = $imageName;
                    }

                    $university->save();
                    $array2 = [
                        'data' => $university,
                        'msg' => "تم التعديل بنجاح"
                    ];
                    return response($array2);
                } else {
                    $array2 = [
                        'msg' => "Unuthenticated"
                    ];
                    return response($array2);
                }
            } else if ($user->jobtype == "مسؤول عام") {
                $university->fill($request->post())->update();
                if ($request->hasFile('image')) {
                    if ($university->image) {
                        $exist =  Storage::disk('public')->exists("images/{$university->img}");
                        if ($exist) {
                            Storage::disk('public')->delete("images/{$university->img}");
                        }
                    }
                }



                if ($request->hasFile('image')) {
                    $imageName = Str::random() . '.' . $request->image->getClientOriginalExtension();
                    Storage::disk('public')->putFileAs('images', $request->image, $imageName);
                    $university->image = $imageName;
                }

                $university->save();
                $array2 = [
                    'data' => $university,
                    'msg' => "تم التعديل بنجاح"
                ];
                return response($array2);
            } else {
                $array2 = ['msg' => "Unuthenticated"];
                return response($array2);
            }
        }
    }
    protected function search($name)
    {
        $university = University::where('name', $name)->get();
        if (!$university) {
            $array = [];
            return response($array);
        }
        return response($university);
    }
}
