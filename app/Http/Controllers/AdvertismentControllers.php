<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\Advertisment;
use App\Models\CollageAdmin;
use App\Models\UniversityAdmin;
use App\Models\University;
use App\Models\Collage;

class AdvertismentControllers extends Controller
{
    public function showAllAdvertismentInCollage($CollageId)
    {
        $adver = Advertisment::where('collage_id', $CollageId)->get();
        $data = [];
        if (!$adver) {
            return response($data);
        } else {

            return response($adver, 200);
        }
    }
    public function showAllAdvertismentInUniversity($UniversityId)
    {
        $adver = Advertisment::where('university_id', $UniversityId)->get();
        $university = University::where('id', $UniversityId)->get()->first();
        $data = [];
        if (!$adver) {
            return response($data, 404);
        } else {
            $adver->university = $university->name;
            return response($adver, 200);
        }
    }
    public function addToCollage($collageId, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'content' => 'required|string',

        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        $user = JWTAuth::parseToken()->authenticate();
        if ($user->jobtype == "مسؤول عام") {
            $adver = new Advertisment();
            $adver->content = $request->content;
            // $adver->publish_date = $request->publish_date;
            $adver->collage_id = $collageId;
            $adver->save();

            return response()->json([
                'message' => 'advertisment successfully added',
                'user' => $adver
            ], 201);
        } else if ($user->jobtype == "مسؤول كلية") {
            $mycollage = CollageAdmin::where('admin_id', $user->id)->get()->first();
            if ($mycollage != null && $mycollage->collage_id == $collageId) {
                $adver = new Advertisment();
                $adver->content = $request->content;
                $adver->publish_date = $request->publish_date;
                // $adver->collage_id = $collageId;
                $adver->save();

                return response()->json([
                    'message' => 'advertisment successfully added',
                    'user' => $adver
                ], 201);
            } else {
                $msg = ['msg' => "Unuthenticated"];
                return response($msg);
            }
        } else {
            $msg = ['msg' => "Unuthenticated"];
            return response($msg);
        }
    }
    public function addToUniversity($universityId, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'content' => 'required|string',

        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        $user = JWTAuth::parseToken()->authenticate();
        if ($user->jobtype == "مسؤول عام") {
            $adver = new Advertisment();
            $adver->content = $request->content;
            // $adver->publish_date = $request->publish_date;
            $adver->university_id = $universityId;
            $adver->save();

            return response()->json([
                'message' => 'advertisment successfully added',
                'user' => $adver
            ], 201);
        } else if ($user->jobtype == "مسؤول جامعة") {
            $myuniversity = UniversityAdmin::where('admin_id', $user->id)->get()->first();
            if ($myuniversity != null && $myuniversity->university_id == $universityId) {
                $adver = new Advertisment();
                $adver->content = $request->content;
                //  $adver->publish_date = $request->publish_date;
                $adver->university_id = $universityId;
                $adver->save();

                return response()->json([
                    'message' => 'advertisment successfully added',
                    'user' => $adver
                ], 201);
            } else {
                $msg = ['msg' => "Unuthenticated"];
                return response($msg);
            }
        } else {
            $msg = ['msg' => "Unuthenticated"];
            return response($msg);
        }
    }
    public function destroy($id)
    {
        $adver = Advertisment::find($id);
        $data = [];
        if (!$adver) {
            return response($data, 404);
        }
        $user = JWTAuth::parseToken()->authenticate();
        if ($user->jobtype == "مسؤول عام") {
            $adver->delete($id);
            $myarray2 = [
                'data' => null,
                'message' => "advertisment deleted"
            ];

            return response($myarray2, 200);
        } else {
            if ($adver->collage_id != null) {
                if ($user->jobtype == "مسؤول كلية") {
                    $mycollage = CollageAdmin::where('admin_id', $user->id)->get()->first();
                    if ($mycollage != null && $mycollage->collage_id == $adver->collage_id) {
                        $adver->delete($id);
                        $myarray2 = [
                            'data' => null,
                            'message' => "advertisment deleted"
                        ];

                        return response($myarray2, 200);
                    } else {
                        $msg = ['msg' => "Unuthenticated"];
                        return response($msg);
                    }
                } else {
                    $msg = ['msg' => "Unuthenticated"];
                    return response($msg);
                }
            } else if ($adver->university_id != null) {
                if ($user->jobtype == "مسؤول جامعة") {
                    $myuniversity = UniversityAdmin::where('admin_id', $user->id)->get()->first();
                    if ($myuniversity != null && $myuniversity->university_id == $adver->university_id) {
                        $adver->delete($id);
                        $myarray2 = [
                            'data' => null,
                            'message' => "advertisment deleted"
                        ];

                        return response($myarray2, 200);
                    } else {
                        $msg = ['msg' => "Unuthenticated"];
                        return response($msg);
                    }
                } else {
                    $msg = ['msg' => "Unuthenticated"];
                    return response($msg);
                }
            } else {
                $msg = ['msg' => "Unuthenticated"];
                return response($msg);
            }
        }
    }
    public function update($id, Request $request)
    {
        $adver = Advertisment::find($id);
        $data = [];
        if (!$adver) {
            return response($data, 404);
        }
        $user = JWTAuth::parseToken()->authenticate();
        if ($user->jobtype == "مسؤول عام") {
            $adver->update($request->all());

            $myarray2 = [
                'data' => $adver,
                'message' => "advertisment updated"
            ];

            return response($myarray2, 200);
        } else {
            if ($adver->collage_id != null) {
                if ($user->jobtype == "مسؤول كلية") {
                    $mycollage = CollageAdmin::where('admin_id', $user->id)->get()->first();
                    if ($mycollage != null && $mycollage->collage_id == $adver->collage_id) {
                        $adver->update($request->all());
                        $myarray2 = [
                            'data' => $adver,
                            'message' => "advertisment updated"
                        ];

                        return response($myarray2, 200);
                    } else {
                        $msg = ['msg' => "Unuthenticated"];
                        return response($msg);
                    }
                } else {
                    $msg = ['msg' => "Unuthenticated"];
                    return response($msg);
                }
            } else if ($adver->university_id != null) {
                if ($user->jobtype == "مسؤول جامعة") {
                    $myuniversity = UniversityAdmin::where('admin_id', $user->id)->get()->first();
                    if ($myuniversity != null && $myuniversity->university_id == $adver->university_id) {
                        $adver->update($request->all());

                        $myarray2 = [
                            'data' => $adver,
                            'message' => "advertisment updated"
                        ];

                        return response($myarray2, 200);
                    } else {
                        $msg = ['msg' => "Unuthenticated"];
                        return response($msg);
                    }
                } else {
                    $msg = ['msg' => "Unuthenticated"];
                    return response($msg);
                }
            } else {
                $msg = ['msg' => "Unuthenticated"];
                return response($msg);
            }
        }
    }


    public function showAdv($id)
    {
        $adv = Advertisment::find($id);
        if (!$adv) {
            $data=[];
            return response($data);
        }
        return response($adv);
    }
}
