<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Shopper;
use App\Packages;
use App\Packages_price;
use App\ShopperCart;
use Illuminate\Support\Facades\Hash;
use Validator;
use Illuminate\Http\Request;
use DB;
#use  Hash;
#use Illuminate\Http\Response;
class ShopperController extends Controller
{
		
        public function create(Request $request)
        {
	    $allowed_shopper_columns=array('nickname','title','firstname','middlename','lastname','phone','faxno','address','landmark','city','state','country','zipcode','email','password','status');	
	    $error_code=200;
            $message="Shopper Details Added Successfully";
            $success=false;
            $result=""; 	
	    $data =$request->all();		
            $rules = array(
                'email' => 'required|email',
                'status' => 'required'
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
                                $Shopper = Shopper::where('email',$email)->orderBy('shopper_id','desc')->first();
                                if($Shopper)
                                {
					$ShopperArr=$Shopper->toArray();
                                        $error_code="200";
					$success=true;	
                                        $message="Shopper details already exist for email id [$email]";
                                        $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result"=>$ShopperArr,"success"=>$success];
                                        return response()->json($response,$error_code);
                                }
				$Shopper=new Shopper();
                                DB::beginTransaction();
				foreach($newtodo as $shopper_key=>$shopper_val)
                        	{
					if($shopper_key=="password")
					{
						$password = Hash::make($shopper_val);
		                                $Shopper->password=$password;
					}
					elseif($shopper_key=="email")
					{
						$Shopper->email=strtolower($shopper_val);
					}	
                                	elseif(in_array($shopper_key,$allowed_shopper_columns) && $shopper_val!='')
	                                {
        	                                $Shopper->$shopper_key=$shopper_val;
                	                }
                        	}		
                                $response=$Shopper->save();
                                if(!$response)
                                {
                                        DB::rollback();
					$error_code=503;
					$message="Could not create booking user details.Please try again after some time";
					#$response= ["error"=>true,"message" => "Could not create login user .Please try again after some time","status_code"=>403];
					$response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>$result,"success"=>$success];	
	                                return response()->json($response,$error_code);	
                                }
                                DB::commit();
				$success=true;
				$ShopperArr=$Shopper->toArray();
				$response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>$ShopperArr,"success"=>$success];	
				#$response= ["error"=>false,"message" => "","status_code"=>200];
                                return response()->json($response,$error_code);
                        }
                        catch(Exception $e)
                        {
				$error_code="502";
				$message="Could not create booking login user .Please try again after some time";
				$response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>$result,"success"=>$success];
				#$response= ["error"=>true,"message" => "Could not create login user .Please try again after some time","status_code"=>403];
		                return response()->json($response,$error_code);	
                        }
	
	    }		
        }
	public function show()
	{
		$Shopper = Shopper::get();
		$ShopperArr=$Shopper->toArray();
		$success=true;
		$error_code="200";
		$message="";		
		#$response= ["error"=>false,"message" =>$ShopperArr,"status_code"=>200];	
		$response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>$ShopperArr,"success"=>$success];	
		return response()->json($response,$error_code);
	}
	public function getshopper($shopper_id)
        {
		$success=false;
		$error_code="200";
		$message="";
		$Shopper = Shopper::where('shopper_id',$shopper_id)->orderBy('shopper_id','desc')->first();
                #$Shopper = Shopper::query('login_userid',$login_userid);
		if(!$Shopper)
		{
			$error_code="403";
			$message="Data Not Found for Shopper Id $shopper_id";
			$response= ["error"=>array("code"=>$error_code,"message"=>$message),"result"=>$Shopper,"success"=>$success];
	                return response()->json($response,$error_code);
		}
		$success=true;	
                $ShopperArr=$Shopper->toArray();
                $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>[$ShopperArr],"success"=>$success];
                return response()->json($response,$error_code);
        }
	public function getshopper_by_email($shopper_email)
        {
                $success=false;
                $error_code="200";
                $message="";
		$login_email=trim(urldecode($shopper_email));
		$shopper_email = str_replace(' ', '', $shopper_email);
                $Shopper = Shopper::where('email',$shopper_email)->orderBy('shopper_id','desc')->first();
                #$Shopper = Shopper::query('login_userid',$login_userid);
                if(!$Shopper)
                {
                        $error_code="403";
                        $message="Data Not Found for Shopper Email Id $shopper_email";
                        $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result"=>$Shopper,"success"=>$success];
                        return response()->json($response,$error_code);
                }
                $success=true;
                $ShopperArr=$Shopper->toArray();
                $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>[$ShopperArr],"success"=>$success];
                return response()->json($response,$error_code);
        }
	public function verify_shopper(Request $request)
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
                $email=$newtodo['email'];
                $password=$newtodo['password'];
		$Shopper = Shopper::where('email',$email)->first();
                if(!$Shopper)
                {
                        $error_code="403";
                        $message="Email / password is not correct.";
                        $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result"=>$Shopper,"success"=>$success];
			return response()->json($response,$error_code);
                }
		elseif(Hash::check($password,$Shopper->password))
                {
                        $success=true;
                }
                else
                {
                        $Shopper="";
                        $error_code="403";
                        $message="Email / password is not correct.";
                        $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result"=>$AdminShopper,"success"=>$success];
                        return response()->json($response,$error_code);
                }	
                $ShopperArr=$Shopper->toArray();
                $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>[$ShopperArr],"success"=>$success];
                return response()->json($response);
            }	
	}
	public function update_shopper(Request $request,$shopper_id)
	{
			
		$allowed_shopper_columns=array('nickname','title','firstname','middlename','lastname','phone','faxno','address','landmark','city','state','country','zipcode','status');
		$error_code=200;
	        $message="Shopper Details updated successfully.";
        	$success=false;
	        $result="";
        	$datetime=date("Y-m-d H:i:s"); 	
		$data =$request->all();
		$Shopper = Shopper::find($shopper_id);
                 if(!$Shopper)
                 {
                     DB::rollback();
                     $error_code="403";
                     $message="Please check Shopper id.Shopper Booking details not found for shopper id $shopper_id.";
                     $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result"=>$Shopper,"success"=>$success];
                     return response()->json($response,$error_code);
                 }
		 $shopper_details=$data;
                 $Shopper->shopper_id=$shopper_id;
		 foreach($shopper_details as $shopper_key=>$shopper_val)
                 {
                         if(in_array($shopper_key,$allowed_shopper_columns) && $shopper_val!='')
                         {
                                 $Shopper->$shopper_key=$shopper_val;
                         }
                 }
                 $response=$Shopper->save();
                 if(!$response)
                 {
                     DB::rollback();
                     $error_code="403";
                     $message="Unable to update Booking shopper details for shopper_id $shopper_id.";
                     $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result"=>$Shopper,"success"=>$success];
                     return response()->json($response,$error_code);
                 }
		 DB::commit();
	         $ShopperArr=$Shopper->toArray();
        	 $success=true;
	         $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result"=>$ShopperArr,"success"=>$success];
        	 return response()->json($response,$error_code);
	}
	public function add_to_cart(Request $request)
        {
            $allowed_addtocart_columns=array('shopper_id','package_id','web_price','currency','quantity','adult','children','total_participants','discount','offer_id','tracking_url','booking_activity_date','booking_activity_time','adult_activity_count','children_activity_count');
	    $error_code=200;
            $message="Shopper package details Added to Add To Cart";
            $success=false;
            $result="";	
            $data =$request->all();
	    $log_prefix=date("Ymd H:i:s");
            $log_file_name="add_to_cart".date('Ymd').".log";
            $file_upload_log="/var/tmp/".$log_file_name;
            error_log("\n$log_prefix Data Request ".print_r($data,true)."",3,$file_upload_log); 	
            $rules = array(
                'shopper_id' => 'required|numeric',
                'package_id' => 'required|numeric',
                'web_price' => 'required',
                'currency' => 'required|min:3',
                'booking_activity_date' => 'required',
                'total_participants' => 'required|numeric'
             );
            $validator = Validator::make($data, $rules);
            if ($validator->fails())
            {
                $error_code="403";
                $message=$validator->errors();
                $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>$result,"success"=>$success];
                return response()->json($response,$error_code);
            }
	    try
                        {
                                $newtodo = $data;
				$shopper_id=$newtodo['shopper_id'];
				$package_id=$newtodo['package_id'];
                                $ShopperCart = ShopperCart::where('shopper_id',$shopper_id)->where('package_id',$package_id)->orderBy('shopper_id','desc')->first();
                                if($ShopperCart)
                                {
                                        $ShopperCartArr=$ShopperCart->toArray();
                                        $error_code="200";
                                        $success=true;
                                        $message="Package Already added in shopper add to cart.";
                                        $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result"=>$ShopperCartArr,"success"=>$success];
                                        return response()->json($response,$error_code);
                                }	
				$datetime=date("Y-m-d H:i:s");
				$newtodo['cart_date']=$datetime;
                                DB::beginTransaction();
				$ShopperCart=new ShopperCart();
                                foreach($newtodo as $shopper_key=>$shopper_val)
                                {
                                        if(in_array($shopper_key,$allowed_addtocart_columns) && $shopper_val!='')
                                        {
                                                $ShopperCart->$shopper_key=$shopper_val;
                                        }
                                }
                                $response=$ShopperCart->save();
                                if(!$response)
                                {
                                        DB::rollback();
                                        $error_code=503;
                                        $message="Could not create shopper cart details.Please try again after some time";
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
                                $message="Could not create shopper cart details .Please try again after some time";
                                $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>$result,"success"=>$success];
                                #$response= ["error"=>true,"message" => "Could not create login user .Please try again after some time","status_code"=>403];
                                return response()->json($response,$error_code);
                        }

            }
	public  function add_to_cart_new(Request $request)
	{
	    $allowed_addtocart_columns=array('shopper_id','package_id','currency','quantity','adult','children','total_participants','tracking_url','booking_activity_date','booking_activity_time','adult_activity_count','children_activity_count');
            $error_code=200;
            $message="Shopper package details Added to Add To Cart";
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
            $log_prefix=date("Ymd H:i:s");
            $log_file_name="add_to_cart".date('Ymd').".log";
            $file_upload_log="/var/tmp/".$log_file_name;
            error_log("\n$log_prefix Data Request ".print_r($data,true)."",3,$file_upload_log);
		$db_commit=false;	
            	DB::beginTransaction();
		foreach ($data as $key=>$cart_details)
		{
			if(!is_array($cart_details))
			{
				DB::rollback();	
				$error_code="403";
                                $success=false;
                                $message="Data not found to add details in add to cart for shopper.";
                                $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result"=>$result,"success"=>$success];
                                return response()->json($response,$error_code);		
			}	
			$newtodo=$cart_details;	
            		$rules = array(
	                'shopper_id' => 'required|numeric',
        	        'package_id' => 'required|numeric',
                	'currency' => 'required|min:3',
	                'booking_activity_date' => 'required',
        	        'total_participants' => 'required|numeric'
	            	 );
	        	    $validator = Validator::make($newtodo, $rules);
		            if ($validator->fails())
        		    {
				DB::rollback();
                		$error_code="403";
		                $message=$validator->errors();
        		        $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>$result,"success"=>$success];
	        	        return response()->json($response,$error_code);
        	    		}		
			    try
        	                {
                        	        $shopper_id=$newtodo['shopper_id'];
	                                $package_id=$newtodo['package_id'];
					$Packages_price = Packages_price::where('package_id',$package_id)->orderBy('package_id','desc')->first();
                        	        if(!$Packages_price)
                                	{
						DB::rollback();
                                        	$error_code="403";
	                                        $success=false;
        	                                $message="Price details not found for pacakge id [$package_id].";
                	                        $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result"=>$result,"success"=>$success];
                        	                return response()->json($response,$error_code);
                                	}				
					$Packages_priceArr=$Packages_price->toArray();
					$web_price=$Packages_priceArr['web_price'];
					$list_price=$Packages_priceArr['list_price'];
					$cost_price=$Packages_priceArr['cost_price'];
                        	        $ShopperCart = ShopperCart::where('shopper_id',$shopper_id)->where('package_id',$package_id)->orderBy('shopper_id','desc')->first();
                                	if($ShopperCart)
	                                {
						DB::rollback();				
        	                                $ShopperCartArr=$ShopperCart->toArray();
                	                        $error_code="200";
                        	                $success=true;
                                	        $message="Package Already added in shopper add to cart.";
                                        	$response= ["error"=>array("code"=>$error_code,"message"=>$message),"result"=>$ShopperCartArr,"success"=>$success];
	                                        return response()->json($response,$error_code);
        	                        }
					//Packages_price		
	                                $datetime=date("Y-m-d H:i:s");
        	                        $newtodo['cart_date']=$datetime;
                        	        $ShopperCart=new ShopperCart();
                                	foreach($newtodo as $shopper_key=>$shopper_val)
	                                {
        	                                if(in_array($shopper_key,$allowed_addtocart_columns) && $shopper_val!='')
                	                        {
                        	                        $ShopperCart->$shopper_key=$shopper_val;
                                	        }
	                                }
					$ShopperCart->web_price=$web_price;
					$ShopperCart->list_price=$list_price;
					$ShopperCart->cost_price=$cost_price;
                                	$response=$ShopperCart->save();
	                                if(!$response)
        	                        {
                	                        DB::rollback();
                        	                $error_code=503;
						$message="Could not create shopper cart details.Please try again after some time";
                                        	#$response= ["error"=>true,"message" => "Could not create login user .Please try again after some time","status_code"=>403];
	                                        $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>$result,"success"=>$success];
        	                                return response()->json($response,$error_code);
                	                }
					$db_commit=true;
                        	}
	                        catch(Exception $e)
        	                {
                	                $error_code="502";
                        	        $message="Could not create shopper cart details .Please try again after some time";
                                	$response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>$result,"success"=>$success];
	                                #$response= ["error"=>true,"message" => "Could not create login user .Please try again after some time","status_code"=>403];
        	                        return response()->json($response,$error_code);
                	        }	
			}
			if($db_commit)
			{
	                       DB::commit();
        	               $success=true;
	        	       $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>$result,"success"=>$success];
	                       return response()->json($response,$error_code);
			}
	}	
			
	public function get_add_to_cart_details($shopper_id)
        {
                $success=false;
                $error_code="200";
                $message="";
                $ShopperCart = ShopperCart::where('shopper_id',$shopper_id)->get();
		$ShopperCartArr=$ShopperCart->toArray();
                if(!$ShopperCartArr)
                {
                        $error_code="403";
                        $message="Data Not Found for Shopper Id $shopper_id";
                        $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result"=>$ShopperCart,"success"=>$success];
                        return response()->json($response,$error_code);
                }
                $success=true;
		foreach($ShopperCartArr as  $key=>$cart_details)
		{
			$package_id=$cart_details['package_id'];
			$Packages= DB::table('package_details')->join('activity_details', 'package_details.activity_id', '=', 'activity_details.activity_id')->where('package_id', '=', $package_id)->select('package_details.package_name', 'activity_details.name as activity_name','activity_details.activity_id','package_details.merchant_id')->get();
	                $PackagesArr=$Packages->toArray();
			$ShopperCartArr[$key]['activity_id']=$PackagesArr[0]->activity_id;
			$ShopperCartArr[$key]['merchant_id']=$PackagesArr[0]->merchant_id;
			$ShopperCartArr[$key]['activity_name']=$PackagesArr[0]->activity_name;
			$ShopperCartArr[$key]['package_name']=$PackagesArr[0]->package_name;
		}
                $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>[$ShopperCartArr],"success"=>$success];
                return response()->json($response,$error_code);
        }
	public function delete_add_to_cart_details($shopper_id,$package_id)
        {
                $success=false;
                $error_code="200";
                $message="Pacakge removed from shopper add to cart details.";
                $ShopperCart = ShopperCart::where('shopper_id',$shopper_id)->where('package_id',$package_id)->get();
		$ShopperCartArr=$ShopperCart->toArray();
		$result="";
                if(!$ShopperCartArr)
                {
                        $error_code="403";
                        $message="Data Not Found for Pacakge Id $package_id";
                        $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result"=>$ShopperCart,"success"=>$success];
                        return response()->json($response,$error_code);
                }
		DB::beginTransaction();
		$delete_response=ShopperCart::where('shopper_id',$shopper_id)->where('package_id',$package_id)->delete();
                if(!$delete_response)
                {
                         DB::rollback();
                         $error_code=503;
                         $message="Could not remove pacakge details from shopper add to cart details";
                         $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>$result,"success"=>$success];
                         return response()->json($response,$error_code);
                }
		DB::commit();
                $success=true;
		$ShopperCart = ShopperCart::where('shopper_id',$shopper_id)->get();
                $ShopperCartArr=$ShopperCart->toArray();
                $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>[$ShopperCartArr],"success"=>$success];
                return response()->json($response,$error_code);
        }	
	public function update_add_to_cart_details(Request $request,$shopper_id,$package_id)
        {
		$success=false;
                $error_code="200";
                $message="Pacakge removed from shopper add to cart details.";
		$quantity=$request->input('quantity');
		$result="";	
		if(trim($quantity)=="")
		{
			$error_code="403";
                        $message="Missing Mandatory Parameter Quantity for update shopper pacakge details";
                        $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result"=>$result,"success"=>$success];
                        return response()->json($response,$error_code);
		}
                $ShopperCart = ShopperCart::where('shopper_id',$shopper_id)->where('package_id',$package_id)->get();
		 $ShopperCartArr=$ShopperCart->toArray();
                $result="";
                if(!$ShopperCartArry)
                {
                        $error_code="403";
                        $message="Data Not Found for Pacakge Id $package_id";
                        $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result"=>$ShopperCart,"success"=>$success];
                        return response()->json($response,$error_code);
                }
		DB::beginTransaction();
		$update_response=DB::table('shopper_cart')->where('shopper_id', '=', $shopper_id)->where('package_id', '=', $package_id)->update(['quantity' => $quantity]);
                if(!$update_response)
                {
                        DB::rollback();
                        $error_code=503;
                        $message="Could not update Package Details.Please try again after some time";
                        $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>$result,"success"=>$success];
                        return response()->json($response,$error_code);
                }
		DB::commit();
                $success=true;
		$ShopperCart = ShopperCart::where('shopper_id',$shopper_id)->get();
                $ShopperCartArr=$ShopperCart->toArray();
                $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>[$ShopperCartArr],"success"=>$success];
                return response()->json($response,$error_code);
        }
}
?>
