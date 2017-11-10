<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Categories;
use Validator;
use Illuminate\Http\Request;
use DB;
#use  Hash;
#use Illuminate\Http\Response;
class CategoriesController extends Controller
{	
        public function create(Request $request)
        {
	    $error_code=200;
	    $message="Category Added Successfully.";
	    $success=false;
	    $result="";			
	    $data =$request->all();	
            $rules = array(
		'name' => 'required',
		'image_url' => 'required',
		'description' => 'required',
		'status' => 'required'
             );
	    $messages = [
                  'name.required' => 'Please enter category name.',
                  'image_url.required' => 'Please enter image url for category.',
                  'description.required' => 'Please enter category description.',
                  'status.required' => 'Please choose status of categiry.'
            ];
	
            $validator = Validator::make($data, $rules,$messages);
	    if ($validator->fails())
	    {
		$error_code="403";
		$message=$validator->errors();
		$response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>$result,"success"=>$success];	
		return response()->json($response);
	    }
	    else
	    {
		try
                        {
                                $newtodo = $data;
                                $Categories=new Categories();
                                $Categories->cg_name=strtolower($newtodo['name']);
                                $Categories->cg_desc=$newtodo['description'];
				$Categories->status=$newtodo['status'];
				$Categories->cg_imageurl=$newtodo['image_url'];
				if($request->input('thumb_url'))
                                {
                                        $Categories->thumb_url=$request->input('thumb_url');
                                }
                                DB::beginTransaction();
                                $response=$Categories->save();
                                if(!$response)
                                {
                                        DB::rollback();
					$error_code=503;
					$message="Could not create category.Please try again after some time";
					#$response= ["error"=>true,"message" => "Could not create login user .Please try again after some time","status_code"=>403];
					$response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>$result,"success"=>$success];	
	                                return response()->json($response);	
                                }
                                DB::commit();
				$success=true;
				$response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>$result,"success"=>$success];	
				#$response= ["error"=>false,"message" => "","status_code"=>200];
                                return response()->json($response);
                        }
                        catch(Exception $e)
                        {
				$error_code="502";
				$message="Could not create category.Please try again after some time";
				$response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>$result,"success"=>$success];
				#$response= ["error"=>true,"message" => "Could not create login user .Please try again after some time","status_code"=>403];
		                return response()->json($response);	
                        }
	
	    }		
        }
	public function show()
	{
		$Categories = Categories::get();
		$CategoriesArr=$Categories->toArray();
		if(sizeof($CategoriesArr)===0)	
		{
			$success=false;
	                $error_code="403";
        	        $message="No Data Found";
			 $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>$Categories,"success"=>$success];
			return response()->json($response);
		}
		$success=true;
		$error_code="200";
		$message="";		
		#$response= ["error"=>false,"message" =>$UserArr,"status_code"=>200];	
		$response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>$Categories,"success"=>$success];	
		return response()->json($response);
	}
	public function get_category_details($category_id)
        {
		$success=false;
		$error_code="200";
		$message="";
		$Categories = Categories::where('cg_id',$category_id)->orderBy('cg_id','desc')->first();
                #$User = User::query('login_userid',$login_userid);
		if(!$Categories)
		{
			$error_code="403";
			$message="Data Not Found for Category Id $category_id";
			$response= ["error"=>array("code"=>$error_code,"message"=>$message),"result"=>$Categories,"success"=>$success];
	                return response()->json($response);
		}
		$success=true;	
                $CategoriesArr=$Categories->toArray();
                $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>[$CategoriesArr],"success"=>$success];
                return response()->json($response);
        }
	public function update_category_details(Request $request,$category_id)
	{	
       	    $error_code=200;
            $message="Category Details updated successfully.";
            $success=false;
            $result="";
	    $Categories = Categories::find($category_id);	
            if(!$Categories)
            {
                    $error_code="403";
                    $message="Please check category id.Category details not found for category id $category_id.";
                    $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result"=>$Categories,"success"=>$success];
                    return response()->json($response);
            }		
	    DB::beginTransaction();
	    $Categories->cg_id = $category_id;
	    $status=$request->input('status');	
	    if(isset($status))
	    {		
	    	$Categories->status = $status;
	    }	
	    if($request->input('name'))
	 		$Categories->cg_name = $request->input('name');
	    
	    if($request->input('description'))
                        $Categories->cg_desc = $request->input('description');
	    if($request->input('thumb_url'))
                        $Categories->thumb_url = $request->input('thumb_url');
	    if($request->input('image_url'))
                        $Categories->cg_imageurl = $request->input('image_url');
            $response=$Categories->save();
	    if(!$response)				    	
	    {
		    DB::rollback();
		    $error_code="403";
                    $message="Unable to update category details for page id $category_id.";
                    $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result"=>$Categories,"success"=>$success];
                    return response()->json($response);	
	    } 		
	    DB::commit();
	    $CategoriesArr=$Categories->toArray();
	    $success=true;	
	    $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result"=>$CategoriesArr,"success"=>$success];	
	    return response()->json($response);
	}
}
?>
