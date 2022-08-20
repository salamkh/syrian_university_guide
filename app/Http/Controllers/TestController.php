<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Models\University;
use App\Models\Test;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return University::select('id', 'name', 'image', 'address')->get();
    }



    public function store(Request $request)
    {    $test = new Test();
        $arr=$request->num;
        for($i=0;$i<sizeof($arr);$i++)
        {
            $test = new Test();
            $test->num = $arr[$i];
            $test->save();
        }

        
      

       
      }


    public function show(University $test)
    {
        return response()->json([
            'test' => $test
        ]);
    }

    /*
    public function update(Request $request, Test $test)
    {
        $request->validate([
            'name'=>'required',
            'img'=>'nullable'
        ]);
        $test->fill($request->post())->update();

        if($request->hasFile('img')){
            if($test->img){
                $exist=  Storage::disk('public')->exists("test/image/{$test->img}");
                if($exist){
                    Storage::disk('public')->delete("test/image/{$test->img}");
                }
            }
    
        }
    

        $imageName=Str::random().'.'. $request->img->getClientOriginalExtension();
        Storage::disk('public')->putFileAs('test/image',$request->img,$imageName);
        $test->img=$imageName;
        $test->save();
        return response()->json([
            'message'=>'updated successfuly'
        ]);
    }

   
    public function destroy(Test $test)
    {
        if($test->img){
            $exist=  Storage::disk('public')->exists("test/image/{$test->img}");
            if($exist){
                Storage::disk('public')->delete("test/image/{$test->img}");
            }
        }

        $test->delete();
        return response()->json([
            'message'=>'deleted successfuly'
        ]);

    }
    */
}
