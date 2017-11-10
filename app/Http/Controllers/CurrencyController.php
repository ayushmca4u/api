<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Currencies;
use Validator;
use Illuminate\Http\Request;
use DB;
#use  Hash;
#use Illuminate\Http\Response;
class CurrencyController extends Controller
{
		
        public function create(Request $request)
        {
	    $error_code=200;
	    $message="Currency Added Successfully";
	    $success=false;
	    $result="";			
	    $data =$request->all();		
            $rules = array(
		'currency_name' => 'required|min:3',
		'currency_code' => 'required|min:2',
		'status'=>'required'
             );
	     $messages = [
                  'currency_name.required' => 'Please enter currency name',
                  'currency_code.required' => 'Please enter currency code',
                  'status.required' => 'Please choose currency status',
                  'currency_name.min' => 'Currency name should be equal or more than 3 character',
                  'currency_name.min' => 'Currency Code should be equal or more than 2 character'
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
                                $Currencies=new Currencies();
				$last = $Currencies->latest()->first();
				if($last)
				{
					$lastArr=$last->toArray();	
					$currency_id=$lastArr['currency_id'];
					$currency_id=$currency_id+1;	
				}
				else
				{	
					$currency_id=1;		
				}	
				$currency_name=strtolower($newtodo['currency_name']);
				$Currencies = Currencies::where('currency_name', '=', $currency_name)->first();
			        $CurrenciesArr=$Currencies->toArray();
				if($CurrenciesArr)
				{
					$error_code="403";
		                        $message="Currnecy already exists for currency name [$currency_name].";
                		        $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result"=>$Currencies,"success"=>$success];
		                        return response()->json($response,$error_code);			
				}	

				$Currencies->currency_id=$currency_id;
                                $Currencies->currency_name=strtolower($newtodo['currency_name']);
                                $Currencies->status=$newtodo['status'];
                                $Currencies->currency_code=$newtodo['currency_code'];
	                        $Currencies->created_at=date("Y-m-d H:i:s");
	                        $Currencies->updated_at=date("Y-m-d H:i:s");
                                $response=$Currencies->save();
                                if(!$response)
                                {
					$error_code=503;
					$message="Could not add currency .Please try again after some time";
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
				$message="Could not add currency .Please try again after some time";
				$response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>$result,"success"=>$success];
				#$response= ["error"=>true,"message" => "Could not create login user .Please try again after some time","status_code"=>403];
				return response()->json($response,$error_code);
                        }
	
	    }		
        }
	public function show()
	{
		$Currencies = Currencies::get();
		$CurrenciesArr=$Currencies->toArray();
		if(count($Currencies)==0)
		{
		    $error_code="403";
		   $success=false;
                    $message="Currency details not found.";
                    $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result"=>$Currencies,"success"=>$success];
                    return response()->json($response);
	
		}
		$CurrenciesArr=$Currencies->toArray();
		$success=true;
		$error_code="200";
		$message="";		
		#$response= ["error"=>false,"message" =>$UserArr,"status_code"=>200];	
		$response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>$CurrenciesArr,"success"=>$success];	
		return response()->json($response,$error_code);
	}
	public function getcurrency($currency_id)
        {
		$success=false;
		$error_code="200";
		$message="";
                #$Currencies = Currencies::query('currency_id',$currency_id);
		$currency_id=(int)$currency_id;
		$Currencies = Currencies::where('currency_id', '=', $currency_id)->first();
		if(!$Currencies)
		{
			$error_code="403";
			$message="Data Not Found for Currency Id $currency_id";
			$response= ["error"=>array("code"=>$error_code,"message"=>$message),"result"=>$Currencies,"success"=>$success];
			return response()->json($response,$error_code);
		}
		$success=true;	
                $CurrenciesArr=$Currencies->toArray();
                $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>[$CurrenciesArr],"success"=>$success];
		return response()->json($response,$error_code);
        }
	public function update_currency(Request $request,$currency_id)
	{	
       	    $error_code=200;
            $message="Currency Details updated successfully.";
            $success=false;
            $result="";
	    #$tag_query = Currencies::query();
	    $currency_id=(int)$currency_id;
	    $Currencies = Currencies::where('currency_id', '=', $currency_id)->first();	
	    $CurrenciesArr=$Currencies->toArray();	
	    /*$Currencies = Currencies::raw(function ($collection) use ($currency_id) {
		$currency_id=(int)$currency_id;	
           	return $collection->find(['currency_id' => $currency_id]);
            }); 	*/
            if(!$Currencies)
            {
                    $error_code="403";
                    $message="Please check currency id. Currency details not found for currency id $currency_id.";
                    $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result"=>$Currencies,"success"=>$success];
		    return response()->json($response,$error_code);
            }		
	    $Currencies->currency_id = $currency_id;
	    $status=$request->input('status');	
	    if(isset($status))
	    {		
	    	$Currencies->status = $status;
	    }	
	    if($request->input('currency_name'))
	 		$Currencies->currency_name = strtolower($request->input('currency_name'));
	    
	    if($request->input('currency_code'))
                        $Currencies->currency_code = $request->input('currency_code');

            $response=$Currencies->save();	
	    if(!$response)				    	
	    {
		    $error_code="403";
                    $message="Unable to update Currency details for currency id $currency_id.";
                    $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result"=>$Currencies,"success"=>$success];
		    return response()->json($response,$error_code);				
	    } 		
	    $CurrenciesArr=$Currencies->toArray();
	    $success=true;	
	    $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result"=>$CurrenciesArr,"success"=>$success];	
	    return response()->json($response,$error_code);	
	}
}
?>
