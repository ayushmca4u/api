<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Banner;
use App\Activities;
use App\Categories;
use App\Cities;
use Validator;
use Illuminate\Http\Request;
use DB;
#use  Hash;
#use Illuminate\Http\Response;
class HomeController_new extends Controller
{
		
	public function show()
	{
		$Banner = Banner::paginate(3);	
	//	$Banner = Banner::get();
		if($Banner)
		{
			$BannerArr=$Banner->toArray();
			$BannerArr=$BannerArr['data'];
			foreach($BannerArr as $key=>$banner_details)
			{
				$banner_image_url="";
				$urlArr=explode("//",$banner_details['imageurl']);
				$urlArr2=explode("/",$urlArr[1]);
				$banner_image_url="https://".$urlArr2[0]."/".$urlArr2[1]."/1200-600/".$urlArr2[3]."/".$urlArr2[4];
				//$responseArr['crousel'][]=str_replace("200-200","800-600",$banner_details['imageurl']);
				$responseArr['crousel'][]=$banner_image_url;
			}
			//$responseArr['crousel']=$BannerArr['data'];
		}
		$Cities = Cities::where('ispopular', '=', "1")->paginate(4);
		if($Cities)
		{
			$CitiesArr=$Cities->toArray();
			$CitiesArr=$CitiesArr['data'];	
		 	foreach($CitiesArr as $key=>$city_details)
                        {
                                $responseArr['popularDestinations']['destination'.($key+1)]['id']=$city_details['city_id'];
                                $responseArr['popularDestinations']['destination'.($key+1)]['name']=$city_details['displayname'];
                                $responseArr['popularDestinations']['destination'.($key+1)]['imageUrl']=$city_details['imageurl'];
                        }
		}
		$Activities = Activities::where('status', '=', "1")->paginate(4);
		if($Activities)
		{
			$ActivitiesArr=$Activities->toArray();
			$ActivitiesArr=$ActivitiesArr['data'];
			foreach($ActivitiesArr as $key=>$activity_details)
                        {
				$activity_id=$activity_details['activity_id'];
				$responseArr['popularActivities'][$activity_details['name']]['id']=$activity_id;
                                $responseArr['popularActivities'][$activity_details['name']]['name']=$activity_details['name'];
				$image_mapping = DB::table('activity_images')->select('activity_id','content_url as image_url','description','filename','mime_type','upload_type as file_type')->where('activity_id', [$activity_id])->where('upload_type', 'image')->get();
				$imageArr=$image_mapping->toArray();
				foreach($imageArr as $key=>$image_details)
				{
					$mapping=(array)$image_details;
                                	$responseArr['popularActivities'][$activity_details['name']]['imageurl']=$mapping['image_url'];
				}
				
				$video_mapping = DB::table('activity_images')->select('activity_id','content_url as video_url','description','filename','mime_type','upload_type as file_type')->where('activity_id', [$activity_id])->where('upload_type', 'video')->get();
                        //$mapping=$city_mapping->toArray();
				$videoArr=$video_mapping->toArray();
                                foreach($videoArr as $key=>$video_details)
                                {
                                        $mapping1=(array)$video_details;
                                        $responseArr['popularActivities'][$activity_details['name']]['videourl']=$mapping1['video_url'];
                                }	
                               $responseArr['popularActivities'][$activity_details['name']]['subHeading']=$activity_details['neighborhood'];
			}	
			
			$responseArr['travelInspiration']=$responseArr['popularActivities'];
			$responseArr['featuredExperience'][1]['name']="Food Time";
			$responseArr['featuredExperience'][1]['subHeading']="Plan your Asia travels around the delicate pink sakura of cherry blossom season!";
			$responseArr['featuredExperience'][1]['imageUrl']="https://imgs.ticketstodo.com/imgs/200-200/t/tile7.jpg";
			$responseArr['TicketsToDoRecomended']=$responseArr['popularActivities'];
		}
		/*
		if($Categories)
		{
			$CategoriesArr=$Categories->toArray();
                        $CategoriesArr=$CategoriesArr['data'];
			foreach($CategoriesArr as $key=>$category_details)
			{
				$responseArr['popularActivities'][$category_details['cg_name']]['id']=$category_details['cg_id'];
				$responseArr['popularActivities'][$category_details['cg_name']]['imageurl']=$category_details['cg_imageurl'];
				$responseArr['popularActivities'][$category_details['cg_name']]['name']=$category_details['cg_name'];
				$responseArr['popularActivities'][$category_details['cg_name']]['subHeading']=$category_details['cg_desc'];
			}
			$responseArr['travelInspiration']=$responseArr['popularActivities'];
			$responseArr['featuredExperience'][1]['name']="Food Time";
			$responseArr['featuredExperience'][1]['subHeading']="Plan your Asia travels around the delicate pink sakura of cherry blossom season!";
			$responseArr['featuredExperience'][1]['imageUrl']="https://imgs.ticketstodo.com/imgs/200-200/t/tile7.jpg";
			$responseArr['TicketsToDoRecomended']=$responseArr['popularActivities'];
		}*/
		//$Categories = Categories::paginate(4);	
		$success=true;
		$error_code="200";
		$message="";		
		#$response= ["error"=>false,"message" =>$UserArr,"status_code"=>200];	
		$response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>$responseArr,"success"=>$success];	
		return response()->json($response);
	}
	public function sendemail()
	{
	   $user = auth()->user();
	}
}
?>
