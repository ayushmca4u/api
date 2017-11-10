<?php
$success_string='[
		error:{
			code:200,
			message:""
		},
		success:true,
		result:{
        		"Cart" : "Cart",
		        "Sign_In" : "Sign_In",
		        "Download_App" : "Download_App",
		        "Sign_Up" : "Sign_Up",
			"Yours_to_Explore" : "Yours_to_Explore",
		        "Discover_and_book_amazing_things_to_do_at_exclusive_prices" : "Discover_and_book_amazing_things_to_do_at_exclusive_prices", 
		        "Search_By_destination_activity_or_attraction" : "Search_By_destination_activity_or_attraction",
		        "Handpicked_Experiences" : "Handpicked_Experiences",
		        "Read_real_user_reviews" : "Read_real_user_reviews",
		        "Best_price_guaranteed" : "Best_price_guaranteed",
		        "Hassle_free_eticket_entry" : "Hassle_free_eticket_entry",
		        "Seamless & Safe Booking" : "Seamless & Safe Booking",
		        "Top_Destination" : "Top_Destination",
		        "Discover_tours_attractions_and_activities_for_your_next_adventure" : "Discover_tours_attractions_and_activities_for_your_next_adventure",
			 "Explore_More_Destinations" : "Explore_More_Destinations",
		        "Popular_Activities" : "Popular_Activities",
		        "Favourite_experiences_booked_by_travelers" : "Favourite_experiences_booked_by_travelers",
		        "booked" : "booked",
		        "Travel_Inspiration" : "Travel_Inspiration",
  "Curated_suggestions_based_on_seasons_festivals_and_interests" : "Curated_suggestions_based_on_seasons_festivals_and_interests",
		        "Taipai" : "Taipai",
		        "TicketsToDo_Recommended" : "TicketsToDo_Recommended",
			"Activities_hand_picked_by_our_travel_customers" : "Activities_hand_picked_by_our_travel_customers",
		        "Enter_your_email_address" : "Enter_your_email_address",
		        "Submit" : "Submit",
		        "All_rights_Reserved" : "All_rights_Reserved",
		        "About_TicketsToDo" : "About_TicketsToDo",
		        "About_Us" : "About_Us",
			 "Blog" : "Blog",
		        "Method_of_payment" : "Method_of_payment",
		        "Terms_of_use" : "Terms_of_use",
		        "Terms_conditions" : "Terms_conditions",
		        "Refund_cancellation_policy" : "Refund_cancellation_policy",
		        "Privacy_Policy" : "Privacy_Policy",
		        "Contact" : "Contact",
		        "Contact_Us" : "Contact_Us",
		        "FAQ’s" : ""FAQ’s",
		        "Ask_TicketsToDo" : "Ask_TicketsToDo",
		        "Payment_Channel" : "Payment_Channel"
			}]';
/*$new_str_arr["Payment_Channel"]="ﻖﻧﺍﺓ ﺎﻟﺪﻔﻋ";
$new_str_arr["Ask_TicketsToDo"]="ﺎﺳﺄﻟ ﺖﻴﻜﺴﺗﻭﺩﻭ";

$result=$new_str_arr;
$return_success=array('error' =>array("code"=>200,"message"=>""),'result' =>$result,'success' =>true);
$return_success=json_encode($return_success);
echo "<pre>";print_r($return_success);die;
$return_failure=array('error' =>array("code"=>500,"message"=>"Internal Server Error"),'result' =>'','success' =>false);
$return_failure=json_encode($return_failure);*/
$return_failure='
                error:{
                        code:500,
                        message:"Internal Server Error"
                },
		result:""
                success:false';
return array('success' => $success_string,"failure"=>$return_failure);
?>
