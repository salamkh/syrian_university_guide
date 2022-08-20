<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Models\User;
use App\Models\Administartor;
use App\Models\CollageAdmin;
use App\Models\UniversityAdmin;
use App\Models\ClassesAdmin;
use App\Models\SectionAdmin;
use App\Models\Collage;
use App\Models\University;
use App\Models\Classes;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Models\Section;
use Illuminate\Support\Facades\Validator;

use App\Http\Controllers\Controller;


class UserController extends Controller
{
    public function index()
    {
        $users = User::select('id', 'name', 'address', 'birthdate', 'jobtype', 'image', 'phone_number')->where('jobtype',"مسؤول عام")->orwhere('jobtype',"مسؤول جامعة")->orwhere('jobtype',"مسؤول كلية")->orwhere('jobtype',"مسؤول قسم")->orwhere('jobtype',"مسؤول شعبة")->get();
        for ($count = 0; $count < sizeof($users); $count++) {
            if ($users[$count]->jobtype == 'مسؤول جامعة') {
                $unAdmin = UniversityAdmin::where('admin_id', $users[$count]->id)->get()->first();
                $un = University::find($unAdmin->university_id);
                $users[$count]->university = $un->name;
            } else if ($users[$count]->jobtype == 'مسؤول كلية') {
                $collAdmin = CollageAdmin::where('admin_id', $users[$count]->id)->get()->first();
                $coll = Collage::find($collAdmin->collage_id);
                $un = University::find($coll->university_id);
                $users[$count]->university = $un->name;
                $users[$count]->collage = $coll->coll_name;
            } else if ($users[$count]->jobtype == 'مسؤول قسم') {
                $secAdmin = SectionAdmin::where('admin_id', $users[$count]->id)->get()->first();
                $sec = Section::find($secAdmin->section_id);
                $coll = Collage::find($sec->collage_id);
                $un = University::find($coll->university_id);
                $users[$count]->university = $un->name;
                $users[$count]->collage = $coll->coll_name;
                $users[$count]->section = $sec->sec_name;
            } else if ($users[$count]->jobtype == 'مسؤول شعبة') {
                $classAdmin = ClassesAdmin::where('admin_id', $users[$count]->id)->get()->first();
                $clas = Classes::find($classAdmin->class_id);
                $sec = Section::find($clas->section_id);
                $coll = Collage::find($sec->collage_id);
                $un = University::find($coll->university_id);
                $users[$count]->university = $un->name;
                $users[$count]->collage = $coll->coll_name;
                $users[$count]->section = $sec->sec_name;
                $users[$count]->class = $clas->class_name;
            }
        }
        return response($users);
    }
    public function show($id)
    {
        $users = User::select('id', 'name', 'address', 'birthdate', 'jobtype', 'image', 'email', 'phone_number')->where('id', $id)->get()->first();
        if ($users->jobtype == 'مسؤول جامعة') {
            $unAdmin = UniversityAdmin::where('admin_id', $users->id)->get()->first();
            $un = University::find($unAdmin->university_id);
            $users->university = $un->name;
        } else if ($users->jobtype == 'مسؤول كلية') {
            $collAdmin = CollageAdmin::where('admin_id', $users[$count]->id)->get()->first();
            $coll = Collage::find($collAdmin->collage_id);
            $un = University::find($coll->university_id);
            $users->university = $un->name;
            $users->collage = $coll->coll_name;
        } else if ($users->jobtype == 'مسؤول قسم') {
            $secAdmin = SectionAdmin::where('admin_id', $users->id)->get()->first();
            $sec = Section::find($secAdmin->section_id);
            $coll = Collage::find($sec->collage_id);
            $un = University::find($coll->university_id);
            $users->university = $un->name;
            $users->collage = $coll->coll_name;
            $users->section = $sec->sec_name;
        } else if ($users->jobtype == 'مسؤول شعبة') {
            $classAdmin = ClassesAdmin::where('admin_id', $users->id)->get()->first();
            $clas = Classes::find($classAdmin->id);
            $sec = Section::find($clas->section_id);
            $coll = Collage::find($sec->collage_id);
            $un = University::find($coll->university_id);
            $users->university = $un->name;
            $users->collage = $coll->coll_name;
            $users->section = $sec->sec_name;
            $users->class = $clas->class_name;
        }
        return response($users);
    }

    public function profile($id)
    {
        $users = User::select('id', 'name', 'address', 'birthdate', 'jobtype', 'image', 'email', 'phone_number')->where('id', $id)->get()->first();
        if ($users->jobtype == 'مسؤول جامعة') {
            $unAdmin = UniversityAdmin::where('admin_id', $users->id)->get()->first();
            $un = University::find($unAdmin->university_id);
            $users->university = $un->name;
        } else if ($users->jobtype == 'مسؤول كلية') {
            $collAdmin = CollageAdmin::where('admin_id', $users[$count]->id)->get()->first();
            $coll = Collage::find($collAdmin->collage_id);
            $un = University::find($coll->university_id);
            $users->university = $un->name;
            $users->collage = $coll->coll_name;
        } else if ($users->jobtype == 'مسؤول قسم') {
            $secAdmin = SectionAdmin::where('admin_id', $users->id)->get()->first();
            $sec = Section::find($secAdmin->section_id);
            $coll = Collage::find($sec->collage_id);
            $un = University::find($coll->university_id);
            $users->university = $un->name;
            $users->collage = $coll->coll_name;
            $users->section = $sec->sec_name;
        } else if ($users->jobtype == 'مسؤول شعبة') {
            $classAdmin = ClassesAdmin::where('admin_id', $users->id)->get()->first();
            $clas = Classes::find($classAdmin->id);
            $sec = Section::find($clas->section_id);
            $coll = Collage::find($sec->collage_id);
            $un = University::find($coll->university_id);
            $users->university = $un->name;
            $users->collage = $coll->coll_name;
            $users->section = $sec->sec_name;
            $users->class = $clas->class_name;
        }
        return response($users);
    }

    public function destroy($id)
    {
        $user = User::find($id);
        $myarray1 = [];
        if (!$user) {
            return response($myarray1, 404);
        }
        if ($user->jobtype == 'مسؤول جامعة') {
            $un_admin = UniversityAdmin::where('admin_id', $user->id);
            $un_admin->delete();
        } else if ($user->jobtype == 'مسؤول كلية') {
            $coll_admin = CollageAdmin::where('admin_id', $user->id);
            $coll_admin->delete();
        } else if ($user->jobtype == 'مسؤول قسم') {
            $sec_admin = SectionAdmin::where('admin_id', $user->id);
            $sec_admin->delete();
        } else if ($user->jobtype == 'مسؤول شعبة') {
            $class_admin = ClassesAdmin::where('admin_id', $user->id);
            $class_admin->delete();
        }

        $user->delete($id);
        $myarray2 = [
            'data' => null,
            'message' => "user deleted"
        ];
        if ($user) {
            return response($myarray2, 200);
        }
    }
    public function update(Request $request, $id)
    {
        $user = User::find($id);
        $myarray1 = [];
        if (!$user) {
            return response($myarray1, 404);
        }

        $user->fill($request->post())->update();

        if ($request->hasFile('image')) {
            if ($user->image) {
                $exist =  Storage::disk('public')->exists("images/{$user->img}");
                if ($exist) {
                    Storage::disk('public')->delete("images/{$user->img}");
                }
            }
        }



        if ($request->hasFile('image')) {
            $imageName = Str::random() . '.' . $request->image->getClientOriginalExtension();
            Storage::disk('public')->putFileAs('images', $request->image, $imageName);
            $user->image = $imageName;
        }

        if ($request->password) {
            $user->password = bcrypt($request->password);
        }
        $user->save();


        return response()->json([
            'message' => 'updated successfuly'
        ]);
    }

    public function searchByName(Request $request)
    {

        $users = User::where('name', $request->name)->get();
        $myarray1 = [];
        if (!$users || sizeof($users) == 0) {
            return response($myarray1, 404);
        }


        for ($count = 0; $count < sizeof($users); $count++) {
            if ($users[$count]->jobtype == 'مسؤول جامعة') {
                $unAdmin = UniversityAdmin::where('admin_id', $users[$count]->id)->get()->first();
                $un = University::find($unAdmin->university_id);
                $users[$count]->university = $un->name;
            } else if ($users[$count]->jobtype == 'مسؤول كلية') {
                $collAdmin = CollageAdmin::where('admin_id', $users[$count]->id)->get()->first();
                $coll = Collage::find($collAdmin->collage_id);
                $un = University::find($coll->university_id);
                $users[$count]->university = $un->name;
                $users[$count]->collage = $coll->coll_name;
            } else if ($users[$count]->jobtype == 'مسؤول قسم') {
                $secAdmin = SectionAdmin::where('admin_id', $users[$count]->id)->get()->first();
                $sec = Section::find($secAdmin->section_id);
                $coll = Collage::find($sec->collage_id);
                $un = University::find($coll->university_id);
                $users[$count]->university = $un->name;
                $users[$count]->collage = $coll->coll_name;
                $users[$count]->section = $sec->sec_name;
            } else if ($users[$count]->jobtype == 'مسؤول شعبة') {
                $classAdmin = ClassesAdmin::where('admin_id', $users[$count]->id)->get()->first();
                $clas = Classes::find($classAdmin->id);
                $sec = Section::find($clas->section_id);
                $coll = Collage::find($sec->collage_id);
                $un = University::find($coll->university_id);
                $users[$count]->university = $un->name;
                $users[$count]->collage = $coll->coll_name;
                $users[$count]->section = $sec->sec_name;
                $users[$count]->class = $clas->class_name;
            }
        }
        if ($users) {
            return response($users);
        }
    }
}
