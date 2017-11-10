<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
class LanguageLocalizationController extends Controller
{
    public function home(Request $request,$lang)
    {
	$home_path="/home/httpd/htdocs/stgapi/resources/lang/$lang/alert.php";
	if(file_exists($home_path))	
	{
		$return=app('translator')->setLocale($lang);
		$value = config('app.locale');
		echo trans('alert.success');
	}
	else
	{
		$error_code=403;
		$success=false;
		$message="Template not supported for Langauge [$lang]";		
		$response= ["error"=>array("error_code"=>$error_code,"message"=>$message),"result"=>"","success"=>$success];
                return response()->json($response,$error_code);
		
	}	
    }
}
