<?php
$success_string='[
		error:{
			code:200,
			message:""
		},
		success:true,
		result:{
        		"Cart" : "العربة",
		        "Sign_In" : "الدخول لحسابك",
		        "Download_App" : "حمل التطبيق",
		        "Sign_Up" : "التسجيل",
			"Yours_to_Explore" : "لك للاستكشاف",
		        "Discover_and_book_amazing_things_to_do_at_exclusive_prices" : "اكتشف وحجز أشياء مذهلة للقيام بها بأسعار حصرية",
		        "Search_By_destination_activity_or_attraction" : "البحث حسب الوجهة أو النشاط أو الجذب",
		        "Handpicked_Experiences" : "تجارب مختارة بعناية",
		        "Read_real_user_reviews" : "قراءة نقديات المستخدم الحقيقي",
		        "Best_price_guaranteed" : "أفضل الأسعار مضمونة",
		        "Hassle_free_eticket_entry" : "الدخول بالتذاكر الإلكترونية من غير متاعب",
		        "Seamless & Safe Booking" : "الحجز السلس والآمن",
		        "Top_Destination" : "أعلى الوجهة",
		        "Discover_tours_attractions_and_activities_for_your_next_adventure" : "اكتشف الجولات والمعالم السياحية والأنشطة للمغامرة الخاصة بك المقبلة",
		        "Explore_More_Destinations" : "استكشف المزيد من الوجهات",
		        "Popular_Activities" : "الأنشطة الشعبية",
		        "Favourite_experiences_booked_by_travelers" : "التجارب المفضلة حجزها المسافرون",
		        "booked" : "الحجز",
		        "Travel_Inspiration" : "إلهام السفر",
  "Curated_suggestions_based_on_seasons_festivals_and_interests" : "اقتراحات منسقة على أساس المواسم والمهرجانات والمصالح",
		        "Taipai" : "تايباي",
		        "TicketsToDo_Recommended" : "تيكيتستودو الموصى بها",
			"Activities_hand_picked_by_our_travel_customers" : "الأنشطة اختارها عملائنا المسافرون",
		        "Enter_your_email_address" : "أدخل عنوان بريدك الالكتروني",
		        "Submit" : "عرض",
		        "All_rights_Reserved" : "كل الحقوق محفوظة",
		        "About_TicketsToDo" : "معلومات عن تيكيتستودو",
		        "About_Us" : "معلومات عنا",
			 "Blog" : "بلوق",
		        "Method_of_payment" : "طريقة الدفع",
		        "Terms_of_use" : "شروط الاستخدام",
		        "Terms_conditions" : "الشروط والأحكام",
		        "Refund_cancellation_policy" : "سياسة رد الأموال والإلغاء",
		        "Privacy_Policy" : "سياسة الخصوصية",
		        "Contact" : "اتصال",
		        "Contact_Us" : "اتصل بنا",
		        "FAQ’s" : "أسئلة شائعة",
		        "Ask_TicketsToDo" : "اسأل تيكستودو",
		        "Payment_Channel" : "قناة الدفع"
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
