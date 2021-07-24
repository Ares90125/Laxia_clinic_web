<?php
namespace App\Services\Clinic;

use Illuminate\Support\Arr;
use App\Models\ClinicDoctorsRelation;
use App\Models\Doctor;
use App\Models\Attachment;
use App\Enums\User\Type as UserType;
use DB;
use Auth;
use Throwable;

/**
 * Class ClinicDoctorsRelationService
 * @package App\Services
 */
class ClinicDoctorsRelationService
{
  public function add($clinic_id, $doctor_id) {
    
    $clinicDoctorRelationInfo = ClinicDoctorsRelation::where(['clinic_id' => $clinic_id, 'doctor_id' => $doctor_id])
      ->first();

    if (!$clinicDoctorRelationInfo) {
      $clinicDoctorRelationInfo = ClinicDoctorsRelation::create(['clinic_id' => $clinic_id, 'doctor_id' => $doctor_id]);
    }

    return $clinicDoctorRelationInfo->id;
  }

  public function get($search, $clinic_id) {
    if(isset($search['status']) && $search['status'] != 0){
      $query = ClinicDoctorsRelation::where('clinic_doctors_relation.clinic_id', $clinic_id)        
      ->join('doctors', 'doctors.doctor_id', '=', 'clinic_doctors_relation.doctor_id')
      ->where('doctors.job_id', '=', $search['status'])
      ->get();
    }else if(isset($search['q'])) {
      $query = ClinicDoctorsRelation::where('clinic_doctors_relation.clinic_id', $clinic_id)        
        ->join('doctors', 'doctors.doctor_id', '=', 'clinic_doctors_relation.doctor_id')
        ->where('doctors.kata_name', 'LIKE', "%{$search['q']}%")
        ->orWhere('doctors.hira_name', 'LIKE', "%{$search['q']}%")
        ->get();
    }else{
      $query = ClinicDoctorsRelation::where('clinic_doctors_relation.clinic_id', $clinic_id)
        ->join('doctors', 'doctors.doctor_id', '=', 'clinic_doctors_relation.doctor_id')
        ->get();
    }
    return $query;
  }

  public function getMemberCount($clinic_id, $request = array()) {
    $query = ClinicDoctorsRelation::where('clinic_doctors_relation.clinic_id', $clinic_id)
      ->join('doctors', 'doctors.doctor_id', '=', 'clinic_doctors_relation.doctor_id');

    if(isset($request['q'])) {
      $query->where('doctors.kata_name', 'LIKE', "%{$request['q']}%")
        ->orWhere('doctors.hira_name', 'LIKE', "%{$request['q']}%");
    }

    $result = $query->get()->count();

    return $result;
  }

  public function delete($clinic_id, $doctor_id) {
    $clinicDoctorRelationInfo = ClinicDoctorsRelation::where(['clinic_id' => $clinic_id, 'doctor_id' => $doctor_id]);
    $clinicDoctorRelationInfo->delete();
    return true;
  }
}
