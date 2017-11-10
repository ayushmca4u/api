<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Countries;
use Validator;
use Illuminate\Http\Request;
use DB;
#use  Hash;
#use Illuminate\Http\Response;
class CountryController extends Controller
{
		
        public function create(Request $request)
        {
	    $error_code=200;
	    $message="Country Added Successfully";
	    $success=false;
	    $result="";
	    $data =$request->all();		
            $rules = array(
		'name' => 'required|min:4',
		'code' => 'required|min:3',
		'status'=>'required'
             );
	    $messages = [
      		  'name.required' => 'Please enter country name',
		  'code.required' => 'Please enter country code',
	          'status.required' => 'Please choose country status',
		  'name.min' => 'Country name should be equal or more than 4 character',
        	  'code.min' => 'Country Code should be equal or more than 3 character'
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
                                $Countries=new Countries();
				$last = $Countries->latest()->first();
				if($last)
				{
					$lastArr=$last->toArray();	
					$country_id=$lastArr['country_id'];
					$country_id=$country_id+1;	
				}
				else
				{	
					$country_id=1;		
				}	
				$code=$newtodo['code'];
			#	$name=strtolower($name);
                                $Countries = Countries::where('code', '=', $code)->first();
                                if($Countries)
                                {
                                	$CountriesArr=$Countries->toArray();
                                        $error_code="403";
                                        $message="Country details already exists for country code [$code].";
                                        $response= ["error"=>array("error_code"=>$error_code,"message"=>$message),"result"=>$CountriesArr];
                                        return response()->json($response,$error_code);
                                }
				$Countries=new Countries();
				$Countries->country_id=$country_id;
                                $Countries->name=$newtodo['name'];
                                $Countries->status=$newtodo['status'];
                                $Countries->code=$newtodo['code'];
	                        $Countries->created_at=date("Y-m-d H:i:s");
	                        $Countries->updated_at=date("Y-m-d H:i:s");
                                $response=$Countries->save();
                                if(!$response)
                                {
					$error_code=503;
					$message="Could not add country .Please try again after some time";
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
				$message="Could not add country .Please try again after some time";
				$response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>$result,"success"=>$success];
				#$response= ["error"=>true,"message" => "Could not create login user .Please try again after some time","status_code"=>403];
				return response()->json($response,$error_code);
                        }
	
	    }		
        }
	public function show()
	{
		$Countries = Countries::get();
		$CountriesArr=$Countries->toArray();
		$success=true;
		$error_code="200";
		$message="";		
		#$response= ["error"=>false,"message" =>$UserArr,"status_code"=>200];	
		$response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>$CountriesArr,"success"=>$success];	
		return response()->json($response);	
	}
	public function getcountry($country_id)
        {
		$success=false;
		$error_code="200";
		$message="";
                #$Countries = Countries::query('country_id',$country_id);
		$country_id=(int)$country_id;
		$Countries = Countries::where('country_id', '=', $country_id)->first();
		if(!$Countries)
		{
			$error_code="403";
			$message="Data Not Found for Countries Id $country_id";
			$response= ["error"=>array("code"=>$error_code,"message"=>$message),"result"=>$Countries,"success"=>$success];
			return response()->json($response,$error_code);
		}
		$success=true;	
                $CountriesArr=$Countries->toArray();
                $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>[$CountriesArr],"success"=>$success];
		 return response()->json($response,$error_code);
        }
	public function update_country(Request $request,$country_id)
	{	
       	    $error_code=200;
            $message="Country Details updated successfully.";
            $success=false;
            $result="";
	    #$tag_query = Countries::query();
	    $country_id=(int)$country_id;
	    $Countries = Countries::where('country_id', '=', $country_id)->first();	
	    $CountriesArr=$Countries->toArray();	
	    /*$Countries = Countries::raw(function ($collection) use ($country_id) {
		$country_id=(int)$country_id;	
           	return $collection->find(['country_id' => $country_id]);
            }); 	*/
            if(!$Countries)
            {
                    $error_code="403";
                    $message="Please check country id. Country details not found for country id $country_id.";
                    $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result"=>$Countries,"success"=>$success];
	             return response()->json($response,$error_code); 		 
            }		
	    $Countries->country_id = $country_id;
	    $status=$request->input('status');	
	    if(isset($status))
	    {		
	    	$Countries->status = $status;
	    }	
	    if($request->input('name'))
	 		$Countries->name = $request->input('name');
	    
	    if($request->input('code'))
	    {
			 $code 	= $request->input('code');
			 $Countries = Countries::where('code', '=', $code)->first();
                                if($Countries)
                                {
                                        $CountriesArr=$Countries->toArray();
                                        $error_code="403";
                                        $message="Country details already exists for country code [$code].";
                                        $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result"=>$CountriesArr];
                                        return response()->json($response,$error_code);
                                }	
                        $Countries->code = $request->input('code');
	    }	

            $response=$Countries->save();	
	    if(!$response)				    	
	    {
		    $error_code="403";
                    $message="Unable to update Country details for country id $country_id.";
                    $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result"=>$Countries,"success"=>$success];
	             return response()->json($response,$error_code);		
	    } 		
	    $CountriesArr=$Countries->toArray();
            $success=true; 		    
	    $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result"=>$CountriesArr,"success"=>$success];	
	    return response()->json($response,$error_code);
	}
}
?>
