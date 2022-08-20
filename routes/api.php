<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClassesControllers;
use App\Http\Controllers\AdvertismentControllers;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\CertificationsControllers;
use App\Http\Controllers\UniversityController;
use App\Http\Controllers\CollageControllers;
use App\Http\Controllers\knowledg_domainController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\PracticalAssignmentController;
use App\Http\Controllers\TheoriticalAssignmentController;
use App\Http\Controllers\WorkExperianceController;
use App\Http\Controllers\sceintific_experiecne_controller;
use App\Http\Controllers\TeacherCertificateController;
use App\Http\Controllers\AssignmentController;

Route::resource('test', TestController::class);
Route::post('/creat_university', [UniversityController::class, 'create']);

Route::get('/University/{id}', [UniversityController::class, 'showUniversity']);
Route::post('/update_university/{id}', [UniversityController::class, 'update']);
Route::post('/login', [AuthController::class, 'login']);
Route::group(['middleware' => ['api', 'cros']], function ($router) {
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('checkusertoken:user_api');
    Route::get('/user-profile', [AuthController::class, 'userProfile'])->middleware('checkusertoken:user_api');
    Route::get('/search/{name}', [UserController::class, 'searchByName'])->middleware('checkusertoken:user_api');
    Route::get('/showAllClassesInSection/{sectionId}', [ClassesControllers::class, 'showAllClasses']);
    Route::post('/addClass/{sectionId}', [ClassesControllers::class, 'add'])->middleware('checkusertoken:user_api');
    Route::delete('/destroy/{id}', [ClassesControllers::class, 'destroy'])->middleware('checkusertoken:user_api');
    Route::post('/update/{id}', [ClassesControllers::class, 'update'])->middleware('checkusertoken:user_api');
    Route::get('/searchByName', [ClassesControllers::class, 'search']);
    Route::get('/class/{id}', [ClassesControllers::class, 'showClass']);
    //subjects
    Route::get('/showSubject/{id}',[SubjectController::class,'show']);
    Route::get('/showClassSubjects/{id}',[SubjectController::class,'showInClass']);
    Route::get('/showSectionSubjects/{id}',[SubjectController::class,'showInSection']);
    Route::post('/addSectionSubject/{id}',[SubjectController::class,'addSubjectToSection'])->middleware('checkusertoken:user_api');
    Route::post('/addClassSubject/{id}',[SubjectController::class,'addSubjectToClass'])->middleware('checkusertoken:user_api');
    Route::delete('/deleteSubject/{id}',[SubjectController::class,'destory'])->middleware('checkusertoken:user_api');
    Route::post('/updateSubject/{id}',[SubjectController::class,'update'])->middleware('checkusertoken:user_api');
    Route::get('/searchSubject/{name}',[SubjectController::class,'search']);
  //Advertisments
    Route::get('/showAllAdvertismentsInCollage/{CollageId}', [AdvertismentControllers::class, 'showAllAdvertismentInCollage']);
    Route::get('/showAllAdvertismentsInUniversity/{UniversityId}', [AdvertismentControllers::class, 'showAllAdvertismentInUniversity']);
    Route::post('/addAdvertismentToCollage/{CollageId}', [AdvertismentControllers::class, 'addToCollage'])->middleware('checkusertoken:user_api');
    Route::post('/addAdvertismentToUniversity/{UniversityId}', [AdvertismentControllers::class, 'addToUniversity'])->middleware('checkusertoken:user_api');
    Route::delete('/destroyAdvertisment/{id}', [AdvertismentControllers::class, 'destroy'])->middleware('checkusertoken:user_api');
    Route::post('/updateAdvertisment/{id}', [AdvertismentControllers::class, 'update'])->middleware('checkusertoken:user_api');
    Route::get('/adver/{id}', [AdvertismentControllers::class, 'showAdv']);
    //certifications
    Route::get('/showAllCertificationsInCollage/{CollageId}', [CertificationsControllers::class, 'showAllCertificationsInCollage']);
    Route::get('/showAllCertificationsInSection/{SectionId}', [CertificationsControllers::class, 'showAllCertificationsInSection']);
    Route::get('/showAllCertificationsInClass/{ClassId}', [CertificationsControllers::class, 'showAllCertificationsInClass']);
    Route::post('/addCertificationsToCollage/{CollageId}', [CertificationsControllers::class, 'addCertificationsToCollage'])->middleware('checkusertoken:user_api');
    Route::post('/addCertificationsToSection/{SectionId}', [CertificationsControllers::class, 'addCertificationsToSection'])->middleware('checkusertoken:user_api');
    Route::post('/addCertificationsToClass/{ClassId}', [CertificationsControllers::class, 'addCertificationsToClass'])->middleware('checkusertoken:user_api');
    Route::delete('/destroyCertifications/{id}', [CertificationsControllers::class, 'destroy'])->middleware('checkusertoken:user_api');
    Route::post('/updateCertifications/{id}', [CertificationsControllers::class, 'update'])->middleware('checkusertoken:user_api');  //universities
    Route::get('/showCert/{id}', [CertificationsControllers::class, 'showCert']);

    //  Route::get('/AllUniversities', [UniversityController::class, 'showAllUniversities']);
    Route::get('/University/{id}', [UniversityController::class, 'showUniversity']);
    Route::delete('/delete_university/{id}', [UniversityController::class, 'destroy'])->middleware('checkadmintoken:user_api');
    Route::post('/creat_university', [UniversityController::class, 'create'])->middleware('checkadmintoken:admin_api');
    Route::post('/update_university/{id}', [UniversityController::class, 'update'])->middleware('admin_university:user_api');
    Route::get('/AllUniversities', [UniversityController::class, 'showAllUniversities']);

    Route::get('/search_university_name/{name}', [UniversityController::class, 'search']);
    //collages
    Route::get('/AllCollages/{id}', [CollageControllers::class, 'showAllCollages']);
    Route::get('/collage/{id}', [CollageControllers::class, 'showCollage']);
    Route::delete('/delete_collage/{id}', [CollageControllers::class, 'destroy'])->middleware('admin_collage:user_api');
    Route::post('/creat_collage', [CollageControllers::class, 'create'])->middleware('admin_university:user_api');
    Route::post('/update_collage/{coll_id}', [CollageControllers::class, 'update'])->middleware('admin_collage:user_api');
    Route::get('/search_collage_name/{name}', [CollageControllers::class, 'search']);
    Route::get('/search_collage_uni/{name}', [CollageControllers::class, 'showSearchCollages']);

    //sections
    Route::get('/AllSections/{id}', [SectionController::class, 'showAllSections']);
    Route::get('/section/{id}', [SectionController::class, 'showSection']);
    Route::delete('/delete_section/{id}', [SectionController::class, 'destroy'])->middleware('admin_section:user_api');
    Route::post('/creat_section', [SectionController::class, 'create'])->middleware('admin_collage:user_api');
    Route::post('/update_section/{id}', [SectionController::class, 'update'])->middleware('admin_section:user_api');
    Route::get('/search_section_name/{name}', [SectionController::class, 'search']);
    Route::get('/search_section_coll/{name}', [SectionController::class, 'showSearchSection']);
});
Route::post('/refresh', [AuthController::class, 'refresh'])->middleware('checkusertoken:user_api');
Route::get('/getAllAdmins', [UserController::class, 'index'])->middleware('checkusertoken:user_api');
Route::get('/myShow', [AuthController::class, 'userProfile'])->middleware('checkusertoken:user_api');
Route::group(['middleware' => ['api', 'cros', 'checkadmintoken:admin_api']], function ($router) {
    Route::post('/register', [AuthController::class, 'register']);
    Route::delete('/delete_user/{id}', [UserController::class, 'destroy']);
    Route::post('/update_user/{id}', [UserController::class, 'update']);
    Route::get('/showUser/{id}', [UserController::class, 'show']);
});
//Teacher
Route::post('/addTeacher', [TeacherController::class, 'addTeacher']);
Route::delete('/deleteTeacher/{id}', [TeacherController::class, 'destroy']);
Route::post('/updateTeacher/{id}', [TeacherController::class, 'update']);
Route::get('/teacherProfile', [TeacherController::class, 'userProfile']);
Route::get('/getAllTeachers', [TeacherController::class, 'getAllTeachers']);
Route::get('/showTeacher/{id}', [TeacherController::class, 'showTeacher']);
Route::get('/searchByName', [TeacherController::class, 'searchByName']);
//Teacher Certification 
Route::post('/AddTeacherCertification/{teacherId}', [TeacherCertificateController::class, 'store']);
Route::delete('/deleteTeacherCertification/{id}', [TeacherCertificateController::class, 'destroy']);
Route::post('/updateTeacherCertification/{id}', [TeacherCertificateController::class, 'update']);
Route::get('/showTeacherCertification/{teacherId}', [TeacherCertificateController::class, 'index']);
//Teacher Experiance 
Route::post('/AddTeacherExperiance/{teacherId}', [WorkExperianceController::class, 'store']);
Route::delete('/deleteTeacherExperiance/{id}', [WorkExperianceController::class, 'destroy']);
Route::post('/updateTeacherExperiance/{id}', [WorkExperianceController::class, 'update']);
Route::get('/showTeacherExperiance/{teacherId}', [WorkExperianceController::class, 'index']);
//Teacher Skills 
Route::post('/addSkill/{teacherId}', [sceintific_experiecne_controller::class, 'add']);
Route::delete('/deleteSkill/{id}', [sceintific_experiecne_controller::class, 'delete']);
Route::post('/updateSkill/{id}', [sceintific_experiecne_controller::class, 'edit']);
Route::get('/showSkill/{teacherId}', [sceintific_experiecne_controller::class, 'show']);

//knowledge domain
Route::post('/adddomain', [knowledg_domainController::class, 'adddomain'])->middleware('checkadmintoken:admin_api');
Route::delete('/deletedomain/{id}', [knowledg_domainController::class, 'deletedomain'])->middleware('checkadmintoken:admin_api');
Route::post('/updatedomain/{id}', [knowledg_domainController::class, 'updatedomain'])->middleware('checkadmintoken:admin_api');
Route::get('/getalldomain', [knowledg_domainController::class, 'showall']);
Route::get('/getdomain/{id}', [knowledg_domainController::class, 'showdomain']);
Route::get('/searchdomain', [knowledg_domainController::class, 'search']);
Route::get('/showdomainsubjects/{id}', [knowledg_domainController::class, 'showSubjects']);
Route::get('/showdomainsubjectsforteacher/{id}', [knowledg_domainController::class, 'getsubjectforteacher']);
//practical assignment
Route::post('/createPAssignment', [PracticalAssignmentController::class, 'createAssignment']);//->middleware('checkusertoken:user_api');
Route::delete('/destroyPAssignment/{id}', [PracticalAssignmentController::class, 'destroyAssignment']);//->middleware('checkusertoken:user_api');
Route::post('/updatePAssignment/{id}', [PracticalAssignmentController::class, 'updateAssignment']);//->middleware('checkusertoken:user_api');
Route::get('/showPAssignment/{id}', [PracticalAssignmentController::class, 'showAssignment']);
Route::get('/showSubjectPAssignment/{id}', [PracticalAssignmentController::class, 'showSubjectAssignment']);
//theoritical assignment
Route::post('/createTAssignment', [TheoriticalAssignmentController::class, 'createAssignment']);//->middleware('checkusertoken:user_api');
Route::delete('/destroyTAssignment/{id}', [TheoriticalAssignmentController::class, 'destroyAssignment']);//->middleware('checkusertoken:user_api');
Route::post('/updateTAssignment/{id}', [TheoriticalAssignmentController::class, 'updateAssignment']);//->middleware('checkusertoken:user_api');
Route::get('/showTAssignment/{id}', [TheoriticalAssignmentController::class, 'showAssignment']);
Route::get('/showSubjectTAssignment/{id}', [TheoriticalAssignmentController::class, 'showSubjectAssignment']);
//teacher assignment
Route::post('/portioncalc/{id}',[AssignmentController::class,'calculatePortion'])->middleware('checkteachertoken:user_api');
Route::get('/getassigment/{id}',[AssignmentController::class,'getsuitableAssignment'])->middleware('checkteachertoken:user_api');
Route::get('/getTeacherPracticalPortions',[AssignmentController::class,'showmypracticalportions'])->middleware('checkteachertoken:user_api');
Route::get('/getTeacherTheoriticalPortions',[AssignmentController::class,'showmytheoriticalportions'])->middleware('checkteachertoken:user_api');
Route::delete('/deletepracticalportion/{id}',[AssignmentController::class,'deletePportion'])->middleware('checkteachertoken:user_api');
Route::delete('/deletetheoriticalportion/{id}',[AssignmentController::class,'deleteTportion'])->middleware('checkteachertoken:user_api');
Route::get('/getselectedpassignment/{id}',[AssignmentController::class,'getselectedPassignment']);
Route::get('/getselectedtassignment/{id}',[AssignmentController::class,'getselectedTassignment']);
Route::get('/getteachers/{id}',[AssignmentController::class,'getteacher'])->middleware('checkusertoken:user_api');