<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Cities;
use Validator;
use Illuminate\Http\Request;
use DB;
#use  Hash;
#use Illuminate\Http\Response;
class CityController extends Controller
{
		
        public function create(Request $request)
        {
	    $error_code=200;
	    $message="City Added Successfully";
	    $success=false;
	    $result="";			
	    $data =$request->all();		
            $rules = array(
		'citycode' => 'required|min:2',
		'displayname' => 'required|min:3',
		'status'=>'required',
		'country_id'=>'required|numeric',
		'imageurl'=>'required',
		'smsnumber'=>'required',
		'ispopular'=>'required'
             );
	    $messages = [
                  'citycode.required' => 'Please enter city code',
                  'displayname.required' => 'Please enter display name for city',
                  'status.required' => 'Please choose city status',
		  'country_id.required' => 'Please choose country for the city',
		  'imageurl.required' => 'Please enter imageurl of city',
		  'smsnumber.required' => 'Please enter smsnumber', 	  	
		  'ispopular.required' => 'Please define city is popular or not.', 	  	
                  'displayname.min' => 'Display name should be equal or more than 3 character',
                  'citycode.min' => 'City Code should be equal or more than 2 character',
		  'country_id.required' => 'Country id should be numeric.' 
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
                                $Cities=new Cities();
				$last = $Cities->latest()->first();
				if($last)
				{
					$lastArr=$last->toArray();	
					$city_id=$lastArr['city_id'];
					$city_id=$city_id+1;	
				}
				else
				{	
					$city_id=1;		
				}	
				$Cities->city_id=$city_id;
                                $Cities->citycode=$newtodo['citycode'];
                                $Cities->displayname=$newtodo['displayname'];
                                $Cities->status=$newtodo['status'];
                                $Cities->country_id=$newtodo['country_id'];
                                $Cities->imageurl=$newtodo['imageurl'];
                                $Cities->smsnumber=$newtodo['smsnumber'];
                                $Cities->ispopular=$newtodo['ispopular'];

				if($request->input('phonenumber'))
		                        $Cities->phonenumber = $request->input('phonenumber');
				if($request->input('tagline'))
				$Cities->tagline = $request->input('tagline');
				
	                        $Cities->created_at=date("Y-m-d H:i:s");
	                        $Cities->updated_at=date("Y-m-d H:i:s");
                                $response=$Cities->save();
                                if(!$response)
                                {
					$error_code=503;
					$message="Could not add country .Please try again after some time";
					#$response= ["error"=>true,"message" => "Could not create login user .Please try again after some time","status_code"=>403];
					$response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>$result,"success"=>$success];					     return response()->json($response,$error_code);		
                                }
				$success=true;
				$response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>$result,"success"=>$success];	
				#$response= ["error"=>false,"message" => "","status_code"=>200];
				return response()->json($response,$error_code);
                        }
                        catch(Exception $e)
                        {
				$error_code="502";
				$message="Could not add country .Please try again after some time";
				$response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>$result,"success"=>$success];
				#$response= ["error"=>true,"message" => "Could not create login user .Please try again after some time","status_code"=>403];
				return response()->json($response,$error_code);
                        }
	
	    }		
        }
	public function show()
	{
		$Cities = Cities::get();
		$CitiesArr=$Cities->toArray();
		$success=true;
		$error_code="200";
		$message="";		
		#$response= ["error"=>false,"message" =>$UserArr,"status_code"=>200];	
		$response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>$CitiesArr,"success"=>$success];	
		return response()->json($response,$error_code);
	}
	public function getcity($city_id)
        {
		$success=false;
		$error_code="200";
		$message="";
                #$Cities = Cities::query('city_id',$city_id);
		$city_id=(int)$city_id;
		$Cities = Cities::where('city_id', '=', $city_id)->first();
		if(!$Cities)
		{
			$error_code="403";
			$message="Data Not Found for Cities Id $city_id";
			$response= ["error"=>array("code"=>$error_code,"message"=>$message),"result"=>$Cities,"success"=>$success];
			return response()->json($response,$error_code);
		}
		$success=true;	
                $CitiesArr=$Cities->toArray();
                $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>[$CitiesArr],"success"=>$success];
		return response()->json($response,$error_code);
        }
	public function get_popular_city()
        {
                $success=false;
                $error_code="200";
                $message="";
                #$Cities = Cities::query('city_id',$city_id);
                $Cities = Cities::where('ispopular', '=', "1")->first();
                if(!$Cities)
                {
                        $error_code="403";
                        $message="Data Not Found for Popular City";
                        $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result"=>$Cities,"success"=>$success];
                        return response()->json($response);
                }
                $success=true;
                $CitiesArr=$Cities->toArray();
                $response= ["error"=>array("code"=>$code,"message"=>$message),"result" =>[$CitiesArr],"success"=>$success];
		return response()->json($response,$error_code);
        }
	public function update_city(Request $request,$city_id)
	{	
            if(!$city_id)
	    {
		    $error_code="403";
                    $message="Mandatory parameter missing city id.";
                    $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result"=>$Cities,"success"=>$success];
                    return response()->json($response);
	    }				
       	    $error_code=200;
            $message="City Details updated successfully.";
            $success=false;
            $result="";
	    #$tag_query = Cities::query();
	    $city_id=(int)$city_id;
	    $Cities = Cities::where('city_id', '=', $city_id)->first();	
	    $CitiesArr=$Cities->toArray();	
	    /*$Cities = Cities::raw(function ($collection) use ($city_id) {
		$city_id=(int)$city_id;	
           	return $collection->find(['city_id' => $city_id]);
            }); 	*/
            if(!$Cities)
            {
                    $error_code="403";
                    $message="Please check country id. City details not found for country id $city_id.";
                    $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result"=>$Cities,"success"=>$success];
	            return response()->json($response,$error_code);		
            }		
	    $Cities->city_id = $city_id;
	    $status=$request->input('status');	
	    if(isset($status))
	    {		
	    	$Cities->status = $status;
	    }	
	    if($request->input('displayname'))
	 		$Cities->displayname = $request->input('displayname');
	    
	    if($request->input('citycode'))
                        $Cities->citycode = $request->input('citycode');
	    if($request->input('country_id'))
                        $Cities->country_id = $request->input('country_id');
	    if($request->input('imageurl'))
                        $Cities->imageurl = $request->input('imageurl');
	    if($request->input('smsnumber'))
                        $Cities->smsnumber = $request->input('smsnumber');
	    if($request->input('tagline'))
                        $Cities->tagline = $request->input('tagline');
	     $ispopular=$request->input('ispopular');			
	    if(isset($ispopular))
                        $Cities->ispopular = $ispopular;					

            $response=$Cities->save();	
	    if(!$response)				    	
	    {
		    $error_code="403";
                    $message="Unable to update City details for country id $city_id.";
                    $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result"=>$Cities,"success"=>$success];
		    return response()->json($response,$error_code);	
	    } 		
	    $CitiesArr=$Cities->toArray();
	    $success=true;	
	    $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result"=>$CitiesArr,"success"=>$success];	
	    return response()->json($response,$error_code);
	}
}
?>
