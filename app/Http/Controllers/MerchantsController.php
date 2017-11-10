<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Merchants;
use Illuminate\Support\Facades\Hash;
use Validator;
use Illuminate\Http\Request;
use DB;
#use  Hash;
#use Illuminate\Http\Response;
class MerchantsController extends Controller
{
		
        public function create(Request $request)
        {
	    $error_code=200;
	    $message="Merchant Added Successfully.";
	    $success=false;
	    $result="";			
	    $data =$request->all();
            $rules = array(
		'name' => 'required|min:6',
		'description' => 'required|min:20',
		'address' => 'required|min:20',
		'telephone' => 'required|min:10',
		'status' => 'required|min:1',
		'email' => 'required|email',
                'password' => 'required|min:8',	
                'cities' => 'required'	
             );
            $validator = Validator::make($data, $rules);
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
				$email=strtolower($newtodo['email']);
				$Merchants = Merchants::where('email',$email)->orderBy('id','desc')->first();
                		if($Merchants)
		                {
                		        $error_code="403";
		                        $message="Merchant Already Exists with Eamil Id [$email]";
                		        $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result"=>$Merchants,"success"=>$success];
	        	                return response()->json($response,$error_code);
		                }
                                $Merchants=new Merchants();
                                $Merchants->name=$newtodo['name'];
                                $Merchants->email=strtolower($newtodo['email']);
				$password=$newtodo['password'];
                                $password = Hash::make($password);
                                $Merchants->password=$password;
				$Merchants->status=$newtodo['status'];
				$Merchants->description=$newtodo['description'];
				$Merchants->address=$newtodo['address'];
				$Merchants->telephone=$newtodo['telephone'];
				$Merchants->tier=$newtodo['tier'];
				if($request->input('company_url'))
                                {
                                        $Merchants->company_url=$request->input('company_url');
                                }
				if($request->input('company_logo'))
                                {
                                        $Merchants->company_logo=$request->input('company_logo');
                                }
                                DB::beginTransaction();
                                $response=$Merchants->save();
                                if(!$response)
                                {
                                        DB::rollback();
					$error_code=503;
					$message="Could not create Merchant.Please try again after some time";
					#$response= ["error"=>true,"message" => "Could not create login user .Please try again after some time","status_code"=>403];
					$response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>$result,"success"=>$success];
					return response()->json($response,$error_code);	
                                }
				$merchant_id = $Merchants->id; // last inserted id for merchants
				$datetime=date("Y-m-d H:i:s");	
				if($request->input('cities'))	
				{
					$cities=$newtodo['cities'];
					foreach($cities as $key=>$citi_details)
					{
						$city_id=$citycode=$display_name="";	
						$city_id=$citi_details['id'];
						$citycode=$citi_details['citycode'];	
						$display_name=$citi_details['name'];		
						$response=DB::table('city_mapping')->insert(['map_type' =>"merchant", 'map_id' =>$merchant_id,'city_id' =>$city_id,'citycode' =>"$citycode",'city_name' =>"$display_name",'created_at' => "$datetime",'updated_at' => "$datetime"]);		
						if(!$response)
						{
							DB::rollback();
                                	 	        $error_code=503;
                                        		$message="Could not create Merchant.Please try again after some time";
                                        		$response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>$result,"success"=>$success];
	                                        	return response()->json($response,$error_code);			
						}	
					}
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
				$message="Could not create Merchant .Please try again after some time";
				$response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>$result,"success"=>$success];
				#$response= ["error"=>true,"message" => "Could not create login user .Please try again after some time","status_code"=>403];
				return response()->json($response,$error_code);
                        }
	
	    }		
        }
	public function show()
	{
		$Merchants = Merchants::get();
		$MerchantsArr=$Merchants->toArray();
		foreach($MerchantsArr as $key=>$merchant_details)
		{
			$merchant_id=$merchant_details['id'];
			$city_mapping = DB::table('city_mapping')->select('city_id','citycode','city_name')->where('map_id', [$merchant_id])->get();		
			//$mapping=$city_mapping->toArray(); 
			$mapping=json_decode($city_mapping,true);
			$MerchantsArr[$key]['cities']=$mapping;			
		}	
		// $products = DB::table('products')->where('status', [$status])->get();		
		$success=true;
		$error_code="200";
		$message="";		
		#$response= ["error"=>false,"message" =>$UserArr,"status_code"=>200];	
		$response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>$MerchantsArr,"success"=>$success];	
		return response()->json($response,$error_code);
	}
	public function get_merchant_details($merchant_id)
        {
		$success=false;
		$error_code="200";
		$message="";
		$Merchants = Merchants::where('id',$merchant_id)->orderBy('id','desc')->first();
                #$User = User::query('login_userid',$login_userid);
		if(!$Merchants)
		{
			$error_code="403";
			$message="Data Not Found for Merchant Id $merchant_id";
			$response= ["error"=>array("code"=>$error_code,"message"=>$message),"result"=>$Merchants,"success"=>$success];
			return response()->json($response,$error_code);
		}
                $MerchantsArr=$Merchants->toArray();
		$city_mapping = DB::table('city_mapping')->select('city_id','citycode','city_name')->where('map_id', [$merchant_id])->get();
                        //$mapping=$city_mapping->toArray();
		$mapping=json_decode($city_mapping,true);
		$MerchantsArr['cities']=$mapping;
		$success=true;		
                $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>[$MerchantsArr],"success"=>$success];
		return response()->json($response,$error_code);
        }
	public function update_merchant_details(Request $request,$merchant_id)
	{
       	    $error_code=200;
            $message="Merchant Details updated successfully.";
            $success=false;
            $result="";
	    $data =$request->all();
	    $log_prefix=date("Ymd H:i:s");		
	    $log_file_name="update_merchants".date('Ymd').".log";
            $file_upload_log="/var/tmp/".$log_file_name;
            error_log("\n$log_prefix Data Request ".print_r($data,true)."",3,$file_upload_log);
            error_log("\n$log_prefix Post Request ".print_r($_POST,true)."",3,$file_upload_log);
	    $Merchants = Merchants::find($merchant_id);	
            if(!$Merchants)
            {
                    $error_code="403";
                    $message="Please check merchant id.Merchant details not found for merchant  id $merchant_id.";
                    $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result"=>$Merchants,"success"=>$success];
		    return response()->json($response,$error_code);	
            }		
	    DB::beginTransaction();
	    $Merchants->id = $merchant_id;
	    $status=$request->input('status');
	    if(isset($status))
	    {		
	    	$Merchants->status = $status;
	    }
	    if($request->input('name'))
	 		$Merchants->name = $request->input('name');
	    
	    if($request->input('description'))
                        $Merchants->description = $request->input('description');
	    if($request->input('address'))
                        $Merchants->address = $request->input('address');
	    if($request->input('telephone'))
                        $Merchants->telephone = $request->input('telephone');
	    if($request->input('company_url'))
                        $Merchants->company_url = $request->input('company_url'); 				    	  	      if($request->input('tier'))
                        $Merchants->tier = $request->input('tier');
		
            $response=$Merchants->save();
	    if(!$response)				    	
	    {
		    DB::rollback();
		    $error_code="403";
                    $message="Unable to update merchant details for merchant  id $merchant_id.";
                    $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result"=>$Merchants,"success"=>$success];
		    return response()->json($response,$error_code);
	    } 		
	    if($request->input('cities'))
	    {	
	    	$cities=$request->input('cities');
                $datetime=date("Y-m-d H:i:s");
		$delete_response=DB::table('city_mapping')->where('map_id', '=', $merchant_id)->delete();	
		if(!$delete_response)
		{
			DB::rollback();
                        $error_code=503;
                        $message="Could not update Merchant.Please try again after some time";
                        $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>$result,"success"=>$success];
                        return response()->json($response,$error_code);
		}
                foreach($cities as $key=>$citi_details)
                {
                        $city_id=$citycode=$display_name="";
                        $city_id=$citi_details['id'];
                        $citycode=$citi_details['citycode'];
                        $display_name=$citi_details['name'];
                        $response=DB::table('city_mapping')->insert(['map_type' =>"merchant", 'map_id' =>$merchant_id,'city_id' =>$city_id,'citycode' =>"$citycode",'city_name' =>"$display_name",'created_at' => "$datetime",'updated_at' => "$datetime"]);
                        if(!$response)
                        {
                                DB::rollback();
                                $error_code=503;
                                $message="Could not create Merchant.Please try again after some time";
                                $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>$result,"success"=>$success];
                                return response()->json($response,$error_code);
                        }
                }	
	    }	
	    DB::commit();
	    $MerchantsArr=$Merchants->toArray();
	    $success=true;	
	    $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result"=>$MerchantsArr,"success"=>$success];	
	    return response()->json($response,$error_code);
	}
	public function verify_merchant(Request $request)
        {
            $error_code=200;
            $message="";
            $success=false;
            $result="";
            $data =$request->all();
            $rules = array(
                'email' => 'required|email',
                'password' => 'required|min:8'
             );
            $validator = Validator::make($data, $rules);
            if ($validator->fails())
            {
                $error_code="403";
                $message=$validator->errors();
                $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>$result,"success"=>$success];
                return response()->json($response,$error_code);
            }
	    else
            {
                $newtodo = $data;
                $email=strtolower($newtodo['email']);
                $password=$newtodo['password'];
                #echo $user_password=Hash::check('password',$password);die;
                #$AdminUser = AdminUser::where('email',$email)->where('password',Hash::check('password',$password))->first();
                $MerchantsUser = Merchants::where('email',$email)->first();
                If(!$MerchantsUser)
                {
                        $error_code="403";
                        $message="Email / password is not correct.";
                        $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result"=>$MerchantsUser,"success"=>$success];
                        return response()->json($response,$error_code);
                }
                elseif(Hash::check($password,$MerchantsUser->password))
                {
                        $success=true;
                }
		else
                {
                        $MerchantsUser="";
                        $error_code="403";
                        $message="Email / password is not correct.";
                        $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result"=>$MerchantsUser,"success"=>$success];
                        return response()->json($response,$error_code);
                }
                $MerchantsUserArr=$MerchantsUser->toArray();
                $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>[$MerchantsUserArr],"success"=>$success];
                return response()->json($response,$error_code);
            }
        }			
	public function update_password(Request $request)
        {
            $error_code=200;
            $message="Login password has been updated successfully.";
            $success=false;
            $result="";
            $data =$request->all();
		//echo  "<pre>";print_r($data);die;
            $rules = array(
                'email' => 'required|email',
                'old_password' => 'required|min:8',
		'new_password' => 'required|min:8'
             );
	     $messages = [
                  'email.required' => 'Please enter email',
                  'old_password.required' => 'Please enter existing password.',
                  'new_password.required' => 'Please enter new password.',
                  'email.email' => 'Please enter valid email id.',
                  'old_password.min' => 'Existing Password length cannot be equal or less than 8 character.',
                  'new_password.min' => 'New Password length should be equal or  more than 8 character.'
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
                $newtodo = $data;
                $email=strtolower($newtodo['email']);
                $password=$newtodo['old_password'];
                #echo $user_password=Hash::check('password',$password);die;
                #$AdminUser = AdminUser::where('email',$email)->where('password',Hash::check('password',$password))->first();
                $MerchantsUser = Merchants::where('email',$email)->first();
                If(!$MerchantsUser)
                {
                        $error_code="403";
                        $message="Email / password is not correct.";
                        $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result"=>$MerchantsUser,"success"=>$success];
                        return response()->json($response,$error_code);
                }
                elseif(Hash::check($password,$MerchantsUser->password))
                {
                        $success=true;
                }
                else
                {
                        $MerchantsUser="";
                        $error_code="403";
                        $message="Email / password is not correct.";
                        $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result"=>$MerchantsUser,"success"=>$success];
                        return response()->json($response,$error_code);
                }
		$new_password=$newtodo['new_password'];
                $new_password = Hash::make($new_password);
                $MerchantsUser->password=$new_password;
		DB::beginTransaction();
                $response=$MerchantsUser->save();
                if(!$response)
                {
                        DB::rollback();
                        $error_code=503;
                        $message="Could not update login password.Please try again after some time";
                        #$response= ["error"=>true,"message" => "Could not create login user .Please try again after some time","status_code"=>403];
                        $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>$result,"success"=>$success];
                        return response()->json($response,$error_code);
                }
		DB::commit();
		$success=true;
		$response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>$result,"success"=>$success];
                return response()->json($response,$error_code);
            }	
	}
}
?>
