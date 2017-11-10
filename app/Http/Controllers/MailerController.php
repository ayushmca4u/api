<?php
namespace App\Http\Controllers;
use App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Postmark\PostmarkClient;
use Postmark\Models\PostmarkException;
use Validator;
use App\Merchants;
//use Mail;
use App\Http\Requests;

class MailerController extends Controller 
{
   public function basic_email()
   {
      $data = array('name'=>"Virat Gandhi");
   
      Mail::send(['text'=>'mail'], $data, function($message) {
         $message->to('ayush.mca@gmail.com', 'Tutorials Point')->subject
            ('Laravel Basic Testing Mail');
         $message->from('ticketstodo@gmail.com','Virat Gandhi');
      });
      echo "Basic Email Sent. Check your inbox.";
   }

   public function html_email(){
      $data = array('name'=>"Virat Gandhi");
      Mail::send('mail', $data, function($message) {
         $message->to('abc@gmail.com', 'Tutorials Point')->subject
            ('Laravel HTML Testing Mail');
         $message->from('xyz@gmail.com','Virat Gandhi');
      });
      echo "HTML Email Sent. Check your inbox.";
   }
   
   public function attachment_email(){
      $data = array('name'=>"Virat Gandhi");
      Mail::send('mail', $data, function($message) {
         $message->to('abc@gmail.com', 'Tutorials Point')->subject
            ('Laravel Testing Mail with Attachment');
         $message->attach('C:\laravel-master\laravel\public\uploads\image.png');
         $message->attach('C:\laravel-master\laravel\public\uploads\test.txt');
         $message->from('xyz@gmail.com','Virat Gandhi');
      });
      echo "Email Sent with attachment. Check your inbox.";
   }
   public  function send_reset_password_mail(Request $request)	
   {
	    $error_code=200;
            $message="Password regeneration link sent on email id ##email_id##";
            $success=false;
            $result="";
            $data =$request->all();
                //echo  "<pre>";print_r($data);die;
            $rules = array(
                'to' => 'required|email',
                'name' => 'required',
                'action_url' => 'required',
                'support_url' => 'required'
             );
             $messages = [
                  'to.required' => 'Please enter email for email receipent.',
                  'action_url.required' => 'Please enter action url which for reset login password.',
		  'support_url.required' => 'Please enter support url.',   
                  'to.email' => 'Please enter valid email id.',
                  'name.required' => 'Please name of the merchant.'
            ];
            $validator = Validator::make($data, $rules,$messages);
            if ($validator->fails())
            {
                $error_code="403";
                $message=$validator->errors();
                $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>$result,"success"=>$success];
                return response()->json($response,$error_code);
            }	
		try
		{ 
		    $newtodo=$data;
		    $action_url=$newtodo['action_url'];
		    $to=$newtodo['to'];
		    $MerchantsUser = Merchants::where('email',$to)->first();
	            If(!$MerchantsUser)
                	{
                   $error_code="403";
	                $message="Merchant details not found for email id $to";
        	        $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result"=>$MerchantsUser,"success"=>$success];
                        return response()->json($response,$error_code);
        	        }			
		    $name=$newtodo['name'];
		    $support_url=$newtodo['support_url'];			 		
		    $operating_system="";
		    $browser_name="";		
		    if($request->input('operating_system'))
				$operating_system=$request->input('operating_system');
		    if($request->input('browser_name'))
                                $browser_name=$request->input('browser_name');	
		    $client = new PostmarkClient(env("POSTMARK_CLIENT_ID"));	
		    $sendResult = $client->sendEmailWithTemplate(
		  	env("TICKETSTODO_FROM_EMAIL"),
			  "$to",
			  3745721,
			  [
			  "product_name"=>"Tickets TODO Merchant Interface",
			  "product_url" => "https://stgmerchant.ticketstodo.com/", 	
			  "action_url" => "$action_url",
			  "name" => "$name",
			  "operating_system" => "$operating_system",
			  "browser_name" => "$browser_name",
			  "support_url" => "$support_url"
			]);
			$success=true;
			$message=str_replace("##email_id##",$to,$message);
			$response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>$result,"success"=>$success];
                        return response()->json($response,$error_code);	
		}
		catch (PostmarkException $ex)
		{
			$httpStatusCode=$ex->httpStatusCode;
			$error_message=$ex->message;
			$ErrorCode=$ex->postmarkApiErrorCode;
			if($ErrorCode==0)
			{
				$success=true;
			}
			else
			{
				$error_code="502";
				$message=$error_message;
			}
			$response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>$result,"success"=>$success];
                        return response()->json($response,$error_code);	
		}
		catch(exception $e)
		{
			$error_code="502";
                        $message="Could not send email to merhcnat for reset password.Please try again after some time.";
                        $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>$result,"success"=>$success];
                        return response()->json($response,$error_code);				
		}
  	 }	 		
}
?>
