<?php

namespace App\Http\Controllers;

use App\Models\Classes;
use App\Models\ClassesAdmin;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Models\Collage;
use App\Models\University;
use App\Models\CollageAdmin;
use App\Models\Section;
use App\Models\SectionAdmin;
use App\Models\UniversityAdmin;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class CollageControllers extends Controller
{
    public function showAllCollages($id)
    {
        $collage = Collage::where('university_id', $id)->get();
        if (sizeof($collage) == 0) {

            $data = [];

            return response($data);
        }

        return response($collage);
    }
    public function showSearchCollages($name)
    {
        $university = University::where('name', $name)->get()->first();
        $collage = Collage::where('university_id', $university->id)->get();
        if (sizeof($collage) == 0) {

            $data = [];

            return response($data);
        }

        return response($collage);
    }
    public function showCollage($coll_id)
    {
        $collage = Collage::find($coll_id);
        if (!$collage) {
            $msg = [];
            return response($msg);
        }
        return response($collage);
    }
    protected function destroy($id)
    {
        $collage = Collage::find($id);

        if (!$collage) {
            $myarray1 = [];
            return response($myarray1);
        } else {
            $userr = JWTAuth::parseToken()->authenticate();
            if ($userr->jobtype == "مسؤول جامعة") {
                $myuniversity = UniversityAdmin::where('admin_id', $userr->id)->get()->first();
                if ($myuniversity != null && $myuniversity->university_id == $collage->university_id) {
                    $myarray1 = [
                        'message' => "تم حذف الكلية"
                    ];
                    $coll_admin=CollageAdmin::where('collage_id',$id)->get();
                    for ($i=0;$i<sizeof($coll_admin);$i++){
                        $user=User::find($coll_admin[$i]->admin_id);
                        $user->delete();
                    }
                    $coll_admin=CollageAdmin::where('collage_id',$id)->get();
                    for ($i=0;$i<sizeof($coll_admin);$i++){
                        $user=User::find($coll_admin[$i]->admin_id);
                        $user->delete();
                    }
                    $sections=Section::where('collage_id',$id)->get();
                    for($i=0;$i<sizeof($sections);$i++){
                        $section_admin=SectionAdmin::where('section_id',$sections[$i]->sec_id)->get();
                        for($j=0;$j<sizeof($section_admin);$j++){
                            $user=User::find($section_admin[$j]->admin_id);
                            $user->delete();
                        }
                        $classes=Classes::where('section_id',$sections[$i]->sec_id)->get();
                        for($t=0;$t<sizeof($classes);$t++){
                            $class_admin=ClassesAdmin::where('class_id',$classes[$t]->class_id)->get();
                            for($l=0;$l<sizeof($class_admin);$l++){
                                $user=User::find($class_admin[$l]->admin_id);
                                $user->delete();
                            }
                        }
                    }
                    $collage->delete();
                    return response($myarray1);
                } else {
                    $myarray1 = [
                        'message' => "Unuthenticated"
                    ];
                    return response($myarray1);
                }
            } else if ($userr->jobtype == "مسؤول عام") {
                $myarray1 = [
                    'message' => "تم حذف الكلية"
                ];
                $coll_admin=CollageAdmin::where('collage_id',$id)->get();
                    for ($i=0;$i<sizeof($coll_admin);$i++){
                        $user=User::find($coll_admin[$i]->admin_id);
                        $user->delete();
                    }
                    $sections=Section::where('collage_id',$id)->get();
                    for($i=0;$i<sizeof($sections);$i++){
                        $section_admin=SectionAdmin::where('section_id',$sections[$i]->sec_id)->get();
                        for($j=0;$j<sizeof($section_admin);$j++){
                            $user=User::find($section_admin[$j]->admin_id);
                            $user->delete();
                        }
                        $classes=Classes::where('section_id',$sections[$i]->sec_id)->get();
                        for($t=0;$t<sizeof($classes);$t++){
                            $class_admin=ClassesAdmin::where('class_id',$classes[$t]->class_id)->get();
                            for($l=0;$l<sizeof($class_admin);$l++){
                                $user=User::find($class_admin[$l]->admin_id);
                                $user->delete();
                            }
                        }
                    }
                $collage->delete();
                return response($myarray1);
            } else {
                $myarray1 = [
                    'message' => "Unuthenticated"
                ];
                return response($myarray1);
            }
        }
    }
    protected function create(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        if ($user->jobtype == "مسؤول عام") {
            $collage = new Collage();
            $collage->coll_name = $request->coll_name;
            $collage->coll_address = $request->coll_address;
            $collage->university_id = $request->university_id;
            if ($request->hasFile('image')) {
                $imageName = Str::random() . '.' . $request->image->getClientOriginalExtension();
                Storage::disk('public')->putFileAs('images', $request->image, $imageName);
                $request->image = $imageName;
            }

            $collage->coll_image = $request->image;
            $collage->save();
            return response($collage);
        } else if ($user->jobtype == "مسؤول جامعة") {
            $myuniversity =  UniversityAdmin::where('admin_id', $user->id);
            if ($myuniversity != null && $myuniversity->university_id == $request->university_id) {
                $collage = new Collage();
                $collage->coll_name = $request->coll_name;
                $collage->coll_address = $request->coll_address;
                $collage->university_id = $request->university_id;
                if ($request->hasFile('image')) {
                    $imageName = Str::random() . '.' . $request->image->getClientOriginalExtension();
                    Storage::disk('public')->putFileAs('images', $request->image, $imageName);
                    $request->image = $imageName;
                }

                $collage->coll_image = $request->image;
                $collage->save();
                return response($collage);
            } else {
                $mssg = ['msg' => "Unuthenticated"];
                return response($mssg);
            }
        } else {
            $mssg = ['msg' => "Unuthenticated"];
            return response($mssg);
        }
    }
    protected function update($id, Request $request)
    {
        $collage = Collage::find($id);
        if (!$collage) {
            $msg = [];
            return response($msg);
        } else {
            $user = JWTAuth::parseToken()->authenticate();
            if ($user->jobtype == "مسؤول عام") {
                $collage->fill($request->post())->update();
                if ($request->hasFile('image')) {
                    if ($collage->coll_image) {
                        $exist =  Storage::disk('public')->exists("images/{$collage->coll_image}");
                        if ($exist) {
                            Storage::disk('public')->delete("images/{$collage->iamge}");
                        }
                    }
                }



                if ($request->hasFile('image')) {
                    $imageName = Str::random() . '.' . $request->image->getClientOriginalExtension();
                    Storage::disk('public')->putFileAs('images', $request->image, $imageName);
                    $collage->coll_image = $imageName;
                }

                $collage->save();
                $msg = [
                    'data' => $collage,
                    'msg' => "تم التعديل بنجاح"
                ];
                return response($msg);
            } else if ($user->jobtype == "مسؤول جامعة") {
                $myuniversity = UniversityAdmin::where('admin_id', $user->id)->get()->first();
                if ($myuniversity != null && $myuniversity->university_id == $collage->university_id) {
                    $collage->fill($request->post())->update();
                    if ($request->hasFile('image')) {
                        if ($collage->coll_image) {
                            $exist =  Storage::disk('public')->exists("images/{$collage->coll_image}");
                            if ($exist) {
                                Storage::disk('public')->delete("images/{$collage->iamge}");
                            }
                        }
                    }



                    if ($request->hasFile('image')) {
                        $imageName = Str::random() . '.' . $request->image->getClientOriginalExtension();
                        Storage::disk('public')->putFileAs('images', $request->image, $imageName);
                        $collage->coll_image = $imageName;
                    }

                    $collage->save();
                    $msg = [
                        'data' => $collage,
                        'msg' => "تم التعديل بنجاح"
                    ];
                    return response($msg);
                } else {
                    $msg = ['msg' => "Unuthenticated"];
                    return response($msg);
                }
            } else if ($user->jobtype == "مسؤول كلية") {
                $mycollage = CollageAdmin::where('collage_id', $id)->get()->first();
                if ($mycollage->admin_id == $user->id) {
                    $collage->fill($request->post())->update();
                    $collage->fill($request->post())->update();
                    if ($request->hasFile('image')) {
                        if ($collage->coll_image) {
                            $exist =  Storage::disk('public')->exists("images/{$collage->coll_image}");
                            if ($exist) {
                                Storage::disk('public')->delete("images/{$collage->iamge}");
                            }
                        }
                    }



                    if ($request->hasFile('image')) {
                        $imageName = Str::random() . '.' . $request->image->getClientOriginalExtension();
                        Storage::disk('public')->putFileAs('images', $request->image, $imageName);
                        $collage->coll_image = $imageName;
                    }

                    $collage->save();
                    $msg = [
                        'data' => $collage,
                        'msg' => "تم التعديل بنجاح"
                    ];
                    return response($msg);
                } else {
                    $msg = ['msg' => "Unuthenticated"];
                    return response($msg);
                }
            }
        }
    }
    protected function search($name)
    {
        $collage = Collage::where('coll_name', $name)->get();
        if (sizeof($collage) == 0) {
            $msg = [
                'data' => null,
                'msg' => "لم يتم العثور على نتائج"
            ];
            return response($msg);
        } else {
            return response($collage);
        }
    }
}
