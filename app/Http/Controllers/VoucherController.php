<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Merchant_voucher;
use App\Packages;
use Illuminate\Support\Facades\Hash;
use Validator;
use Illuminate\Http\Request;
use DB;
#use  Hash;
#use Illuminate\Http\Response;
class VoucherController extends Controller
{
		
        public function create(Request $request)
        {
	    $error_code=200;
	    $message="Voucher Code Added Successfully.";
	    $success=false;
	    $result="";			
	    $data =$request->all();
            $rules = array(
		'package_id' => 'required|numeric',
		'merchant_id' => 'required|numeric',
		'voucher_code' => 'required|min:6',
		'voucher_status' => 'required'		
             );
	     $messages = [
                  'package_id.required' => 'Please choose pacakge for voucher',
                  'merchant_id.required' => 'Please define merchant for voucher',
                  'voucher_code.required' => 'Please enter voucher_code',
                  'voucher_status.required' => 'Please choose Voucher status',
		  'package_id.numeric' => 'Pcakge Id shouild be numeric ',
		  'merchant_id.numeric' => 'Merchant Id should be numeric',	 
		  'voucher_code.min' => 'Voucher code length should be more than or equal 7 charecters'
            ];
            $validator = Validator::make($data, $rules,$messages);
	    if ($validator->fails())
	    {
		$error_code="403";
		$message=$validator->errors();
		$response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>$result,"success"=>$success];
		return response()->json($response,$error_code);	
	    }
	    else
	    {
			
		try
                        {
                                $newtodo = $data;
				$merchant_id=$newtodo['merchant_id'];
				$package_id=$newtodo['package_id'];
				$voucher_code=strtolower($newtodo['voucher_code']);
				$voucher_status=$newtodo['voucher_status'];
				if($voucher_status!="c" || $voucher_status!="a")
				{
					$error_code="403";
                                        $message="Please provide valid voucher_status, possible values are ('a','c').";
                                        $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result"=>$result,"success"=>$success];
                                        return response()->json($response,$error_code);
				}
				$Packages = Packages::where('merchant_id',$merchant_id)->where('package_id',$package_id)->orderBy('package_id','desc')->first();
        		        if(!$Packages)
	                	{
		                        $error_code="403";
                		        $message="Package does not belongs to merchant.Please check pacakge details";
		                        $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result"=>$Packages,"success"=>$success];
                		        return response()->json($response,$error_code);
		               }

				$Merchant_voucher = Merchant_voucher::where('merchant_id',$merchant_id)->where('voucher_code',$voucher_code)->orderBy('vid','desc')->first();
                		if($Merchant_voucher)
		                {
                		        $error_code="403";
		                        $message="Voucher Code Already exist for merchant.";
                		        $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result"=>$Merchant_voucher,"success"=>$success];
	        	                return response()->json($response,$error_code);
		                }
                                $Merchant_voucher=new Merchant_voucher();
                                $Merchant_voucher->package_id=$package_id;
				$Merchant_voucher->merchant_id=$merchant_id;
				$Merchant_voucher->voucher_code=$voucher_code;
				$Merchant_voucher->voucher_status=$newtodo['voucher_status'];
				$Merchant_voucher->ttd_voucher_code="TTD".$voucher_code;
				if($request->input('booking_id'))
                                {
					$Merchant_voucher->booking_id=$newtodo['booking_id'];
                                }
				if($request->input('shopper_id'))
				{
	                                $Merchant_voucher->shopper_id=$request->input('shopper_id');
				}
				if($voucher_status=="a")
				{
					 $Merchant_voucher->voucher_assigned_date=date('Y-m-d H:i:s');	
				}
                                DB::beginTransaction();
                                $response=$Merchant_voucher->save();
                                if(!$response)
                                {
                                        DB::rollback();
					$error_code=503;
					$message="Could not generate voucher code for Merchant.Please try again after some time";
					#$response= ["error"=>true,"message" => "Could not create login user .Please try again after some time","status_code"=>403];
					$response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>$result,"success"=>$success];
					return response()->json($response,$error_code);	
                                }
                                DB::commit();
				$success=true;
				$response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>$result,"success"=>$success];	
				#$response= ["error"=>false,"message" => "","status_code"=>200];
				return response()->json($response,$error_code);
                        }
                        catch(Exception $e)
                        {
				$error_code="502";
				$response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>$result,"success"=>$success];
				return response()->json($response,$error_code);
                        }
	
	    }		
        }
	public function show()
	{
		$Merchant_voucher = Merchant_voucher::get();
		$Merchant_voucherArr=$Merchant_voucher->toArray();
		$success=true;
		$error_code="200";
		$message="";		
		#$response= ["error"=>false,"message" =>$UserArr,"status_code"=>200];	
		$response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>$Merchant_voucherArr,"success"=>$success];	
		return response()->json($response,$error_code);
	}
	public function get_voucher_details($voucher_id)
        {
		$success=false;
		$error_code="200";
		$message="";
		$Merchant_voucher = Merchant_voucher::where('vid',$voucher_id)->orderBy('vid','desc')->first();
		if(!$Merchant_voucher)
		{
			$error_code="403";
			$message="Data Not Found for Voucher Id $merchant_id";
			$response= ["error"=>array("code"=>$error_code,"message"=>$message),"result"=>$Merchant_voucher,"success"=>$success];
			return response()->json($response,$error_code);
		}
                $Merchant_voucherArr=$Merchant_voucher->toArray();
		$success=true;		
                $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>[$Merchant_voucherArr],"success"=>$success];
		return response()->json($response,$error_code);
        }
	public function get_merchant_voucher_details($merchant_id)
	{
		$success=false;
                $error_code="200";
                $message="";
                $Merchant_voucher = Merchant_voucher::where('merchant_id',$merchant_id)->get();
                $Merchant_voucherArr=$Merchant_voucher->toArray();
                if(empty($Merchant_voucherArr))
                {
                        $error_code="403";
                        $message="Data Not Found for Vmerchant Id $merchant_id";
                        $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result"=>$Merchant_voucher,"success"=>$success];
                        return response()->json($response,$error_code);
                }
                $success=true;
                $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>[$Merchant_voucherArr],"success"=>$success];
                return response()->json($response,$error_code);
	}
	public function update_voucher_details(Request $request,$merchant_id)
	{
       	    $error_code=200;
            $message="Voucher Details updated successfully.";
            $success=false;
            $result="";
	    $data =$request->all();
	    if(empty($data))
            {
                $error_code="502";
                $message="Please check json data.Json is not valid";
                $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>$result,"success"=>$success];
                return response()->json($response,$error_code);
            }	
	     $rules = array(
                'voucher_code' => 'required|min:6',
                'action' => 'required'
             );
             $messages = [
                  'voucher_code.required' => 'Please enter voucher code.',
                  'action.required' => 'Please define action which yiou want to perform on voucher.',
                  'voucher_code.min' => 'Voucher code length should be more than or equal 7 charecters'
            ];
            $validator = Validator::make($data, $rules,$messages);
            if ($validator->fails())
            {
                $error_code="403";
                $message=$validator->errors();
                $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>$result,"success"=>$success];
                return response()->json($response,$error_code);
            }
	    else
	    {		
		    $voucher_code=strtolower($data['voucher_code']);	
		    $action=$data['action'];	
		    if($action=="redeem")
		    {	
				$Merchant_voucher = Merchant_voucher::where('merchant_id',$merchant_id)->where('voucher_code',$voucher_code)->where('voucher_status','a')->orderBy('vid','desc')->first();
		    } 
		    else
			{
		    	$Merchant_voucher = Merchant_voucher::where('merchant_id',$merchant_id)->where('voucher_code',$voucher_code)->orderBy('vid','desc')->first();
			}
	
        	   if(!$Merchant_voucher)
	           {
        	           $error_code="403";
                	   $message="Voucher details not found for $action";
	                   $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result"=>$Merchant_voucher,"success"=>$success];
        	           return response()->json($response,$error_code);
	           }				
		   $Merchant_voucher->updated_at=date('Y-m-d H:i:s');
		   if($action=="redeem")
		   { 			
		  	 $Merchant_voucher->voucher_status='r';
			 $Merchant_voucher->voucher_redemption_date=date('Y-m-d H:i:s'); 	
		   } 	
		   else
		   {
			$Merchant_voucher->voucher_status='a';				
			$Merchant_voucher->voucher_assigned_date=date('Y-m-d H:i:s');	
			if($request->input('booking_id'))
				$Merchant_voucher->booking_id=$request->input('booking_id');	
			if($request->input('shopper_id'))
                                $Merchant_voucher->shopper_id=$request->input('shopper_id');	
		   }
	           $package_id=$Merchant_voucher->package_id;		
		   DB::beginTransaction();
                   $response=$Merchant_voucher->save();
                   if(!$response)
                   {
                           DB::rollback();
                           $error_code=503;
                           $message="Cound not $action voucher code.Please try again after some time";
                           $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>$result,"success"=>$success];
                           return response()->json($response,$error_code);
                   }
                   DB::commit();
		   //
			if($action=="redeem")
			{
				$Packages= DB::table('merchant_voucher_details')->join('package_details', 'package_details.package_id', '=', 'merchant_voucher_details.package_id')->join('activity_details', 'package_details.activity_id', '=', 'activity_details.activity_id')->where('merchant_voucher_details.package_id', '=', $package_id)->where('merchant_voucher_details.merchant_id', '=', $merchant_id)->select('package_details.package_name','activity_details.name','merchant_voucher_details.*')->get();
        	                $result=$Packages->toArray();
                	        if(!$result)
                        	{
                                	$error_code="403";
	                                $message="Data Not Found for Pacakge  Id $package_id";
        	                        $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result"=>$result,"success"=>$success];
                	                return response()->json($response,$error_code);
                        	}
			}	
		   //	
                   $success=true;
                   $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>$result,"success"=>$success];
                   return response()->json($response,$error_code);	
	     }	
	}
	public function check_voucher_code(Request $request,$voucher_code,$merchant_id)
        {
                $success=false;
                $error_code="200";
                $message="Voucher code available for merchant.Merchant can use this voucher code [voucher_code].";
		$result="";
		$merchant_id=trim($merchant_id);
		$voucher_code=trim($voucher_code);
		if($merchant_id=="" || $voucher_code=="")
                {
                        $error_code="403";
                        $message="Missing Mandatory parameter merchant_id/voucher_code";
                        $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result"=>$result,"success"=>$success];
                        return response()->json($response,$error_code);
                }
		if(strlen($voucher_code)<7)	
		{
			$error_code="403";
                        $message="Voucher code length cannot be less than 7 charecter.";
                        $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result"=>$result,"success"=>$success];
                        return response()->json($response,$error_code);
		}		
	#	$Merchant_voucher = Merchant_voucher::where('merchant_id',$merchant_id)->where('voucher_code',$voucher_code)->orderBy('vid','desc')->first();
		$Merchant_voucher = Merchant_voucher::join("package_details",'merchant_voucher_details.package_id','=','package_details.package_id')->join("merchants",'merchants.id','=','merchant_voucher_details.merchant_id')->where('merchant_voucher_details.merchant_id',$merchant_id)->where('voucher_code',$voucher_code)->select('merchant_voucher_details.*','package_details.package_name','merchants.name as merchant_name')->orderBy('vid','desc')->first();
                if($Merchant_voucher)
                {
                        $error_code="403";
                        $message="Voucher Code Already exist for merchant.";
                        $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result"=>$Merchant_voucher,"success"=>$success];
                        return response()->json($response,$error_code);
                }
		$message=str_replace("voucher_code",$voucher_code,$message);
                $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>$result,"success"=>$success];
                return response()->json($response,$error_code);
        }
}
?>
