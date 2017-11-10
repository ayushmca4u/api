<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Banner;
use App\Categories;
use App\Cities;
use Validator;
use Illuminate\Http\Request;
use DB;
#use  Hash;
#use Illuminate\Http\Response;
class HomeController extends Controller
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
		$Categories = Categories::paginate(4);
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
		}
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
