<?php

namespace App\Http\Controllers;
use App\Models\employee;

use Illuminate\Http\Request;

class EmployeeController extends Controller
{
   
   /* public function ShowPage(){
        
        return view('employee/employee_template');  ---- //testing only 
    }
    */

    public function createEmpInfoData(Request $request){
    
        $createEmpInfoData[] = $request->all();
        $return = [];

        try{
            employee::insert($createEmpInfoData);
        }catch(\Exception $e){
           //Log::error($e);
            $return['error']  = true;
            $return['message'] = $e->getMessage();
        }

        return compact('return','createEmpInfoData');

    }
    public function updateEmpInfoData(Request $request){
    
        $updateEmpInfoData = (!empty($request->get('EmpDataUpdate')) ? $request->get('EmpDataUpdate') : []);
        $empID = (!empty($request->get('EMP_ID')) ? $request->get('EMP_ID') : "");

        $return = [];

        try{
            
            employee::where('EMP_ID',$empID)->update($updateEmpInfoData[0]);
        }catch(\Exception $e){
            //Log::error($e);
            $return['error']  = true;
            $return['message'] = $e->getMessage();
        }

        return compact('return','updateEmpInfoData');

    }
    public function getDataById($empID){
        
        $empID = base64_decode($empID);
        $getDataById = employee::where('EMP_ID',$empID)->get();
        
        return $getDataById;
    }
    public function getEmpData(){

        $data = employee::get();

        return $data;
    }


    public function removeEmpInfoById(Request $request){
        $emp_id = (!empty($request->get('EMP_ID')) ? $request->get('EMP_ID') : "");
        
        try{

            $getDataById = employee::where('EMP_ID',$emp_id)->delete();

        }catch(\Exception $e){
            //Log::error($e);
            $return['error']  = true;
            $return['message'] = $e->getMessage();
        }
        
        
        return $getDataById;
    }
    
}


