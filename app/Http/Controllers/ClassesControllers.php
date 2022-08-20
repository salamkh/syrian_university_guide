<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\Models\Classes;
use App\Models\ClassesAdmin;
use App\Models\SectionAdmin;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

class ClassesControllers extends Controller
{
    public function showAllClasses($sectionId)
    {
        $class = Classes::where('section_id', $sectionId)->get();
        $myarray1 = [];
        if (sizeof($class) == 0) {
            return response($class, 404);
        }
        $myarray2 = [
            'data' => $class,
            'message' => "class found"
        ];
        if ($class) {
            return response($class, 200);
        }
    }
    public function add(Request $request, $sectionId)
    {
        $validator = Validator::make($request->all(), [
            'class_name' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()($validator->errors(), 400);
        }
        $user = JWTAuth::parseToken()->authenticate();
        if ($user->jobtype == "مسؤول عام") {
            $class = new Classes();
            $class->class_name = $request->class_name;
            $class->section_id = $sectionId;
            $class->save();

            return response($class, 201);
        } else if ($user->jobtype == "مسؤول قسم") {
            $mysection = SectionAdmin::where('admin_id', $user->id)->get()->first();
            if ($mysection != null && $mysection->section_id == $sectionId) {
                $class = new Classes();
                $class->class_name = $request->class_name;
                $class->section_id = $sectionId;
                $class->save();

                return response($class, 201);
            } else {
                $message = ['msg' => "Unuthenticated"];
                return response($message);
            }
        } else {
            $message = ['msg' => "Unuthenticated"];
            return response($message);
        }
    }
    public function showClass($id)
    {
        $class = Classes::find($id);
        if (!$class) {
            $msg = [];
            return response($msg);
        }
        return response($class);
    }

    public function destroy($id)
    {
        $class = Classes::find($id);
        $myarray1 = [];
        if (!$class) {
            return response($myarray1, 404);
        } else {
            $userr = JWTAuth::parseToken()->authenticate();
            if ($userr->jobtype == "مسؤول عام") {
                $class_admin=ClassesAdmin::where('class_id',$id)->get();
                for($i=0;$i<sizeof($class_admin);$i++){
                    $user=User::find($class_admin[$i]->admin_id);
                    $user->delete();
                }
                $class->delete($id);
                $myarray2 = [
                    'data' => null,
                    'message' => "تمت عملية الحذف بنجاح"
                ];
                return response($myarray2);
            } else if ($userr->jobtype == "مسؤول قسم") {
                $mysection = SectionAdmin::where('admin_id', $userr->id)->get()->first();
                if ($mysection != null && $mysection->section_id == $class->section_id) {
                    $class_admin=ClassesAdmin::where('class_id',$id)->get();
                for($i=0;$i<sizeof($class_admin);$i++){
                    $user=User::find($class_admin[$i]->admin_id);
                    $user->delete();
                }
                    $class->delete($id);
                    $myarray2 = [
                        'message' => "تمت عملية الحذف بنجاح"
                    ];
                    return response($myarray2);
                } else {
                    $myarray2 = [
                        'message' => "Unuthenticated"
                    ];
                    return response($myarray2, 404);
                }
            } else {
                $myarray2 = [
                    'message' => "Unuthenticated"
                ];
                return response($myarray2, 404);
            }
        }
    }
    public function update(Request $request, $id)
    {
        $class = Classes::find($id);
        $myarray1 = [];
        if (!$class) {
            return response($myarray1, 404);
        } else {
            $user = JWTAuth::parseToken()->authenticate();
            if ($user->jobtype == "مسؤول عام") {
                $class->update($request->all());
                $myarray2 = [
                    'data' => $class,
                    'message' => "class updated"
                ];
                return response($class, 200);
            } else if ($user->jobtype == "مسؤول قسم") {
                $mysection = SectionAdmin::where('admin_id', $user->id)->get()->first();
                if ($mysection != null && $mysection->section_id == $class->section_id) {
                    $class->update($request->all());
                    $myarray2 = [
                        'data' => $class,
                        'message' => "class updated"
                    ];
                    return response($class, 200);
                } else {
                    $myarray2 = [
                        'message' => "Unuthenticated"
                    ];
                    return response($myarray2, 404);
                }
            } else if ($user->jobtype == "مسؤول شعبة") {
                $myclass = ClassesAdmin::where('admin_id', $user->id)->get()->first();
                if ($myclass != null && $myclass->class_id == $class->class_id) {
                    $class->update($request->all());
                    $myarray2 = [
                        'data' => $class,
                        'message' => "class updated"
                    ];
                    return response($class, 200);
                } else {
                    $myarray2 = [
                        'message' => "Unuthenticated"
                    ];
                    return response($class, 404);
                }
            } else {
                $myarray2 = [
                    'message' => "Unuthenticated"
                ];
                return response($class, 404);
            }
        }
    }
    public function search(Request $request)
    {
        $class = Classes::where('class_name', $request->class_name)->get();
        $myarray1 = [];
        if (sizeof($class) == 0) {
            return response($myarray1, 404);
        } else {
            $myarray2 = [
                'data' => $class,
                'message' => "class found"
            ];
            return response($class, 200);
        }
    }
}
