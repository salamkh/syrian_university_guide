<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Administartor;
use App\Models\CollageAdmin;
use App\Models\UniversityAdmin;
use App\Models\ClassesAdmin;
use App\Models\SectionAdmin;
use App\Models\Collage;
use App\Models\University;
use App\Models\Classes;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
class AuthController extends Controller
{
 
    public function __construct() {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }
    public function login(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response($validator->errors(), 422);
           // return response(, 422);
        }

        if (! $token = auth()->attempt($validator->validated())) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user=auth()->user();
        $user->accessToken = $token;
        $user->expires_in=auth()->factory()->getTTL() * 60;
        $user->tokenType="bearer";
        return response($user);
    }

    public function forget(Request $request){
        $pass = User::select('password')->where('email',$request->email)->get()->first();
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password'=>$pass,
            
        ]);

        if ($validator->fails()) {
            return response($validator->errors(), 422);
           // return response(, 422);
        }

        if (! $token = auth()->attempt($validator->validated())) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        

        $user=auth()->user();
        $user->accessToken = $token;
        $user->expires_in=auth()->factory()->getTTL() * 60;
        $user->tokenType="bearer";
        return response($user);

    }


    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|min:6',
            'jobtype' => 'required',
            'birthdate'=>'date',
            'image'=>'file',
            'phone_number'=>'int',
            'address'=>'string',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }
        if($request->hasFile('image'))
        {
            $imageName=Str::random().'.'. $request->image->getClientOriginalExtension();
            Storage::disk('public')->putFileAs('images',$request->image,$imageName);
            $request->image=$imageName;
        }
        
        $user=User::create([
        'name'=>$request->name,
       'email'=>$request->email,
       'password'=>bcrypt($request->password),
       'jobtype'=>$request->jobtype,
       'birthdate'=>$request->birthdate,
       'phone_number'=>$request->phone_number,
       'address'=>$request->address,
       'image'=>$request->image,
       ]);
        $anemail=$request->email;
        $user = User::where('email',$anemail)->get()->first();
        $id=$user->id;

        if ($request->jobtype == 'مسؤول جامعة'){
        $admin = new UniversityAdmin ();
        $admin->admin_id = $id;
        $un=University::where('name',$request->university)->get()->first();
        $un_id = $un->id;
        $admin->university_id = $un_id;
        $admin->save();
        }
        else if ($request->jobtype == 'مسؤول كلية'){
            $admin = new CollageAdmin ();
            $admin->admin_id = $id;
            $un=University::where('name',$request->university)->get()->first();
            $un_id = $un->id;
            $coll=Collage::where('coll_name',$request->collage) -> where('university_id',$un_id)->get()->first();
            $coll_id = $coll->coll_id;
            $admin->collage_id = $coll_id;
            $admin->save();
            }
        else if ($request->jobtype == 'مسؤول قسم'){
            $admin = new SectionAdmin ();
            $admin->admin_id = $id;
            $un=University::where('name',$request->university)->get()->first();
            $un_id = $un->id;
            $coll=Collage::where('coll_name',$request->collage) -> where('university_id',$un_id)->get()->first();
            $coll_id = $coll->coll_id;
            $sec=Section::where('sec_name',$request->section)-> where('collage_id',$coll_id)->get()->first();
            $sec_id = $sec->sec_id;
            $admin->section_id = $sec_id;
            $admin->save();
            }    
        else if ($request->jobtype == 'مسؤول شعبة'){
            $admin = new ClassesAdmin ();
            $admin->admin_id = $id;
            $un=University::where('name',$request->university)->get()->first();
            $un_id = $un->id;
            $coll=Collage::where('coll_name',$request->collage)-> where('university_id',$un_id)->get()->first();
            $coll_id = $coll->coll_id;
            $sec=Section::where('sec_name',$request->section)-> where('collage_id',$coll_id)->get()->first();
            $sec_id = $sec->sec_id;
            $classs=Classes::where('class_name',$request->theClass)-> where('section_id',$sec_id)->get()->first();
            $class_id = $classs->class_id;
            $admin->class_id = $class_id;
            $admin->save();
            }

         return response()->json([
            'message' => 'User successfully registered',
            'user' => $user
        ], 201);
    }
 
    public function logout() {
        
        auth()->logout();

        return response()->json(['message' => 'User successfully signed out']);
    }

    public function refresh(Request $request) {
        $user=auth()->user();
        $user->expires_in=auth()->factory()->getTTL() * 60;
        $user->accessToken=$request->accessToken;
        $user->tokenType="bearer";
        return response($user);
          }


    public function userProfile() {
        $users=auth()->user();
        if ($users->jobtype == 'مسؤول جامعة'){
            $unAdmin=UniversityAdmin::where('admin_id',$users->id)->get()->first();
            $un=University::find($unAdmin->university_id);
            $users->university=$un->name;
        }
    else if ($users->jobtype == 'مسؤول كلية'){
            $collAdmin=CollageAdmin::where('admin_id',$users[$count]->id)->get()->first();
            $coll=Collage::find($collAdmin->collage_id);
            $un=University::find($coll->university_id);
            $users->university=$un->name;
            $users->collage=$coll->coll_name;
        }
    else if ($users->jobtype == 'مسؤول قسم'){
            $secAdmin=SectionAdmin::where('admin_id',$users->id)->get()->first();
            $sec=Section::find($secAdmin->section_id);
            $coll=Collage::find($sec->collage_id);
            $un=University::find($coll->university_id);
            $users->university=$un->name;
            $users->collage=$coll->coll_name;
            $users->section=$sec->sec_name;
        }    
    else if ($users->jobtype == 'مسؤول شعبة'){
            $classAdmin=ClassesAdmin::where('admin_id',$users->id)->get()->first();
            $clas=Classes::find($classAdmin->id);
            $sec=Section::find($clas->section_id);
            $coll=Collage::find($sec->collage_id);
            $un=University::find($coll->university_id);
            $users->university=$un->name;
            $users->collage=$coll->coll_name;
            $users->section=$sec->sec_name;
            $users->class=$clas->class_name;
        }
     



        return response($users);
    }

    public function createNewToken($token){
        $user=auth()->user();
        $user->expires_in=auth()->factory()->getTTL() * 60;
        $user->accessToken=$token;
        $user->tokenType="bearer";
        return response($user);
    }
   
}