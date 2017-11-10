<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Pages;
use Validator;
use Illuminate\Http\Request;
use DB;
#use  Hash;
#use Illuminate\Http\Response;
class PagesController extends Controller
{
		
        public function create(Request $request)
        {
	    $error_code=200;
	    $message="Page Details Added Successfully.";
	    $success=false;
	    $result="";			
	    $data =$request->all();		
            $rules = array(
		'page_title' => 'required|min:4',
		'page_url' => 'required|min:10',
		'page_content' => 'required'
             );
	    $messages = [
                  'page_title.required' => 'Please enter page title.',
                  'page_url.required' => 'Please enter page url.',
                  'page_content.required' => 'Please enter page content.',
                  'page_title.min' => 'Page title should be equal or more than 4 character',
                  'page_url.min' => 'Page url  should be equal or more than 10 character'
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
                                $Pages=new Pages();
				$page_title=strtolower($newtodo['page_title']);
				$Pages = Pages::where('page_title',$page_title)->orderBy('page_id','desc')->first();
                		if($Pages)
		                {
                		        $error_code="403";
		                        $message="Page Already Exists for Page Title [$page_title]";
                		        $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result"=>$Pages,"success"=>$success];
	        	                return response()->json($response,$error_code);
		                }
                                $Pages->page_title=strtolower($newtodo['page_title']);
                                $Pages->page_url=$newtodo['page_url'];
                                $Pages->page_content=$newtodo['page_content'];
				$Pages->status=$newtodo['status'];
				if($request->input('redirect_url'))
                                {
                                        $Pages->redirect_url=$request->input('redirect_url');
                                }
				if($request->input('meta_content'))
                                {
                                        $Pages->meta_content=$request->input('meta_content');
                                }
				if($request->input('meta_keyword'))
                                {
                                        $Pages->meta_keyword=$request->input('meta_keyword');
                                }
				if($request->input('meta_title'))
                                {
                                        $Pages->meta_title=$request->input('meta_title');
                                }
				if($request->input('meta_description'))
                                {
                                        $Pages->meta_description=$request->input('meta_description');
                                }

                                DB::beginTransaction();
                                $response=$Pages->save();
                                if(!$response)
                                {
                                        DB::rollback();
					$error_code=503;
					$message="Could not create page.Please try again after some time";
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
				$message="Could not create page.Please try again after some time";
				$response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>$result,"success"=>$success];
				#$response= ["error"=>true,"message" => "Could not create login user .Please try again after some time","status_code"=>403];
				return response()->json($response,$error_code);
                        }
	
	    }		
        }
	public function show()
	{
		$Pages = Pages::get();
		$PagesArr=$Pages->toArray();
		$success=true;
		$error_code="200";
		$message="";		
		#$response= ["error"=>false,"message" =>$UserArr,"status_code"=>200];	
		$response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>$Pages,"success"=>$success];	
		return response()->json($response,$error_code);
	}
	public function get_page_details($page_id)
        {
		$success=false;
		$error_code="200";
		$message="";
		$Pages = Pages::where('page_id',$page_id)->orderBy('page_id','desc')->first();
                #$User = User::query('login_userid',$login_userid);
		if(!$Pages)
		{
			$error_code="403";
			$message="Data Not Found for Page Id $page_id";
			$response= ["error"=>array("code"=>$error_code,"message"=>$message),"result"=>$Pages,"success"=>$success];
			return response()->json($response,$error_code);
		}
		$success=true;	
                $PagesArr=$Pages->toArray();
                $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>[$PagesArr],"success"=>$success];
		return response()->json($response,$error_code);
        }
	public function getpage_by_title($page_title)
	{
		$page_title=urldecode($page_title);
		$success=false;
                $error_code="200";
                $message="";
                $Pages = Pages::where('page_title',$page_title)->orderBy('page_id','desc')->first();
                #$User = User::query('login_userid',$login_userid);
                if(!$Pages)
                {
                        $error_code="403";
                        $message="Data Not Found for Page Title $page_title";
                        $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result"=>$Pages,"success"=>$success];
			return response()->json($response,$error_code);	
                }
                $success=true;
                $PagesArr=$Pages->toArray();
                $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>[$PagesArr],"success"=>$success];
		return response()->json($response,$error_code);
	}
	public function update_page_details(Request $request,$page_id)
	{	
       	    $error_code=200;
            $message="Page Details updated successfully.";
            $success=false;
            $result="";
	    $Pages = Pages::find($page_id);	
            if(!$Pages)
            {
                    $error_code="403";
                    $message="Please check page id.Page details not found for page id $page_id.";
                    $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result"=>$Pages,"success"=>$success];
		    return response()->json($response,$error_code);	
            }		
	    DB::beginTransaction();
	    $Pages->page_id = $page_id;
	    $status=$request->input('status');	
	    if(isset($status))
	    {		
	    	$Pages->status = $status;
	    }	
	    if($request->input('page_title'))
	 		$Pages->page_title = $request->input('page_title');
	    
	    if($request->input('page_url'))
                        $Pages->page_url = $request->input('page_url');
	    if($request->input('page_content'))
                        $Pages->page_content = $request->input('page_content');
	    if($request->input('redirect_url'))
                        $Pages->redirect_url = $request->input('redirect_url');
	    if($request->input('meta_content'))
                        $Pages->meta_content = $request->input('meta_content'); 				    	   if($request->input('meta_keyword'))
                        $Pages->meta_keyword = $request->input('meta_keyword');
	    if($request->input('meta_title'))
                        $Pages->meta_title = $request->input('meta_title'); 	 	
	    if($request->input('meta_description'))
                        $Pages->meta_description = $request->input('meta_description');
 		
            $response=$Pages->save();
	    if(!$response)				    	
	    {
		    DB::rollback();
		    $error_code="403";
                    $message="Unable to update page details for page id $page_id.";
                    $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result"=>$Pages,"success"=>$success];
		    return response()->json($response,$error_code);
	    } 		
	    DB::commit();
	    $PagesArr=$Pages->toArray();
	    $success=true;	
	    $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result"=>$PagesArr,"success"=>$success];	
	    return response()->json($response,$error_code);
	}
}
?>
