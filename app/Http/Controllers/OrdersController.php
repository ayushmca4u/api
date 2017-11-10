<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Orders;
use App\Transactions;
use App\ShopperCart;
use App\Payments;
use Validator;
use Illuminate\Http\Request;
use DB;
#use  Hash;
#use Illuminate\Http\Response;
class OrdersController extends Controller
{	
        public function create(Request $request)
        {
	    $all_transactions_columns=array('order_id','shopper_id','package_id','activity_id','merchant_id','web_price','list_price','cost_price','currency','quantity','adult','children','total_participants','booking_activity_date','booking_activity_time','adult_activity_count','children_activity_count','transaction_state','transaction_reverse','remarks');
	    $manadatory_parameter=array('package_id','activity_id','merchant_id','web_price','list_price','cost_price','quantity','total_participants','booking_activity_date','booking_activity_time');		
	    $error_code=200;
            $message="Order Created Successfully";
            $success=false;
            $result=""; 	
	    $data =$request->all();		
            $rules = array(
                'shopper_id' => 'required|numeric',
                'order_total' => 'required',
                'currency' => 'required|min:3',
                'order_payment_status' => 'required',
		'transactions' => 'required',
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
				$datetime=date("Y-m-d H:i:s");
                                $newtodo = $data;
				$Orders=new Orders();
				$shopper_id=$newtodo['shopper_id'];
				$currency=$newtodo['currency'];
                                $Orders->shopper_id=$shopper_id;
                                $Orders->order_total=$newtodo['order_total'];
                                $Orders->currency=$currency;
                                $Orders->order_payment_status=$newtodo['order_payment_status'];	
				$Orders->order_approval_state=0;
				$Orders->created_at=$datetime;
				$Orders->updated_at=$datetime;
				DB::beginTransaction();
                                $response=$Orders->save();
                                if(!$response)
                                {
                                        DB::rollback();
                                        $error_code=503;
                                        $message="Could not create Order.Please try again after some time";
                                        #$response= ["error"=>true,"message" => "Could not create login user .Please try again after some time","status_code"=>403];
                                        $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>$result,"success"=>$success];
                                        return response()->json($response,$error_code);
                                }
				$order_id = $Orders->order_id;
				if($request->input('transactions'))
                                {
                                        $transactions=$request->input('transactions');
                                        foreach($transactions as $key=>$tran_details)
                                        {
						foreach($manadatory_parameter as $key)
						{
                 				       if(!$tran_details[$key])
						       {
								$error_code="502";
				                                $message="Mandatory parameter missing. Data Missing for $key";
        	                			        $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>$result,"success"=>$success];
                	                			return response()->json($response,$error_code);
                       					}			
                				}
						unset($Transactions);
						$Transactions=new Transactions();			
						$Transactions->order_id=$order_id;
						$Transactions->shopper_id=$shopper_id;	
						$Transactions->currency=$currency;	
						$Transactions->created_at=$datetime;
						$Transactions->updated_at=$datetime;
						$Transactions->transaction_state=0;
						$Transactions->transaction_reverse='N';
						foreach($tran_details as $tran_key=>$tran_val)
                        			{
			                                if(in_array($tran_key,$all_transactions_columns) && $tran_val!='')
                        				{
				                                $Transactions->$tran_key=$tran_val;
                                			}
                        			}
						$response=$Transactions->save();
			                        if(!$response)
                        			{
			                            DB::rollback();
			                            $error_code="403";
                        			    $message="Unable to update order transaction details.please try after some time.";
			                            $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result"=>$result,"success"=>$success];
                        			    return response()->json($response,$error_code);
			                        }	
                                        }
					DB::commit();
				        $TransactionsArr=$Transactions->toArray();
			                $success=true;
			                $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result"=>$TransactionsArr,"success"=>$success];
			                return response()->json($response,$error_code);	
                                }
                        }
                        catch(Exception $e)
                        {
				$error_code="502";
				$message="Could not create order.Please try again after some time";
				$response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>$result,"success"=>$success];
				#$response= ["error"=>true,"message" => "Could not create login user .Please try again after some time","status_code"=>403];
		                return response()->json($response,$error_code);	
                        }
	
	    }		
        }
	public function show()
	{
		$Orders = Orders::get();
		$OrdersArr=$Orders->toArray();
		$success=true;
		$error_code="200";
		$message="";		
		#$response= ["error"=>false,"message" =>$ShopperArr,"status_code"=>200];	
		$response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>$OrdersArr,"success"=>$success];	
		return response()->json($response,$error_code);
	}
	public function update_shopper(Request $request,$order_id)
	{		
		$all_transactions_columns=array('order_id','shopper_id','package_id','activity_id','merchant_id','web_price','list_price','cost_price','currency','quantity','adult','children','total_participants','booking_activity_date','booking_activity_time','adult_activity_count','children_activity_count','transaction_state','transaction_reverse','remarks');
		$error_code=200;
	        $message="Order Details updated successfully.";
        	$success=false;
	        $result="";
        	$datetime=date("Y-m-d H:i:s"); 	
		$data =$request->all();
		$Orders = Orders::find($shopper_id);
                 if(!$Shopper)
                 {
                     DB::rollback();
                     $error_code="403";
                     $message="Please check Shopper id.Shopper Booking details not found for shopper id $shopper_id.";
                     $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result"=>$Shopper,"success"=>$success];
                     return response()->json($response,$error_code);
                 }
		 if($request->input('transactions'))
                 {
                 	$transactions=$request->input('transactions');
                 	$Shopper->shopper_id=$shopper_id;
			 foreach($shopper_details as $shopper_key=>$shopper_val)
        	         {
                        	 if(in_array($shopper_key,$allowed_shopper_columns) && $shopper_val!='')
                	         {
                                	 $Shopper->$shopper_key=$shopper_val;
                         	}
                 	}
		 }	
                 $response=$Shopper->save();
                 if(!$response)
                 {
                     DB::rollback();
                     $error_code="403";
                     $message="Unable to update Booking shopper details for shopper_id $shopper_id.";
                     $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result"=>$Shopper,"success"=>$success];
                     return response()->json($response,$error_code);
                 }
		 DB::commit();
	         $ShopperArr=$Shopper->toArray();
        	 $success=true;
	         $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result"=>$ShopperArr,"success"=>$success];
        	 return response()->json($response,$error_code);
	}
	public function add_to_cart(Request $request)
        {
            $allowed_addtocart_columns=array('shopper_id','package_id','web_price','currency','quantity','adult','children','total_participants','discount','offer_id','tracking_url','booking_activity_date','booking_activity_time','adult_activity_count','children_activity_count');
	    $error_code=200;
            $message="Shopper package details Added to Add To Cart";
            $success=false;
            $result="";	
            $data =$request->all();
            $rules = array(
                'shopper_id' => 'required|numeric',
                'package_id' => 'required|numeric',
                'web_price' => 'required',
                'currency' => 'required|min:3',
                'booking_activity_date' => 'required',
                'total_participants' => 'required|numeric'
             );
            $validator = Validator::make($data, $rules);
            if ($validator->fails())
            {
                $error_code="403";
                $message=$validator->errors();
                $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>$result,"success"=>$success];
                return response()->json($response,$error_code);
            }
	    try
                        {
                                $newtodo = $data;
				$shopper_id=$newtodo['shopper_id'];
				$package_id=$newtodo['package_id'];
                                $ShopperCart = ShopperCart::where('shopper_id',$shopper_id)->where('package_id',$package_id)->orderBy('shopper_id','desc')->first();
                                if($ShopperCart)
                                {
                                        $ShopperCartArr=$ShopperCart->toArray();
                                        $error_code="200";
                                        $success=true;
                                        $message="Package Already added in shopper add to cart.";
                                        $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result"=>$ShopperCartArr,"success"=>$success];
                                        return response()->json($response,$error_code);
                                }	
				$datetime=date("Y-m-d H:i:s");
				$newtodo['cart_date']=$datetime;
                                DB::beginTransaction();
				$ShopperCart=new ShopperCart();
                                foreach($newtodo as $shopper_key=>$shopper_val)
                                {
                                        if(in_array($shopper_key,$allowed_addtocart_columns) && $shopper_val!='')
                                        {
                                                $ShopperCart->$shopper_key=$shopper_val;
                                        }
                                }
                                $response=$ShopperCart->save();
                                if(!$response)
                                {
                                        DB::rollback();
                                        $error_code=503;
                                        $message="Could not create shopper cart details.Please try again after some time";
                                        #$response= ["error"=>true,"message" => "Could not create login user .Please try again after some time","status_code"=>403];
                                        $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>$result,"success"=>$success];
                                        return response()->json($response,$error_code);
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
                                $message="Could not create shopper cart details .Please try again after some time";
                                $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>$result,"success"=>$success];
                                #$response= ["error"=>true,"message" => "Could not create login user .Please try again after some time","status_code"=>403];
                                return response()->json($response,$error_code);
                        }

            }
	public function get_add_to_cart_details($shopper_id)
        {
                $success=false;
                $error_code="200";
                $message="";
                $ShopperCart = ShopperCart::where('shopper_id',$shopper_id)->get();
                #$Shopper = Shopper::query('login_userid',$login_userid);
                if(!$ShopperCart)
                {
                        $error_code="403";
                        $message="Data Not Found for Shopper Id $shopper_id";
                        $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result"=>$ShopperCart,"success"=>$success];
                        return response()->json($response,$error_code);
                }
                $success=true;
                $ShopperCartArr=$ShopperCart->toArray();
                $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>[$ShopperCartArr],"success"=>$success];
                return response()->json($response,$error_code);
        }	
	public function Order_Checkout(Request $request)
        {
            $error_code=200;
            $message="";
            $success=false;
            $result="";
            $data =$request->all();
            $rules = array(
                'gid' => 'required|numeric',
                'currency' => 'required|min:3',
                'shopper_id' => 'required|numeric'
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
				$shopper_id=$newtodo['shopper_id'];
				$currency=$newtodo['currency'];
				$Packages= DB::table('package_details')->join('shopper_cart', 'package_details.package_id', '=', 'shopper_cart.package_id')->join('shopper_address', 'shopper_address.shopper_id', '=', 'shopper_cart.shopper_id')->where('shopper_cart.shopper_id', '=', $shopper_id)->select('package_details.activity_id','package_details.merchant_id','shopper_address.email','shopper_cart.*')->get();
				$ShopperCartArr=$Packages->toArray();
                		if(!$ShopperCartArr)
		                {
                		        $error_code="403";
		                        $message="Data Not Found for Shopper Id $shopper_id";
                		        $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result"=>$Packages,"success"=>$success];
		                        return response()->json($response,$error_code);
                		}
			
                                $gateway_id=$newtodo['gid'];
                                $Payments = Payments::where('gid',$gateway_id)->orderBy('gid','desc')->first();
                                if(!$Payments)
                                {
                                    $error_code="403";
                                    $message="Data Not Found for Gateway ID $gateway_id";
                                    $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result"=>$result,"success"=>$success];
                                    return response()->json($response,$error_code);
                                }
				$order_total=0;
				foreach ($ShopperCartArr as $key=>$cart_details)
				{
					$cart_details=(array)$cart_details;
					$email=$cart_details['email'];
					$order_total=$order_total+($cart_details['web_price']*($cart_details['adult']));
					$order_total=$order_total+($cart_details['web_price']*($cart_details['children']));
				}
				$datetime=date("Y-m-d H:i:s");
				$Orders=new Orders();
                                $Orders->shopper_id=$shopper_id;
                                $Orders->order_total=$order_total;
                                $Orders->currency=$currency;
                                $Orders->order_payment_status='P';
                                $Orders->order_approval_state=0;
                                $Orders->created_at=$datetime;
                                $Orders->updated_at=$datetime;
                                DB::beginTransaction();
                                $response=$Orders->save();
                                if(!$response)
                                {
                                        DB::rollback();
                                        $error_code=503;
                                        $message="Could not create Order.Please try again after some time";
                                        #$response= ["error"=>true,"message" => "Could not create login user .Please try again after some time","status_code"=>403];
                                        $response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>$result,"success"=>$success];
                                        return response()->json($response,$error_code);
                                }
                                $order_id = $Orders->order_id;
				foreach ($ShopperCartArr as $key=>$cart_details)
                                {
                                        $cart_details=(array)$cart_details;
					unset($Transactions);
                                        $Transactions=new Transactions();
					$Transactions->order_id=$order_id;
					$Transactions->shopper_id=$shopper_id;
					$Transactions->currency=$currency;
                                        $Transactions->package_id=$cart_details['package_id'];
                                        $Transactions->activity_id=$cart_details['activity_id'];
                                        $Transactions->merchant_id=$cart_details['merchant_id'];
                                        $Transactions->web_price=$cart_details['web_price'];
                                        $Transactions->list_price=$cart_details['list_price'];
                                        $Transactions->cost_price=$cart_details['cost_price'];
                                        $Transactions->quantity=$cart_details['quantity'];
                                        $Transactions->adult=$cart_details['adult'];
                                        $Transactions->children=$cart_details['children'];
                                        $Transactions->total_participants=$cart_details['total_participants'];
                                        $Transactions->booking_activity_date=$cart_details['booking_activity_date'];
                                        $Transactions->booking_activity_time=$cart_details['booking_activity_time'];	
                                        $Transactions->adult_activity_count=$cart_details['adult_activity_count'];	
                                        $Transactions->children_activity_count=$cart_details['children_activity_count'];
                                        $Transactions->created_at=$datetime;
                                        $Transactions->transaction_state=0;
                                        $Transactions->transaction_reverse='N';
                                        $Transactions->updated_at=$datetime;
					unset($response);
					$response=$Transactions->save();
                                	if(!$response)
	                                {
        	                                DB::rollback();
                	                        $error_code=503;
                        	                $message="Could not create Order Transactions .Please try again after some time";
                                	        #$response= ["error"=>true,"message" => "Could not create login user .Please try again after some time","status_code"=>403];
                                        	$response= ["error"=>array("code"=>$error_code,"message"=>$message),"result" =>$result,"success"=>$success];
	                                        return response()->json($response,$error_code);
        	                        }
                                }
				DB::commit();
				$formstring="";
                                switch ($gateway_id)
                                {
                                        case "1":
						$private_key="test_open_k_5a4ab20723412787b6f3";
						$formstring.='<form action="https://stgstatic.ticketstodo.com/charge.php" method="post">';	
						$formstring.='<script src="https://beautiful.start.payfort.com/checkout.js"';	
						$formstring.='data-key="'.$private_key.'"';	
						$formstring.='data-currency="'.$currency.'"';	
						$formstring.='data-amount="'.$order_total.'"';	
						$formstring.='data-email="'.$email.'"';	
						$formstring.='</script>';
						$formstring.="<input type='hidden' name='order_id' value='".$order_id."'>"; 
						$formstring.='</form>';
                                        break;
                                        case "2":
						$private_key="support-facilitator@ticketstodo.com";
						$rm=2;
						$formstring.='<form action="https://www.sandbox.paypal.com/cgi-bin/webscr" method="post" target="_top">';
						$formstring.="data-currency='".$currency."'";
						$formstring.='<input type="hidden" name="cmd" value="_xclick">';
						$formstring.="<input type='hidden' name='item_name' value='".$private_key."'>";	
						$formstring.="<input type='hidden' name='amount' value='".$order_total."'>";	
						$formstring.="<input type='hidden' name='rm' value='".$rm."'>";	
						$formstring.="<input type='hidden' name='currency_code' value='".$currency."'>";	
						$formstring.="<input type='hidden' name='custom' value='".$order_id."'>";	
						$formstring.="<input type='hidden' name='return' value='https://www.ticketstodo.com/success.php'>";	
						$formstring.="<input type='hidden' name='cancel_return' value='https://www.ticketstodo.com/cancel.php'>";	
						$formstring.='</form>';
						
                                        break;
                                }
				$success=true;
				$formstringArr=array("form"=>$formstring);
				$response= ["error"=>array("code"=>$error_code,"message"=>$message),"result"=>$formstringArr,"success"=>$success];
				return response()->json($response,$error_code);

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
}
?>
