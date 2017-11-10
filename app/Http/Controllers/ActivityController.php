<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Activities;
use App\Packages;
use App\Packages_schedule;
use App\Packages_price;
use App\Packages_booking;
use App\Packages_access;
use Validator;
use Illuminate\Http\Request;
use DB;
class ActivityController extends Controller
{		
        public function create(Request $request)
        {
	    $error_code=200;
	    $message="Activity Added Successfully.";
	    $success=false;
	    $result="";			
	    $data =$request->all();
	    $log_prefix=date("Ymd H:i:s");
	    $log_file_name="add_activity".date('Ymd').".log";
            $file_upload_log="/var/tmp/".$log_file_name;
            error_log("\n$log_prefix Data Request ".print_r($data,true)."",3,$file_upload_log);
            error_log("\n$log_prefix Post Request ".print_r($_POST,true)."",3,$file_upload_log);
            $rules = array(
		'neighborhood' => 'required|min:6',
		'name'=>'required',
		'merchant_id'=>'required',
		'address' => 'required|min:20',
		'city_id' => 'required',
		'type' => 'required',
		'venue_location' => 'required|min:20'
                //'cities' => 'required'	
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
                                $Activities=new Activities();
                                $Activities->neighborhood=$newtodo['neighborhood'];
                                $Activities->merchant_id=$newtodo['merchant_id'];
                                $Activities->name=$newtodo['name'];
                                $Activities->address=$newtodo['address'];
                                $Activities->city_id=$newtodo['city_id'];
				$Activities->status=$newtodo['status'];
				$Activities->type=$newtodo['type'];
				$Activities->venue_location=$newtodo['venue_location'];
				if($request->input('google_map_link'))
                                {
                                        $Activities->google_map_link=$request->input('google_map_link');
                                }
				if($request->input('location_map'))
                                {
                                        $Activities->location_map=$request->input('location_map');
                                }
				if($request->input('tip1'))
                                {
					$Activities->cashtip=1;
					$Activities->tip1=$request->input('tip1');
                                }
				if($request->input('tip2'))
                                {
                                        $Activities->cashtip=1;
                                        $Activities->tip2=$request->input('tip2');
                                }
				if($request->input('calltoaction'))
                                {
                                        $Activities->calltoaction=$request->input('calltoaction');
                                }
				if($request->input('reviews'))
                                {
                                        $Activities->reviews=$request->input('reviews');
                                }
				if($request->input('images'))
					$Activities->activity_images=1;
				if($request->input('videos'))
					$Activities->activity_videos=1;
			
                                DB::beginTransaction();
                                $response=$Activities->save();
                                if(!$response)
                                {
                                        DB::rollback();
					$error_code=503;
					$message="Could not create Activity.Please try again after some time";
					#$response= ["error"=>true,"message" => "Could not create login user .Please try again after some time","status_code"=>403];
					$response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>$result,"success"=>$success];
					return response()->json($response,$error_code);	
                                }
				$activity_id = $Activities->activity_id; // last inserted id for merchants
				$datetime=date("Y-m-d H:i:s");
				$images=$request->input('images');	
            		//	error_log("\n$log_prefix Images  Object".print_r($images[0],true)."",3,$file_upload_log);
            			//error_log("\n$log_prefix Images  Object".$images[0]->activity_id."activity_id",3,$file_upload_log);
				if($request->input('images'))
				{	
					$images=$request->input('images');
					foreach($images as $key=>$images_details)
					{
						$upload_type="image";
						$description=$images_details['description'];
						$filename=$images_details['filename'];
						$file_type=$images_details['file_type'];
						$image_url=$images_details['image_url'];
						$mime_type="image/jpeg";
						$response=DB::table('activity_images')->insert(['activity_id' =>$activity_id, 'upload_type' =>$upload_type,'description' =>"$description",'filename' =>"$filename",'file_type' =>"$file_type",'mime_type'=>"$mime_type",'content_url'=>"$image_url",'created_at' => "$datetime",'updated_at' => "$datetime"]);		
						if(!$response)
						{
							DB::rollback();
                	                 	        $error_code=503;
                        	                	$message="Could not create Activity.Please try again after some time";
                                	        	$response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>$result,"success"=>$success];
                                        		return response()->json($response,$error_code);			
						}	
					}
				}
				$upload_type="";
				$description="";
				$filename="";
				$file_type="";
				$mime_type="";	
				if($request->input('videos'))
				{
					$videos=$request->input('videos');
					foreach($videos as $key=>$videos_details)
                	                {
                        	                $upload_type="video";
                                	        $description=$videos_details['description'];
                                        	$filename=$videos_details['filename'];
	                                        $file_type=$videos_details['file_type'];
        	                                $mime_type="video/mp4";
						$video_url=$videos_details['video_url'];
                        	                $response=DB::table('activity_images')->insert(['activity_id' =>$activity_id, 'upload_type' =>$upload_type,'description' =>"$description",'filename' =>"$filename",'file_type' =>"$file_type",'mime_type'=>"$mime_type",'content_url'=>"$video_url",'created_at' => "$datetime",'updated_at' => "$datetime"]);
                                	        if(!$response)
                                        	{
                                                	DB::rollback();
	                                                $error_code=503;
        	                                        $message="Could not create Activity.Please try again after some time";
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
				$message="Could not create Activity .Please try again after some time";
				$response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>$result,"success"=>$success];
				#$response= ["error"=>true,"message" => "Could not create login user .Please try again after some time","status_code"=>403];
				return response()->json($response,$error_code);
                        }
	
	    }		
        }
	public function show()
	{
		$Activities = Activities::get();
		$ActivitiesArr=$Activities->toArray();
		foreach($ActivitiesArr as $key=>$activity_details)
		{
			$activity_id=$activity_details['activity_id'];
			$image_mapping = DB::table('activity_images')->select('activity_id','content_url as image_url','description','filename','mime_type','upload_type as file_type')->where('activity_id', [$activity_id])->where('upload_type', 'image')->get();		
			//$mapping=$city_mapping->toArray(); 
			$mapping=json_decode($image_mapping,true);
			$ActivitiesArr[$key]['images']=$mapping;			
				
			 $video_mapping = DB::table('activity_images')->select('activity_id','content_url as video_url','description','filename','mime_type','upload_type as file_type')->where('activity_id', [$activity_id])->where('upload_type', 'video')->get();
                        //$mapping=$city_mapping->toArray();
                        $mapping=json_decode($video_mapping,true);
                        $ActivitiesArr[$key]['videos']=$mapping;
		}	
		// $products = DB::table('products')->where('status', [$status])->get();		
		$success=true;
		$error_code="200";
		$message="";		
		#$response= ["error"=>false,"message" =>$UserArr,"status_code"=>200];	
		$response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>$ActivitiesArr,"success"=>$success];	
		return response()->json($response,$error_code);
	}
	public function get_activity_details($activity_id)
        {
		$success=false;
		$error_code="200";
		$message="";
		$Activities = Activities::where('activity_id',$activity_id)->orderBy('activity_id','desc')->first();
                #$User = User::query('login_userid',$login_userid);
		if(!$Activities)
		{
			$error_code="403";
			$message="Data Not Found for Activity Id $activity_id";
			$response= ["error"=>array("code"=>$error_code,"message"=>$message),"result"=>$Activities,"success"=>$success];
			return response()->json($response,$error_code);
		}
                $ActivitiesArr=$Activities->toArray();
		$image_mapping = DB::table('activity_images')->select('activity_id','content_url as image_url','description','filename','mime_type','upload_type as file_type')->where('activity_id', [$activity_id])->where('upload_type', 'image')->get();
                        //$mapping=$city_mapping->toArray();
                        $mapping=json_decode($image_mapping,true);
                        $ActivitiesArr['images']=$mapping;

                         $video_mapping = DB::table('activity_images')->select('activity_id','content_url as video_url','description','filename','mime_type','upload_type as file_type')->where('activity_id', [$activity_id])->where('upload_type', 'video')->get();
                        //$mapping=$city_mapping->toArray();
                        $mapping=json_decode($video_mapping,true);
                        $ActivitiesArr['videos']=$mapping;		
		//echo  "<pre>";print_r($ActivitiesArr);die;

		$success=true;		
                $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>[$ActivitiesArr],"success"=>$success];
		return response()->json($response,$error_code);
        }
	public function get_activity_pacakge_details($activity_id)
	{
		$success=false;
                $error_code="200";
                $message="";
                $Activities = Activities::where('activity_id',$activity_id)->orderBy('activity_id','desc')->first();
                #$User = User::query('login_userid',$login_userid);
                if(!$Activities)
                {
                        $error_code="403";
                        $message="Data Not Found for Activity Id $activity_id";
                        $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result"=>$Activities,"success"=>$success];
                        return response()->json($response,$error_code);
                }
                $ActivitiesArr=$Activities->toArray();
                $image_mapping = DB::table('activity_images')->select('activity_id','content_url as image_url','description','filename','mime_type','upload_type as file_type')->where('activity_id', [$activity_id])->where('upload_type', 'image')->get();
                        //$mapping=$city_mapping->toArray();
                $mapping=json_decode($image_mapping,true);
                $ActivitiesArr['images']=$mapping;

                $video_mapping = DB::table('activity_images')->select('activity_id','content_url as video_url','description','filename','mime_type','upload_type as file_type')->where('activity_id', [$activity_id])->where('upload_type', 'video')->get();
                //$mapping=$city_mapping->toArray();
                $mapping=json_decode($video_mapping,true);
                $ActivitiesArr['videos']=$mapping;
                //echo  "<pre>";print_r($ActivitiesArr);die;
		$Packages = Packages::where('activity_id',$activity_id)->orderBy('package_id','desc')->get();
                $PackagesArr=$Packages->toArray();
                foreach($PackagesArr as $key=>$package_details)
                {
                        $package_id=$package_details['package_id'];
                        $price_details = DB::table('package_price_details')->select('*')->where('package_id', [$package_id])->get();
                        //$mapping=$city_mapping->toArray();
                        $price_details=json_decode($price_details,true);
                        $PackagesArr[$key]['price_details']=$price_details;

                         $schedule_details = DB::table('package_schedule_details')->select('*')->where('package_id', [$package_id])->get();
                        //$mapping=$city_mapping->toArray();
                        $schedule_details=json_decode($schedule_details,true);
                        $PackagesArr[$key]['schedule_details']=$schedule_details;

                        $booking_details = DB::table('package_booking_details')->select('*')->where('package_id', [$package_id])->get();
                        //$mapping=$city_mapping->toArray();
                        $booking_details=json_decode($booking_details,true);
                        $PackagesArr[$key]['booking_details']=$booking_details;
                        //access_details
                        $access_details = DB::table('package_access_details')->select('*')->where('package_id', [$package_id])->get();
                //$mapping=$city_mapping->toArray();
                        $access_details=json_decode($access_details,true);
                        $access_details=$access_details[0];
                        if($access_details['allowed_age_group']==1)
                        {
                                 $package_age_restriction = DB::table('package_age_restriction')->select('age_group_attr')->where('package_id', [$package_id])->get();
				$package_age_restriction=json_decode($package_age_restriction,true);
                                $access_details['age_groups']=$package_age_restriction;
                        }
                        $PackagesArr[$key]['access_details']=$access_details;
                }
		$ActivitiesArr['package_details']=$PackagesArr;
                $success=true;	
		$response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>[$ActivitiesArr],"success"=>$success];
                return response()->json($response,$error_code);
	}	
	public function update_activity_details(Request $request,$activity_id)
	{	
       	    $error_code=200;
            $message="Activity Details updated successfully.";
            $success=false;
            $result="";
	    $Activities = Activities::find($activity_id);	
            if(!$Activities)
            {
                    $error_code="403";
                    $message="Please check activity id.Activity details not found for activity id $merchant_id.";
                    $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result"=>$Activities,"success"=>$success];
		    return response()->json($response,$error_code);	
            }		
	    DB::beginTransaction();
	    $Activities->activity_id = $activity_id;
	    $status=$request->input('status');
	    if(isset($status))
	    {		
	    	$Activities->status = $status;
	    }
	    if($request->input('neighborhood'))
	 		$Activities->neighborhood = $request->input('neighborhood');
	    
	    if($request->input('address'))
                        $Activities->address = $request->input('address');
	    if($request->input('name'))
                        $Activities->name = $request->input('name'); 	
		
	    if($request->input('city_id'))
                        $Activities->city_id = $request->input('city_id');
	    if($request->input('type'))
                        $Activities->type = $request->input('type');
	    if($request->input('venue_location'))
                        $Activities->venue_location = $request->input('venue_location'); 				    	  	      if($request->input('google_map_link'))
                        $Activities->google_map_link = $request->input('google_map_link');
	     if($request->input('location_map'))
                        $Activities->location_map = $request->input('location_map');
		 if($request->input('calltoaction'))
                        $Activities->calltoaction = $request->input('calltoaction');
		 if($request->input('reviews'))
                        $Activities->reviews = $request->input('reviews');	

			if($request->input('tip1'))
                                {
                                        $Activities->cashtip=1;
                                        $Activities->tip1=$request->input('tip1');
                                }
                                if($request->input('tip2'))
                                {
                                        $Activities->cashtip=1;
                                        $Activities->tip2=$request->input('tip2');
                                }	

            $response=$Activities->save();
	    if(!$response)				    	
	    {
		    DB::rollback();
		    $error_code="403";
                    $message="Unable to update activity details for activity  id $merchant_id.";
                    $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result"=>$Activities,"success"=>$success];
		    return response()->json($response,$error_code);
	    } 		
	    if($request->input('images'))
	    {	
	    	$images=$request->input('images');
                $datetime=date("Y-m-d H:i:s");
		$delete_response=DB::table('activity_images')->where('activity_id', '=', $activity_id)->where('upload_type', '=', 'image')->delete();	
		if(!$delete_response)
		{
			DB::rollback();
                        $error_code=503;
                        $message="Could not update Activity Details.Please try again after some time";
                        $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>$result,"success"=>$success];
                        return response()->json($response,$error_code);
		}
		foreach($images as $key=>$images_details)
                {
                        $upload_type="image";
                        $description=$images_details['description'];
                        $filename=$images_details['filename'];
                        $file_type=$images_details['file_type'];
                        $image_url=$images_details['image_url'];
                        $mime_type="image/jpeg";
                        $response=DB::table('activity_images')->insert(['activity_id' =>$activity_id, 'upload_type' =>$upload_type,'description' =>"$description",'filename' =>"$filename",'file_type' =>"$file_type",'mime_type'=>"$mime_type",'content_url'=>"$image_url",'created_at' => "$datetime",'updated_at' => "$datetime"]);
                        if(!$response)
                        {
                                DB::rollback();
                                $error_code=503;
                                $message="Could not create Activity.Please try again after some time";
                                $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>$result,"success"=>$success];
                                return response()->json($response,$error_code);
                        }
                }
	    }	
	    if($request->input('videos'))
		{			
			$upload_type="";
        	        $description="";
                	$filename="";
	                $file_type="";
        	        $mime_type="";
			$videos=$request->input('videos');	
			$delete_response=DB::table('activity_images')->where('activity_id', '=', $activity_id)->where('upload_type', '=', 'video')->delete();
	                if(!$delete_response)
        	        {
                	        DB::rollback();
                        	$error_code=503;
	                        $message="Could not update Activity Details.Please try again after some time";
        	                $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>$result,"success"=>$success];
                	        return response()->json($response,$error_code);
                	}
			foreach($videos as $key=>$videos_details)
                        {
                                $upload_type="video";
                                $description=$videos_details['description'];
                                $filename=$videos_details['filename'];
                                $file_type=$videos_details['file_type'];
                                $mime_type="video/mp4";
                                $video_url=$videos_details['video_url'];
                                $response=DB::table('activity_images')->insert(['activity_id' =>$activity_id, 'upload_type' =>$upload_type,'description' =>"$description",'filename' =>"$filename",'file_type' =>"$file_type",'mime_type'=>"$mime_type",'content_url'=>"$video_url",'created_at' => "$datetime",'updated_at' => "$datetime"]);
                                if(!$response)
                                {
                                        DB::rollback();
                                        $error_code=503;
                                        $message="Could not create Activity.Please try again after some time";
                                        $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>$result,"success"=>$success];
                                        return response()->json($response,$error_code);
                                }
                        }
		
		}	
	    DB::commit();
	    $ActivitiesArr=$Activities->toArray();
	    $success=true;	
	    $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result"=>$ActivitiesArr,"success"=>$success];	
	    return response()->json($response,$error_code);
	}
	public function get_merchant_activity_details($merchant_id,$activity_id)
        {
                $success=false;
                $error_code="200";
                $message="";
		$Activities = Activities::where('merchant_id',$merchant_id)->where('activity_id',$activity_id)->orderBy('activity_id','desc')->first();	
                #$User = User::query('login_userid',$login_userid);
                if(!$Activities)
                {
                        $error_code="403";
                        $message="Data Not Found for Activity Id $activity_id of merhcant_id $merchant_id";
                        $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result"=>$Activities,"success"=>$success];
                        return response()->json($response,$error_code);
                }
                $ActivitiesArr=$Activities->toArray();
                $image_mapping = DB::table('activity_images')->select('activity_id','content_url as image_url','description','filename','mime_type','upload_type as file_type')->where('activity_id', [$activity_id])->where('upload_type', 'image')->get();
                        //$mapping=$city_mapping->toArray();
                        $mapping=json_decode($image_mapping,true);
                        $ActivitiesArr['images']=$mapping;

                         $video_mapping = DB::table('activity_images')->select('activity_id','content_url as video_url','description','filename','mime_type','upload_type as file_type')->where('activity_id', [$activity_id])->where('upload_type', 'video')->get();
                        //$mapping=$city_mapping->toArray();
                        $mapping=json_decode($video_mapping,true);
                        $ActivitiesArr['videos']=$mapping;
                //echo  "<pre>";print_r($ActivitiesArr);die;
		$success=true;
                $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>[$ActivitiesArr],"success"=>$success];
                return response()->json($response,$error_code);
        }	
 	public function show_merchant_activities($merchant_id)
        {
		$success=false;
                $error_code="200";
                $message="";
                $Activities = Activities::where('merchant_id',$merchant_id)->orderBy('activity_id','desc')->get();
                $ActivitiesArr=$Activities->toArray();
                #$User = User::query('login_userid',$login_userid);
                if(empty($ActivitiesArr))
                {
                        $error_code="403";
                        $message="Activity Details Not Found for merhcant_id $merchant_id";
                        $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result"=>$Activities,"success"=>$success];
                        return response()->json($response,$error_code);
                }
                foreach($ActivitiesArr as $key=>$activity_details)
                {
                        $activity_id=$activity_details['activity_id'];
                        $image_mapping = DB::table('activity_images')->select('activity_id','content_url as image_url','description','filename','mime_type','upload_type as file_type')->where('activity_id', [$activity_id])->where('upload_type', 'image')->get();
                        //$mapping=$city_mapping->toArray();
                        $mapping=json_decode($image_mapping,true);
                        $ActivitiesArr[$key]['images']=$mapping;

                         $video_mapping = DB::table('activity_images')->select('activity_id','content_url as video_url','description','filename','mime_type','upload_type as file_type')->where('activity_id', [$activity_id])->where('upload_type', 'video')->get();
                        //$mapping=$city_mapping->toArray();
                        $mapping=json_decode($video_mapping,true);
                        $ActivitiesArr[$key]['videos']=$mapping;
                }
                // $products = DB::table('products')->where('status', [$status])->get();
                $success=true;
                $error_code="200";
                $message="";
                #$response= ["error"=>false,"message" =>$UserArr,"status_code"=>200];
                $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>$ActivitiesArr,"success"=>$success];
                return response()->json($response,$error_code);
        }
	public function show_city_activities($city_id)
        {
                $success=false;
                $error_code="200";
                $message="";
                $Activities = Activities::where('city_id',$city_id)->orderBy('activity_id','desc')->get();
		$ActivitiesArr=$Activities->toArray();
                if(empty($ActivitiesArr))
                {
                        $error_code="403";
                        $message="Activity Details Not Found for city_id $city_id";
                        $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result"=>$ActivitiesArr,"success"=>$success];
                        return response()->json($response,$error_code);
                }
                foreach($ActivitiesArr as $key=>$activity_details)
                {
                        $activity_id=$activity_details['activity_id'];
                        $image_mapping = DB::table('activity_images')->select('activity_id','content_url as image_url','description','filename','mime_type','upload_type as file_type')->where('activity_id', [$activity_id])->where('upload_type', 'image')->get();
                        //$mapping=$city_mapping->toArray();
                        $mapping=json_decode($image_mapping,true);
                        $ActivitiesArr[$key]['images']=$mapping;

                         $video_mapping = DB::table('activity_images')->select('activity_id','content_url as video_url','description','filename','mime_type','upload_type as file_type')->where('activity_id', [$activity_id])->where('upload_type', 'video')->get();
                        //$mapping=$city_mapping->toArray();
                        $mapping=json_decode($video_mapping,true);
                        $ActivitiesArr[$key]['videos']=$mapping;
		}
                // $products = DB::table('products')->where('status', [$status])->get();
                $success=true;
                $error_code="200";
                $message="";
                #$response= ["error"=>false,"message" =>$UserArr,"status_code"=>200];
                $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>$ActivitiesArr,"success"=>$success];
                return response()->json($response,$error_code);
        }
}
?>
