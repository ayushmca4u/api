<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Banner;
use Validator;
use Illuminate\Http\Request;
use DB;
#use  Hash;
#use Illuminate\Http\Response;
class BannerController extends Controller
{
		
        public function create(Request $request)
        {
	    $error_code=200;
	    $message=" Banner Added Successfully";
	    $success=false;
	    $result="";			
	    $data =$request->all();		
            $rules = array(
		'title' => 'required|min:4',
		'subtitle' => 'required|min:4',
		'imageurl' => 'required',
                'buttontext' => 'required',
		'status' => 'required',
             );
		$messages = [
                  'title.required' => 'Please enter banner title.',
                  'subtitle.required' => 'Please enter sub title.',
                  'imageurl.required' => 'Please enter image URL of the banner.',
			      'buttontext.required' => 'Please enter button text for banner.',
			      'status.required' => 'Please choose banner status.',
                  'title.email' => 'Title length should be equal or  more than 4 character.',
				  'subtitle.min' => 'Sub Title length should be equal or  more than 4 character.' 	
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
                                $Banner=new Banner();
				$last = $Banner->latest()->first();
				if($last)
				{
					$lastArr=$last->toArray();	
					$banner_id=$lastArr['banner_id'];
					$banner_id=$banner_id+1;	
				}
				else
				{	
					$banner_id=1;		
				}	
				$Banner->banner_id=$banner_id;
                                $Banner->title=$newtodo['title'];
                                $Banner->subtitle=$newtodo['subtitle'];
                                $Banner->imageurl=$newtodo['imageurl'];
				$Banner->buttontext=$newtodo['buttontext'];
				if($request->input('redirectlink'))	
				{
	                        	$Banner->redirectlink=$request->input('redirectlink');
				}
	                        $Banner->created_at=date("Y-m-d H:i:s");
	                        $Banner->updated_at=date("Y-m-d H:i:s");
                                $response=$Banner->save();
                                if(!$response)
                                {
					$error_code=503;
					$message="Could not add banner .Please try again after some time";
					#$response= ["error"=>true,"message" => "Could not create login user .Please try again after some time","status_code"=>403];
					$response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>$result,"success"=>$success];	
					 return response()->json($response,$error_code);
                                }
				$success=true;
				$response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>$result,"success"=>$success];	
				#$response= ["error"=>false,"message" => "","status_code"=>200];
				 return response()->json($response,$error_code);
                        }
                        catch(Exception $e)
                        {
				$error_code="502";
				$message="Could not add banner .Please try again after some time";
				$response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>$result,"success"=>$success];
				#$response= ["error"=>true,"message" => "Could not create login user .Please try again after some time","status_code"=>403];
				 return response()->json($response,$error_code);
                        }
	
	    }		
        }
	public function show()
	{
		$Banner = Banner::get();
		$BannerArr=$Banner->toArray();
		$success=true;
		$error_code="200";
		$message="";		
		#$response= ["error"=>false,"message" =>$UserArr,"status_code"=>200];	
		$response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>$BannerArr,"success"=>$success];	
		return response()->json($response);
	}
	public function getbanner($banner_id)
        {
		$success=false;
		$error_code="200";
		$message="";
                #$Banner = Banner::query('banner_id',$banner_id);
		$banner_id=(int)$banner_id;
                $Banner = Banner::where('banner_id', '=', $banner_id)->first();
		if(!$Banner)
		{
			$error_code="403";
			$message="Data Not Found for Banner Id $banner_id";
			$response= ["error"=>array("code"=>$error_code,"message"=>$message),"result"=>$Banner,"success"=>$success];
	                return response()->json($response);
		}
		$success=true;	
                $BannerArr=$Banner->toArray();
                $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>[$BannerArr],"success"=>$success];
                return response()->json($response);
        }
	public function update_banner(Request $request,$banner_id)
	{	
       	    $error_code=200;
            $message="Banner Details updated successfully.";
            $success=false;
            $result="";
	    #$Banner = Banner::find($banner_id);	
	    $banner_id=(int)$banner_id;
            $Banner = Banner::where('banner_id', '=', $banner_id)->first();	
            if(!$Banner)
            {
                    $error_code="403";
                    $message="Please check banner id. Banner details not found for banner id $banner_id.";
                    $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result"=>$Banner,"success"=>$success];
                    return response()->json($response);
            }		
	    $Banner->banner_id = $banner_id;
	    $status=$request->input('status');	
	    if(isset($status))
	    {		
	    	$Banner->status = $status;
	    }	
	    if($request->input('title'))
	 		$Banner->title = $request->input('title');
	    
	    if($request->input('subtitle'))
                        $Banner->subtitle = $request->input('subtitle');
	    if($request->input('imageurl'))
                        $Banner->imageurl = $request->input('imageurl');
	    if($request->input('buttontext'))
                        $Banner->buttontext = $request->input('buttontext');
	    if($request->input('redirectlink'))
                        $Banner->redirectlink = $request->input('redirectlink');			
            $response=$Banner->save();	
	    if(!$response)				    	
	    {
		    $error_code="403";
                    $message="Unable to update Banner details for banner id $banner_id.";
                    $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result"=>$AdminUser,"success"=>$success];
                    return response()->json($response);	
	    } 		
	    $BannerArr=$Banner->toArray();
	    $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result"=>$BannerArr,"success"=>$success];	
	    return response()->json($response);
	}
}
?>
