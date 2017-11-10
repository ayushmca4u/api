<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\AdminUser;
use Illuminate\Support\Facades\Hash;
use Validator;
use Illuminate\Http\Request;
use DB;
#use  Hash;
#use Illuminate\Http\Response;
class AdminController extends Controller
{
		
        public function create(Request $request)
        {
	    $error_code=200;
	    $message="";
	    $success=false;
	    $result="";			
	    $data =$request->all();		
            $rules = array(
		'firstname' => 'required',
		'lastname' => 'required',
		'admin_status' => 'required',
                'email' => 'required|email',
                'password' => 'required|min:8'
             );
		$messages = [
                  'firstname.required' => 'Please enter first name of the user',
                  'lastname.required' => 'Please enter last name of the user',
                  'admin_status.required' => 'Please choose admin status.',
			      'password.required' => 'Please enter login password',
			      'email.required' => 'Please enter email.',
                  'email.email' => 'Please enter valid Email ID.',
				  'password.min' => 'Password length should be equal or  more than 8 character.' 	
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
                                $AdminUser=new AdminUser();
                                $AdminUser->email=$newtodo['email'];
                                $AdminUser->firstname=$newtodo['firstname'];
                                $AdminUser->lastname=$newtodo['lastname'];
				$password=$newtodo['password'];
				$password = Hash::make($password);
                                $AdminUser->password=$password;
				$AdminUser->status=$newtodo['status'];
	                        $AdminUser->username=$newtodo['username'];
                                DB::beginTransaction();
                                $response=$AdminUser->save();
                                if(!$response)
                                {
                                        DB::rollback();
					$error_code=503;
					$message="Could not create admin user .Please try again after some time";
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
				$message="Could not create admin user .Please try again after some time";
				$response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>$result,"success"=>$success];
				#$response= ["error"=>true,"message" => "Could not create login user .Please try again after some time","status_code"=>403];
		                return response()->json($response,$error_code);	
                        }
	
	    }		
        }
	public function show()
	{
		$AdminUser = AdminUser::get();
		$AdminUserArr=$AdminUser->toArray();
		$success=true;
		$error_code="200";
		$message="";		
		#$response= ["error"=>false,"message" =>$UserArr,"status_code"=>200];	
		$response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>$AdminUser,"success"=>$success];	
		return response()->json($response,$error_code);
	}
	public function getadminuser($admin_userid)
        {
		$success=false;
		$error_code="200";
		$message="";
		$AdminUser = AdminUser::where('user_id',$admin_userid)->orderBy('user_id','desc')->first();
                #$User = User::query('login_userid',$login_userid);
		if(!$AdminUser)
		{
			$error_code="403";
			$message="Data Not Found for User Id $admin_userid";
			$response= ["error"=>array("error_code"=>$error_code,"message"=>$message),"result"=>$AdminUser,"success"=>$success];
	                return response()->json($response,$error_code);
		}
		$success=true;	
                $AdminUserArr=$AdminUser->toArray();
                $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>[$AdminUserArr],"success"=>$success];
                return response()->json($response,$error_code);
        }
	public function getadmin_by_email($email)
        {
                $success=false;
                $error_code="200";
                $message="";
		$email=trim(urldecode($email));
		$email = str_replace(' ', '', $email);
                $AdminUser = AdminUser::where('email',$email)->orderBy('user_id','desc')->first();
                #$User = User::query('login_userid',$login_userid);
                if(!$AdminUser)
                {
                        $error_code="403";
                        $message="Data Not Found for User Email Id $email";
                        $response= ["error"=>array("error_code"=>$error_code,"message"=>$message),"result"=>$AdminUser,"success"=>$success];
                        return response()->json($response,$error_code);
                }
                $success=true;
                $AdminUserArr=$AdminUser->toArray();
                $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>[$AdminUserArr],"success"=>$success];
                return response()->json($response,$error_code);
        }
	public function verify_adminuser(Request $request)
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
		#$AdminUser = AdminUser::where('email',$email)->where('password',Hash::check('password',$password))->first();
		$AdminUser = AdminUser::where('email',$email)->first();
		If(!$AdminUser)
                {
                        $error_code="403";
                        $message="Email / password is not correct.";
                        $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result"=>$AdminUser,"success"=>$success];
                        return response()->json($response,$error_code);
                }
		elseif(Hash::check($password,$AdminUser->password))
		{
			$success=true;					
		}	
		else
		{
			$AdminUser="";	
			$error_code="403";
                        $message="Email / password is not correct.";
                        $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result"=>$AdminUser,"success"=>$success];
                        return response()->json($response,$error_code);
		}
                $AdminUserArr=$AdminUser->toArray();
                $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>[$AdminUserArr],"success"=>$success];
                return response()->json($response,$error_code);		
	    }						
	}
	public function update_adminuser(Request $request,$user_id)
	{	
       	    $error_code=200;
            $message="User Details updated successfully.";
            $success=false;
            $result="";
	    $AdminUser = AdminUser::find($user_id);	
            if(!$AdminUser)
            {
                    $error_code="403";
                    $message="Please check user id. User details not found for user id $user_id.";
                    $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result"=>$AdminUser,"success"=>$success];
                    return response()->json($response,$error_code);
            }		
	    DB::beginTransaction();
	    #$option = $AdminUser->options()->get();
	    #$response=$AdminUser->fill($inputdata)->update();
	    $AdminUser->user_id = $user_id;
	    $status=$request->input('status');	
	    if(isset($status))
	    {		
	    	$AdminUser->status = $status;
	    }	
	    $admin_status=$request->input('admin_status');
            if(isset($admin_status))
            {
                $AdminUser->admin_status = $admin_status;
            }	
	    if($request->input('firstname'))
	 		$AdminUser->firstname = $request->input('firstname');
	    
	    if($request->input('lastname'))
                        $AdminUser->lastname = $request->input('lastname');
			
            $response=$AdminUser->save();	
	    if(!$response)				    	
	    {
		    DB::rollback();
		    $error_code="403";
                    $message="Unable to update user details for user id $user_id.";
                    $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result"=>$AdminUser,"success"=>$success];
                    return response()->json($response,$error_code);	
	    } 		
	    DB::commit();
	    $AdminUserArr=$AdminUser->toArray();
	    $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result"=>$AdminUserArr,"success"=>$success];	
	    return response()->json($response,$error_code);
	}
}
?>
