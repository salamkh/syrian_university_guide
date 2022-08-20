<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Teacher;
use App\Http\Controllers\TeacherCertificateController;
use App\Http\Controllers\WorkExperianceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;

class TeacherController extends Controller
{
    public function getAllTeachers()
    {
        $taechers = Teacher::select('teach_id')->get();
        $users = [];
        for ($count = 0; $count < sizeof($taechers); $count++) {
            $user = User::find($taechers[$count]->teach_id);
            $cer = new TeacherCertificateController();
            $certifications = $cer->index($taechers[$count]->teach_id);

            $exp = new WorkExperianceController();
            $experiances = $exp->index($taechers[$count]->teach_id);

            $users[$count] = $user;
        }
        return $users;
    }

    public function showTeacher($id)
    {
        
        $user = User::find($id);
        $cer = new TeacherCertificateController();
        $certifications = $cer->index($id);

        $exp = new WorkExperianceController();
        $experiances = $exp->index($id);
        $array = [
            'user' => $user,
            'certifications' => $certifications,
            'experiances' => $experiances
        ];
        return response($user);
    }

    public function addTeacher(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|min:6',
            'birthdate' => 'date',
            'image' => 'file',
            'phone_number' => 'digits:10',
            'address' => 'string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        if ($request->hasFile('image')) {
            $imageName = Str::random() . '.' . $request->image->getClientOriginalExtension();
            Storage::disk('public')->putFileAs('images', $request->image, $imageName);
            $request->image = $imageName;
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'jobtype' => "أستاذ",
            'birthdate' => $request->birthdate,
            'phone_number' => $request->phone_number,
            'address' => $request->address,
            'image' => $request->image,
        ]);

        $taecher = new Teacher();
        $taecher->teach_id = $user->id;
        $taecher->save();

        return response([
            'message' => 'User successfully registered',
            'user' => $user
        ], 201);
    }

    public function userProfile()
    {
        $users = auth()->user();

        $cer = new TeacherCertificateController();
        $certifications = $cer->index($users->id);

        $exp = new WorkExperianceController();
        $experiances = $exp->index($users->id);

        $array = [
            'user' => $users,
            'certifications' => $certifications,
            'experiances' => $experiances
        ];
        return response($users);
    }

    public function update(Request $request, $id)
    {
        $user_auth = JWTAuth::parseToken()->authenticate();
        if ($user_auth->jobtype=="أستاذ" && $user_auth->id==$id){
        $user = User::find($id);
        if (!$user) {
            $myarray1 = [];
            return response($myarray1, 404);
        }
        else{
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
    }
    }

    public function searchByName(Request $request)
    {

        $users = User::where('name', $request->name)->get();
        $myarray1 = [];
        if (!$users || sizeof($users) == 0) {
            return response($myarray1, 404);
        }

        for ($count = 0; $count < sizeof($users); $count++) {
            $certifications = new TeacherCertificateController();
            $certifications->index($users[$count]->id);

            $experiances = new WorkExperianceController();
            $experiances->index($users[$count]->id);

            $users[$count]->certifications = $certifications;
            $users[$count]->experiances = $experiances;
        }
        if ($users) {
            return response($users);
        }
    }

    public function destroy($id)
    {
       $user_auth = JWTAuth::parseToken()->authenticate();
       if ($user_auth->jobtype=="أستاذ" && $user_auth->id==$id){
        $user = User::find($id);
        if (!$user) {
            $myarray1 = [];
            return response($myarray1, 404);
        }
        else{
        $user->delete($id);

        $myarray2 = [
            'data' => null,
            'message' => "user deleted"
        ];
        if ($user) {
            return response($myarray2, 200);
        }
    }
    }
}
}
