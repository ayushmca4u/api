<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Payments;
use App\Paymenttransactions;
use Illuminate\Support\Facades\Hash;
use Validator;
use Illuminate\Http\Request;
use DB;
use  Start;
use  Start_Charge;
use  Start_Error;
use App\Orders;
use Ixudra\Curl\Facades\Curl;
#use Illuminate\Http\Response;
class PaymentController extends Controller
{		
        public function create(Request $request)
        {
	    $error_code=200;
	    $message="";
	    $success=false;
	    $result="";			
	    $data =$request->all();		
            $rules = array(
                'gid' => 'required|numeric',
                'currency' => 'required|min:3',
                'adult' => 'required|numeric',
                'total_participants' => 'required|numeric',
                'booking_activity_date' => 'required',
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
				$gateway_id=$newtodo['gid'];
	    			$Payments = Payments::where('gid',$gateway_id)->orderBy('gid','desc')->first();
			        if(!$Payments)
		                {
		                    $error_code="403";
                		    $message="Data Not Found for Gateway ID $gateway_id";
		                    $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result"=>$result,"success"=>$success];
                		    return response()->json($response,$error_code);
		                }	
				switch ($gateway_id)
				{
					case "1":
					$this->show_payfort_form($data);
					break;
					case "2":
					$this->show_paypal_form($data);
					break;
				}
				
                        }
                        catch(Exception $e)
                        {
				$error_code="502";
				$message="Could not fetch Payment gateway details .Please try again after some time";
				$response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>$result,"success"=>$success];
				#$response= ["error"=>true,"message" => "Could not create login user .Please try again after some time","status_code"=>403];
		                return response()->json($response,$error_code);	
                        }
	
	    }		
        }
	 public function show()
        {
                $Payments = Payments::get();
                $PaymentsArr=$Payments->toArray();
                $success=true;
                $error_code="200";
                $message="";
                #$response= ["error"=>false,"message" =>$ShopperArr,"status_code"=>200];
                $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>$PaymentsArr,"success"=>$success];
                return response()->json($response,$error_code);
        }
	public  function payfort_charge(Request $request)
	{
	    $error_code=200;
            $message="";
            $success=false;
            $result="";
            $data =$request->all();
	//print_r($data)	;die;
            $rules = array(
                'order_id' => 'required|numeric',
                'startToken' => 'required|min:32',
                'startEmail' => 'required|email'
             );
            $validator = Validator::make($data, $rules);
            if ($validator->fails())
            {
                $error_code="403";
                $message=$validator->errors();
                $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>$result,"success"=>$success];
                return response()->json($response,$error_code);
            }
		$token=$request->input('startToken');	
		$email=$request->input('startEmail');	
		$order_id=$request->input('order_id');	
		$api_keys = array(
  			  "secret_key" => "test_sec_k_f917d50e102e321e407e5",
			   "open_key"   => "test_open_k_5a4ab20723412787b6f3"
		);
        $Orders = Orders::where('order_id',$order_id)->orderBy('order_id','desc')->first();
        #$Shopper = Shopper::query('login_userid',$login_userid);
        if(!$Orders)
        {
                $error_code="403";
                $message="Data Not Found for Order Id $order_id";
                $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result"=>$Orders,"success"=>$success];
                return response()->json($response,$error_code);
        }
        $OrdersArr=$Orders->toArray();
	$order_total=$OrdersArr["order_total"];
	//$amount=(int)$order_total;
	//$currency=$OrdersArr['currency'];
	$amount =  1* 100;
	$currency="OMR";
	$payment_status=false;
	Start::setApiKey($api_keys["secret_key"]);
	    try {
		    $charge = Start_Charge::create(array(
	            "amount"      => $amount,
	            "currency"    => $currency,
	            "card"        => $token,
	            "email"       => $email,
	            "ip"          => $_SERVER["REMOTE_ADDR"],
	            "description" => "Charge Description"
    		));
		if(strtolower($charge["state"])=="captured")
		{
		    $payment_status=true;		
		}
	    } catch (Start_Error $payfort_error) 
		{
		    $status_code=$payfort_error->getHttpStatus();
		    $error_code = $payfort_error->getErrorCode();
		    $error_message = $payfort_error->getMessage();
		}
	if($payment_status==true)
        {
                $Orders->order_payment_status='C';
                $Orders->order_approval_state='1';
                $Orders->approval_date=date('Y-m-d H:i:s');
        }
        else
        {
                $Orders->order_payment_status='R';
        }
        $response=$Orders->save();
        if(!$response)
        {
                DB::rollback();
                $error_code=503;
                $message="Could not update order payment and status details for order $order_id";
                $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>$result,"success"=>$success];
                return response()->json($response,$error_code);
        }
	DB::beginTransaction();
	$Paymenttransactions=new Paymenttransactions();
	$Paymenttransactions->gid=1;              
	$Paymenttransactions->orderid=$order_id;          
	$Paymenttransactions->currency=$currency; 
	$Paymenttransactions->amount=$amount;
	if($payment_status == true)
	{
		$Paymenttransactions->merchanttxnrefid=$order_id;
		$Paymenttransactions->cardtype=$charge['card']['brand'];
		$Paymenttransactions->cardbin=$charge['card']['id'];
		$Paymenttransactions->account_id=$charge['account_id'];
		$Paymenttransactions->token_id=$charge['token_id'];
		$Paymenttransactions->gamount=$charge['captured_amount'];
		$Paymenttransactions->requesttxntime=date('Y-m-d H:i:s',strtotime($charge['created_at']));
		$Paymenttransactions->gresponsetxntime=date('Y-m-d H:i:s',strtotime($charge['created_at']));
		$Paymenttransactions->gresponsecode=$charge[''];
		$Paymenttransactions->gresponsemsg=$charge['description'];
		$Paymenttransactions->gresponseerrormsg=$charge[''];
		$Paymenttransactions->gtransactionid=$charge['id'];
		$Paymenttransactions->grefundid=$charge['id'];
		$Paymenttransactions->gauthcode=$charge['auth_code'];
		$Paymenttransactions->status=$charge['state'];
		$Paymenttransactions->transactiondate=$charge['created_at'];
		$Paymenttransactions->responsedate=date('Y-m-d H:i:s');
	}
	else
	{
		$Paymenttransactions->status="failure";
		$Paymenttransactions->gresponseerrormsg=$error_message;
		$Paymenttransactions->gauthcode=$error_code;		
	}
	$Paymenttransactions->created_at=date('Y-m-d H:i:s');
	$Paymenttransactions->updated_at=date('Y-m-d H:i:s');
	$response=$Paymenttransactions->save();
        if(!$response)
        {
                DB::rollback();
                $error_code=503;
                $message="Could not create payment transaction details for order $order_id";
                $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>$result,"success"=>$success];
                return response()->json($response,$error_code);
        }
	 DB::commit();
                echo  "Thank you choosing Ticektstodo for Booking products";
                exit;
	}
	public  function paypal_charge(Request $request)
	{
		$error_code=200;
         	$message="";
	        $success=false;
        	$result="";
	        $data =$request->all();
	            $rules = array(
        	        'tx' => 'required',
                	'cm' => 'required|numeric'
	             );
        	    $validator = Validator::make($data, $rules);
	            if ($validator->fails())
        	    {
                	$error_code="403";
	                $message=$validator->errors();
        	        $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>$result,"success"=>$success];
                	return response()->json($response,$error_code);
	            }
        	    $tx=$request->input('tx');
	            $orderid=$request->input('cm');	
		    $currency=$request->input('cc');	
		    $Orders = Orders::where('order_id',$orderid)->orderBy('order_id','desc')->first();
 	       #$Shopper = Shopper::query('login_userid',$login_userid);
	        if(!$Orders)
        	{
                	$error_code="403";
	                $message="Data Not Found for Order Id $orderid";
        	        $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result"=>$Orders,"success"=>$success];
                	return response()->json($response,$error_code);
	        }
		$OrdersArr=$Orders->toArray();
	        $order_total=$OrdersArr["order_total"];
		$pg_status=false;
		$paypal_response = Curl::to('https://www.sandbox.paypal.com/cgi-bin/webscr')->withData(['tx'=>$tx, 'cmd'=>'_notify-synch', 'submit'=>'PDT','at'=>'dJ0sUlehANsj75ops1dLjaRmDSjsKnqzjA7iAy0rPwYdO4DotsCUPa29Dlu'])->post();
		$status_validation=false;
		if(strpos($paypal_response, 'SUCCESS') === 0)	
		{	
			$pg_status=true;
			$paypal_response = substr($paypal_response, 7);
			$paypal_response = urldecode($paypal_response);
			preg_match_all('/^([^=\s]++)=(.*+)/m', $paypal_response, $m, PREG_PATTERN_ORDER);
			$responseArr = array_combine($m[1], $m[2]);	
			$pgorderid=trim($responseArr['custom']);
			$pg_order_total=trim($responseArr['payment_gross']);
			$pgcurrency=trim($responseArr['mc_currency']);
			$payment_status=trim($responseArr['payment_status']);
			if($payment_status=="Completed" && $pgorderid==$orderid && $pg_order_total==$order_total && $pgcurrency==$currency)	
			{
				$status_validation=true;
			}
		}
		DB::beginTransaction();
		if($pg_status==true && $status_validation==true)
		{	
			$Orders->order_payment_status='C';
			$Orders->order_approval_state='1';				
			$Orders->approval_date=date('Y-m-d H:i:s');
		}		
		else
		{
			$Orders->order_payment_status='R';
		}
		$response=$Orders->save();
		if(!$response)
	        {
        	        DB::rollback();
                        $error_code=503;
                	$message="Could not update order payment and status details for order $order_id";
	                $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>$result,"success"=>$success];
        	        return response()->json($response,$error_code);
                }
 	        $Paymenttransactions=new Paymenttransactions();
        	$Paymenttransactions->gid=2;
	        $Paymenttransactions->orderid=$pgorderid;
        	$Paymenttransactions->currency=$currency;
	        $Paymenttransactions->amount=$order_total;
        	if($pg_status == true)
	        {
        	        $Paymenttransactions->merchanttxnrefid=$responseArr['txn_id'];
                	$Paymenttransactions->cardtype=$responseArr['txn_type'];
	                $Paymenttransactions->cardbin=$responseArr['payer_id'];
        	        $Paymenttransactions->account_id=$responseArr['payer_email'];
                	$Paymenttransactions->token_id=$responseArr['payer_email'];
        	        $Paymenttransactions->gamount=$pg_order_total;
	                $Paymenttransactions->requesttxntime=date('Y-m-d H:i:s',strtotime($responseArr['payment_date']));
                	$Paymenttransactions->gresponsetxntime=date('Y-m-d H:i:s',strtotime($responseArr['payment_date']));
	                $Paymenttransactions->gresponsecode=200;
        	        $Paymenttransactions->gresponsemsg="SUCCESS";
                	$Paymenttransactions->gresponseerrormsg="";
        	        $Paymenttransactions->gtransactionid=$responseArr['txn_id'];
	                $Paymenttransactions->grefundid="";
                	$Paymenttransactions->gauthcode="";
	                $Paymenttransactions->status="1";
        	        $Paymenttransactions->transactiondate=date('Y-m-d H:i:s',strtotime($responseArr['payment_date']));
                	$Paymenttransactions->responsedate=date('Y-m-d H:i:s');
        	}
		else
	        {
        	        $Paymenttransactions->status="failure";
                	$Paymenttransactions->gresponseerrormsg=$response;
	                $Paymenttransactions->gauthcode=$response;
        	}
	        $Paymenttransactions->created_at=date('Y-m-d H:i:s');
        	$Paymenttransactions->updated_at=date('Y-m-d H:i:s');
	        $response=$Paymenttransactions->save();
        	if(!$response)
	        {
        	        DB::rollback();
                	$error_code=503;
	                $message="Could not create payment transaction details for order $order_id";
        	        $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>$result,"success"=>$success];
                	return response()->json($response,$error_code);
	        }
		DB::commit();
		echo  "Thank you choosing Ticektstodo for Booking products";
		exit;
	}	
}
?>
