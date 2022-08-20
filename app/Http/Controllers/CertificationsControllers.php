<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\Models\certification;
use App\Models\Donor;
use App\Models\Collage;
use App\Models\Section;
use App\Models\Classes;
use App\Models\CollageAdmin;
use App\Models\SectionAdmin;
use App\Models\ClassesAdmin;
use App\Models\University;
use Tymon\JWTAuth\Facades\JWTAuth;

class CertificationsControllers extends Controller
{
    public function showAllCertificationsInCollage($CollageId, Request $request)
    {
        $donor = Donor::where('collage_id', $CollageId)->where('section_id', NULL)->get()->first();

        if (!$donor) {
            $data =[];
            return response($data, 404);
        }
        $don = $donor->don_id;
        $cer = Certification::where('donor_id', $don)->get();
        $myarray2 = [];
        for ($i = 0; $i < sizeof($cer); $i++) {
            $collageName = (Collage::where('coll_id', $donor->collage_id)->get()->first())->coll_name;
            $universityName = (University::where('id', $donor->university_id)->get()->first())->name;
            $myarray2[$i] = [
                'certificationType' => $cer[$i]->cer_type,
                'collage' => $collageName,
                'university' => $universityName,
                'cer_id' => $cer[$i]->cer_id
            ];
        }
        return response($myarray2);
    }
    public function showAllCertificationsInSection($SectionId, Request $request)
    {
        $donor = Donor::where('section_id', $SectionId)->where('class_id', NULL)->get()->first();
        $myarray1 = [];
        if (!$donor) {
            return response($myarray1, 404);
        }
        $don = $donor->don_id;
        $cer = Certification::where('donor_id', $don)->get();
        for ($i = 0; $i < sizeof($cer); $i++) {
            $sectionName = (Section::where('sec_id', $donor->section_id)->get()->first())->sec_name;
            $collageName = (Collage::where('coll_id', $donor->collage_id)->get()->first())->coll_name;
            $universityName = (University::where('id', $donor->university_id)->get()->first())->name;
            $myarray2[$i] = [
                'certificationType' => $cer[$i]->cer_type,
                'section' => $sectionName,
                'collage' => $collageName,
                'university' => $universityName,
                'cer_id' => $cer[$i]->cer_id
            ];
        }
        return response($myarray2);
    }
    public function showAllCertificationsInClass($ClassId, Request $request)
    {
        $donor = Donor::where('class_id', $ClassId)->get()->first();
        $myarray1 = [];
        if (!$donor) {
            return response($myarray1, 404);
        }
        $don = $donor->don_id;
        $cer = Certification::where('donor_id', $don)->get();
        for ($i = 0; $i < sizeof($cer); $i = $i + 1) {
            $className = (Classes::where('class_id', $donor->class_id)->get()->first())->class_name;
            $sectionName = (Section::where('sec_id', $donor->section_id)->get()->first())->sec_name;
            $collageName = (Collage::where('coll_id', $donor->collage_id)->get()->first())->coll_name;
            $universityName = (University::where('id', $donor->university_id)->get()->first())->name;
            $myarray2[$i] = [
                'certificationType' => $cer[$i]->cer_type,
                'class' => $className,
                'section' => $sectionName,
                'collage' => $collageName,
                'university' => $universityName,
                'cer_id' => $cer[$i]->cer_id
            ];
        }
        return response($myarray2);
    }
    public function addCertificationsToCollage($CollageId, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cer_type' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        $user = JWTAuth::parseToken()->authenticate();
        if ($user->jobtype == "مسؤول عام") {
            $cer = new Certification();
            $cer->cer_type = $request->cer_type;

            $donor = Donor::where('collage_id', $CollageId)->where('section_id', NULL)->get()->first();
            if ($donor) {
                $cer->donor_id = $donor->don_id;
                $cer->save();
                return response($cer, 201);
            } else {
                $coll = Collage::where('coll_id', $CollageId)->get()->first();
                if ($coll) {
                    $don = new Donor();
                    $don->collage_id = $CollageId;
                    $don->university_id = $coll->university_id;
                    $don->save();
                    $cer->donor_id = $don->don_id;
                    $cer->save();
                    return response($cer, 201);
                }
            }
        } else if ($user->jobtype == "مسؤول كلية") {
            $mycollage = CollageAdmin::where('admin_id', $user->id)->get()->first();
            if ($mycollage != null && $mycollage->collage_id == $CollageId) {
                $cer = new Certification();
                $cer->cer_type = $request->cer_type;

                $donor = Donor::where('collage_id', $CollageId)->where('section_id', NULL)->get()->first();
                if ($donor) {
                    $cer->donor_id = $donor->don_id;
                    $cer->save();
                    return response($cer, 201);
                } else {
                    $coll = Collage::where('coll_id', $CollageId)->get()->first();
                    if ($coll) {
                        $don = new Donor();
                        $don->collage_id = $CollageId;
                        $don->university_id = $coll->university_id;
                        $don->save();
                        $cer->donor_id = $don->don_id;
                        $cer->save();
                        return response($cer, 201);
                    }
                }
            } else {
                $msg = ['msg' => "Unthenticated"];
                return response($msg);
            }
        } else {
            $msg = ['msg' => "Unuthenticated"];
            return response($msg);
        }
    }
    public function addCertificationsToSection($SectionId, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cer_type' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        $user = JWTAuth::parseToken()->authenticate();
        if ($user->jobtype == "مسؤول عام") {
            $cer = new Certification();
            $cer->cer_type = $request->cer_type;

            $donor = Donor::where('section_id', $SectionId)->where('class_id', NULL)->get()->first();
            if ($donor) {
                $cer->donor_id = $donor->don_id;
                $cer->save();
                return response($cer, 201);
            } else {
                $sec = Section::where('sec_id', $SectionId)->get()->first();
                if ($sec) {
                    $coll = Collage::where('coll_id', $sec->collage_id)->get()->first();
                    $don = new Donor();
                    $don->section_id = $SectionId;
                    $don->collage_id = $sec->collage_id;
                    $don->university_id = $coll->university_id;
                    $don->save();
                    $cer->donor_id = $don->don_id;
                    $cer->save();
                    return response($cer, 201);
                }
            }
        } else if ($user->jobtype == "مسؤول قسم") {
            $mysection = SectionAdmin::where('admin_id', $user->id)->get()->first();
            if ($mysection != null && $mysection->section_id == $SectionId) {
                $cer = new Certification();
                $cer->cer_type = $request->cer_type;

                $donor = Donor::where('section_id', $SectionId)->where('class_id', NULL)->get()->first();
                if ($donor) {
                    $cer->donor_id = $donor->don_id;
                    $cer->save();
                    return response($cer, 201);
                } else {
                    $sec = Section::where('sec_id', $SectionId)->get()->first();
                    if ($sec) {
                        $coll = Collage::where('coll_id', $sec->collage_id)->get()->first();
                        $don = new Donor();
                        $don->section_id = $SectionId;
                        $don->collage_id = $sec->collage_id;
                        $don->university_id = $coll->university_id;
                        $don->save();
                        $cer->donor_id = $don->don_id;
                        $cer->save();
                        return response($cer, 201);
                    }
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
    public function addCertificationsToClass($ClassId, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cer_type' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        $user = JWTAuth::parseToken()->authenticate();
        if ($user->jobtype == "مسؤول عام") {
            $cer = new Certification();
            $cer->cer_type = $request->cer_type;

            $donor = Donor::where('class_id', $ClassId)->get()->first();
            if ($donor) {
                $cer->donor_id = $donor->don_id;
                $cer->save();
                return response($cer, 201);
            } else {
                $class = Classes::where('class_id', $ClassId)->get()->first();
                if ($class) {
                    $sec = Section::where('sec_id', $class->section_id)->get()->first();
                    $coll = Collage::where('coll_id', $sec->collage_id)->get()->first();
                    $don = new Donor();
                    $don->class_id = $ClassId;
                    $don->section_id = $class->section_id;
                    $don->collage_id = $sec->collage_id;
                    $don->university_id = $coll->university_id;
                    $don->save();
                    $cer->donor_id = $don->don_id;
                    $cer->save();
                    return response($cer, 201);
                }
            }
        } else if ($user->jobtype == "مسؤول شعبة") {
            $myclass = ClassesAdmin::where('admin_id', $user->id)->get()->first();
            if ($myclass != null && $myclass->class_id == $ClassId) {
                $cer = new Certification();
                $cer->cer_type = $request->cer_type;

                $donor = Donor::where('class_id', $ClassId)->get()->first();
                if ($donor) {
                    $cer->donor_id = $donor->don_id;
                    $cer->save();
                    return response($cer, 201);
                } else {
                    $class = Classes::where('class_id', $ClassId)->get()->first();
                    if ($class) {
                        $sec = Section::where('sec_id', $class->section_id)->get()->first();
                        $coll = Collage::where('coll_id', $sec->collage_id)->get()->first();
                        $don = new Donor();
                        $don->class_id = $ClassId;
                        $don->section_id = $class->section_id;
                        $don->collage_id = $sec->collage_id;
                        $don->university_id = $coll->university_id;
                        $don->save();
                        $cer->donor_id = $don->don_id;
                        $cer->save();
                        return response($cer, 201);
                    }
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
    public function destroy($id)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $cer = Certification::find($id);
        $myarray1 = [];
        if (!$cer) {
            return response($myarray1, 404);
        } else {
            if ($user->jobtype == "مسؤول عام") {
                $cer->delete();
                $myarray2 = [
                    'data' => null,
                    'message' => "certification deleted"
                ];

                return response($myarray2, 200);
            } else {
                $donor = Donor::where('don_id', $cer->donor_id)->get()->first();
                if ($donor->class_id != null) {
                    if ($user->jobtype == "مسؤول شعبة") {
                        $myclass = ClassesAdmin::where('admin_id', $user->id)->get()->first();
                        if ($myclass->class_id == $donor->class_id) {
                            $cer->delete();
                            $myarray2 = [
                                'data' => null,
                                'message' => "certification deleted"
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
                } else if ($donor->section_id != null) {
                    if ($user->jobtype == "مسؤول قسم") {
                        $mysection = SectionAdmin::where('admin_id', $user->id)->get()->first();
                        if ($mysection->section_id == $donor->section_id) {
                            $cer->delete();
                            $myarray2 = [
                                'data' => null,
                                'message' => "certification deleted"
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
                } else if ($donor->collage_id != null) {
                    if ($user->jobtype == "مسؤول كلية") {
                        $mycollage = CollageAdmin::where('admin_id', $user->id)->get()->first();
                        if ($mycollage->collage_id == $donor->collage_id) {
                            $cer->delete();
                            $myarray2 = [
                                'data' => null,
                                'message' => "certification deleted"
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
    }
    public function update($id, Request $request)
    {
        $cer = Certification::find($id);
        $myarray1 = [];
        if (!$cer) {
            return response($myarray1, 404);
        }
        $user = JWTAuth::parseToken()->authenticate();
        if ($user->jobtype == "مسؤول عام") {
            $cer->update($request->all());

            $myarray2 = [
                'data' => $cer,
                'message' => "certification updated"
            ];
            if ($cer) {
                return response($myarray2, 200);
            }
        } else {
            $donor = Donor::where('don_id', $cer->donor_id)->get()->first();
            if ($donor->class_id != null) {
                if ($user->jobtype == "مسؤول شعبة") {
                    $myclass = ClassesAdmin::where('admin_id', $user->id)->get()->first();
                    if ($myclass->class_id == $donor->class_id) {

                        $cer->update($request->all());

                        $myarray2 = [
                            'data' => $cer,
                            'message' => "certification updated"
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
            } else if ($donor->section_id != null) {
                if ($user->jobtype == "مسؤول قسم") {
                    $mysection = SectionAdmin::where('admin_id', $user->id)->get()->first();
                    if ($mysection->section_id == $donor->section_id) {
                        $cer->update($request->all());

                        $myarray2 = [
                            'data' => $cer,
                            'message' => "certification updated"
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
            } else if ($donor->collage_id != null) {
                if ($user->jobtype == "مسؤول كلية") {
                    $mycollage = CollageAdmin::where('admin_id', $user->id)->get()->first();
                    if ($mycollage->collage_id == $donor->collage_id) {
                        $cer->update($request->all());

                        $myarray2 = [
                            'data' => $cer,
                            'message' => "certification updated"
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

    public function showCert($id)
    {
        $cer = Certification::find($id);
        if (!$cer) {
            $msg =[];

            return response($msg);
        }
        return response($cer);
    }
}
