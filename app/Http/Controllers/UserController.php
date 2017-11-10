<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Hash;
use Validator;
use Illuminate\Http\Request;
use DB;
#use  Hash;
#use Illuminate\Http\Response;
class UserController extends Controller
{
		
        public function create(Request $request)
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
	    $messages = [
                  'email.required' => 'Please enter email id for login',
                  'password.required' => 'Please enter password.',
                  'email.email' => 'Please enter valif Email Id.',
                  'password.min' => 'Password should be equal or more than 8 character'
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
                                $User=new User();
                                $User->email=$newtodo['email'];
				$password=$newtodo['password'];
				$password = Hash::make($password);
                                $User->password=$password;
                                $User->status=1;
                                DB::beginTransaction();
                                $response=$User->save();
                                if(!$response)
                                {
                                        DB::rollback();
					$error_code=503;
					$message="Could not create login user .Please try again after some time";
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
				$message="Could not create login user .Please try again after some time";
				$response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>$result,"success"=>$success];
				#$response= ["error"=>true,"message" => "Could not create login user .Please try again after some time","status_code"=>403];
		                return response()->json($response,$error_code);	
                        }
	
	    }		
        }
	public function show()
	{
		$User = User::get();
		$UserArr=$User->toArray();
		$success=true;
		$error_code="200";
		$message="";		
		#$response= ["error"=>false,"message" =>$UserArr,"status_code"=>200];	
		$response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>$UserArr,"success"=>$success];	
		return response()->json($response,$error_code);
	}
	public function getuser($login_userid)
        {
		$success=false;
		$error_code="200";
		$message="";
		$User = User::where('login_userid',$login_userid)->orderBy('login_userid','desc')->first();
                #$User = User::query('login_userid',$login_userid);
		if(!$User)
		{
			$error_code="403";
			$message="Data Not Found for User Id $login_userid";
			$response= ["error"=>array("error_code"=>$error_code,"message"=>$message),"result"=>$User,"success"=>$success];
	                return response()->json($response,$error_code);
		}
		$success=true;	
                $UserArr=$User->toArray();
                $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>[$UserArr],"success"=>$success];
                return response()->json($response,$error_code);
        }
	public function getuser_by_email($login_email)
        {
                $success=false;
                $error_code="200";
                $message="";
		$login_email=trim(urldecode($login_email));
		$login_email = str_replace(' ', '', $login_email);
                $User = User::where('email',$login_email)->orderBy('login_userid','desc')->first();
                #$User = User::query('login_userid',$login_userid);
                if(!$User)
                {
                        $error_code="403";
                        $message="Data Not Found for User Email Id $login_email";
                        $response= ["error"=>array("error_code"=>$error_code,"message"=>$message),"result"=>$User,"success"=>$success];
                        return response()->json($response,$error_code);
                }
                $success=true;
                $UserArr=$User->toArray();
                $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>[$UserArr],"success"=>$success];
                return response()->json($response,$error_code);
        }
	public function check_mongo()
	{
		$test = test::all();
		$success=false;
                $error_code="200";
                $message="";
		$response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>$test,"success"=>$success];
		return response()->json($response,$error_code);
	}
	public function verify_user(Request $request)
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
                #echo $user_password=Hash::check('password',$password);die;
                #$User = User::where('email',$email)->where('password',Hash::check('password',$password))->first();
		$User = User::where('email',$email)->first();
                if(!$User)
                {
                        $error_code="403";
                        $message="Email / password is not correct.";
                        $response= ["error"=>array("error_code"=>$error_code,"message"=>$message),"result"=>$User,"success"=>$success];
			return response()->json($response,$error_code);
                }
		elseif(Hash::check($password,$User->password))
                {
                        $success=true;
                }
                else
                {
                        $User="";
                        $error_code="403";
                        $message="Email / password is not correct.";
                        $response= ["error"=>array("error_code"=>$error_code,"message"=>$message),"result"=>$AdminUser,"success"=>$success];
                        return response()->json($response,$error_code);
                }	
                $UserArr=$User->toArray();
                $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>[$UserArr],"success"=>$success];
                return response()->json($response);
            }
 	
	}
}
?>
