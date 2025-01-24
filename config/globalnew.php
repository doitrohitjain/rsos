<?php

$configGlobal = array();

if(str_contains(@$_SERVER['HTTP_USER_AGENT'],"Android")){
	//echo "<center>Your request will be responded soon....you are in queque..please wait......</center>"; die;
} 
// $domain = "10.68.181.229";
// $domain = "10.68.181.213";
$domain = "https://rsosadmission.rajasthan.gov.in/rsos/";

if(@$_SERVER['HTTP_HOST']){ 
	if(isset($_SERVER['HTTP_HOST']) && !empty($_SERVER['HTTP_HOST'])){
		$domain = $_SERVER['HTTP_HOST'];
	} 
}

 
$ip = NULL;


if (isset($_SERVER['HTTP_CLIENT_IP']) && !empty($_SERVER['HTTP_CLIENT_IP'])) {
    $ip = $_SERVER['HTTP_CLIENT_IP'];
} elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
} else { 
	if(@$_SERVER['REMOTE_ADDR']){
    	$ip = $_SERVER['REMOTE_ADDR'];
	}
}
 

$result_process_allowed_ips = '{"0":"10.68.181.236","1":"10.68.181.1751"}';

 
$currentDomain = $domain;
$subFolder = 'rsos';
$protocol = "http://"; 


if(@$_SERVER['REQUEST_SCHEME']){
	$protocol = $_SERVER['REQUEST_SCHEME'] . "://";
} 
  
if($currentDomain == '10.68.181.236'){
	$ssoURl = "ssotest";
	$configGlobal[] = array(
		"APP_NAME" => "Laravel Local",
		"APP_ENV" => "local",
		"APP_URL" => $protocol . "10.68.181.236/".$subFolder . "/"
	); 
	
	$configGlobal[] = array(
		"DB_HOST" => '10.68.181.229',
		"DB_PORT" => 3306,
		"DB_DATABASE" => 'lrsos_new',
		"DB_USERNAME" => 'root',
		"DB_PASSWORD" => ''
	); 
}else if($currentDomain == '10.68.181.175:8080'){
	$ssoURl = "ssotest";
	$configGlobal[] = array(
		"APP_NAME" => "Laravel Local",
		"APP_ENV" => "local",
		"APP_URL" => $protocol . "10.68.181.175:8080/".$subFolder . "/"
	); 
	$result_process_allowed_ips = json_decode($result_process_allowed_ips, true);
	$configGlobal[] = array(
		"DB_HOST" => '10.68.128.254', //10.68.181.175
		"DB_PORT" => 3306,
		"DB_DATABASE" => 'lrsos_new_local',
		"DB_USERNAME" => 'hteapp',  //super_admin
		"DB_PASSWORD" => 'hteapp@123#' //12345
	);
}else if($currentDomain == '10.68.252.122'){//staging IIS server
	$ssoURl = "ssotest";
	$configGlobal[] = array(
		"APP_NAME" => "Laravel Local",
		"APP_ENV" => "local",
		"APP_URL" => $protocol . "10.68.252.122/".$subFolder . "/"
	);
	$configGlobal[] = array(
		"DB_HOST" => '10.68.128.254', //10.68.181.175
		"DB_PORT" => 3306,
		"DB_DATABASE" => 'lrsos_new_local',
		"DB_USERNAME" => 'hteapp',  //super_admin
		"DB_PASSWORD" => 'hteapp@123#' //12345
	);
}else if($currentDomain == '10.68.181.249'){
	$ssoURl = "ssotest";
	$configGlobal[] = array(
		"APP_NAME" => "Laravel Local",
		"APP_ENV" => "local",
		"APP_URL" => $protocol . "10.68.181.249/".$subFolder . "/"
	); 
}else if($currentDomain == '10.70.240.222' || $currentDomain == '172.21.90.58' || $currentDomain == 'rsosadmission.rajasthan.gov.in' || $currentDomain == 'www.rsosadmission.rajasthan.gov.in'){	
	//10.70.241.116

	$subFolder = 'rsos';
	$ssoURl = "sso";
	$configGlobal[] = array(
		"APP_NAME" => "RSOS",
		"APP_ENV" => "local",
		"APP_URL" => $protocol . "rsosadmission.rajasthan.gov.in/".$subFolder . "/"
	);
	$configGlobal[] = array(
		"DB_HOST" => '172.21.70.113:3306',
		"DB_PORT" => 'lrsos',
		"DB_DATABASE" => 'lrsos_new',
		"DB_USERNAME" => 'lrsos',
		"DB_PASSWORD" => 'lrsos@2022#'
	); 

}else if($currentDomain == '10.68.181.213'){
	$ssoURl = "ssotest";
	$configGlobal[] = array(
		"APP_NAME" => "Laravel Local",
		"APP_ENV" => "local",
		"APP_URL" => $protocol . "10.68.181.229/".$subFolder . "/"
	);
	$configGlobal[] = array(
		"DB_HOST" => '10.68.128.254', //10.68.181.175
		"DB_PORT" => 3306,
		"DB_DATABASE" => 'lrsos_new_local',
		"DB_USERNAME" => 'hteapp',  //super_admin
		"DB_PASSWORD" => 'hteapp@123#' //12345
	);
}else{
	$ssoURl = "ssotest";
	$configGlobal[] = array(
		"APP_NAME" => "Laravel Staging",
		"APP_ENV" => "staging",
		// "APP_URL" => "http://10.68.181.236/".$subFolder.  "/"
		"APP_URL" => $protocol . "localhost:" . @$_SERVER['SERVER_PORT'] ."/".$subFolder . "/"
	); 
	$configGlobal[] = array(
		"DB_HOST" => '10.68.181.51',
		"DB_PORT" => 3306,
		"DB_DATABASE" => 'lrsos_new',
		"DB_USERNAME" => 'user',
		"DB_PASSWORD" => '12345'
	); 
}  
 
  
if($ip == '10.68.181.229' || $ip == '10.68.181.236' || $ip == '10.68.181.122' || $ip == '10.68.181.213'){
	$configGlobal[] = ["APP_DEBUG" => "true"];
} else {
	$configGlobal[] = ["APP_DEBUG" => "false"];
}



$configGlobal[] = array(
	"APP_KEY" => "base64:yczig/9chHvYr6eISuYYoeSKFafOxrca3Z9mpNpHalY",
	"SSO_LOGIN_URL" => 'https://' . $ssoURl .'.rajasthan.gov.in/signin',
	"SSO_URL" => 'http://' . $ssoURl .'.rajasthan.gov.in',
	"SSO_URL_DASHBOARD" => 'http://' . $ssoURl .'.rajasthan.gov.in/dashboard',
	"SSO_API_URL" => 'https://' . $ssoURl .'.rajasthan.gov.in:4443/SSOREST/',
	"BACK_TO_SSO_URL" => 'https://' . $ssoURl .'.rajasthan.gov.in/sso',
	"BACK_TO_SSO_LOGOUT_URL" => 'https://' . $ssoURl .'.rajasthan.gov.in/signout',
); 


$configGlobal[]['whiteListMasterIps'] = array("10.68.181.249","10.68.181.122","10.68.181.51","10.68.181.175","10.68.181.236","10.68.181.229","10.68.181.2131");
$configGlobal[]['allowedMacAddressList'] = array("70-5a-0f-3b-66-74","8c-ec-4b-5e-76-03");
$configGlobal[]['allowedBackButtonForRoles']=array(40,58,60,64,66,71,72,73,74,75,76,78,82);

// $configGlobal[]['allowpreviousyear'] = array("13" => "2017-18", "120" => "2018-19","121" => "2019-20","122" => "2020-21","123" => "2021-22","124" => "2022-23");




// $configGlobal['ajaxBaseUrl'] = $protocol . $currentDomain . "/".$subFolder . "/ajax/" ;
// $configGlobal['ajaxBaseUrl'] = url('/ajax/');
$configGlobal['logoutUrl'] = $protocol . $currentDomain . "/".$subFolder . "/logout";
$configGlobal['ajaxBaseUrl'] = "../ajax/";

// $configGlobal['siteTitle'] = "राजस्थान स्टेट ओपन स्कूल, जयपुर ". date("Y") . "-" . date('Y', strtotime('+1 year'));
$configGlobal['siteTitle'] = "राजस्थान स्टेट ओपन स्कूल, जयपुर 2023-2024 ";
$configGlobal['title'] = "राजस्थान स्टेट ओपन स्कूल, जयपुर  2023-2024 ";
$configGlobal['starMark'] = "<span class='starmark' style='color:red;'> *</span>";

$configGlobal['SSO_API_USERNAME'] = 'madarsa.test';
$configGlobal['SSO_API_PASSWORD'] = 'Test@1234';
$configGlobal['CURRENT_IP'] = $ip;



//Use only for fresh form stream-1 and stream-2 purpuse start
$configGlobal['form_admission_academicyear_id'] = "125";
$configGlobal['form_current_exam_month_id'] = "2";

$configGlobal['form_current_allowed_exam_month_id'] = array(1 => 1,2 => 2);
$configGlobal['form_current_admission_session_id'] = "125";
$configGlobal['form_admission_enrolment_year_slug'] = "23";




//Use only for fresh form stream-1 and stream-2 purpuse end

//Use only for admission purpuse start
$configGlobal['admission_academicyear_id'] = "125";
$configGlobal['admission_enrolment_year_slug'] = "23";
// $configGlobal['admission_academicyear_id'] = "125";
// $configGlobal['admission_enrolment_year_slug'] = "23";

$configGlobal['supp_admission_academicyear_id'] = "123";
//Use only for admission purpuse end

//Use only for examination purpuse start
$configGlobal['supp_current_admission_session_id'] = "123";

$configGlobal['form_supp_current_admission_session_id'] = "125";
$configGlobal['supp_current_admission_exam_month'] = "2";

$configGlobal['current_admission_session_id'] = "125";
 
$configGlobal['current_admission_year_string'] = date("Y");
$configGlobal['current_exam_month_id'] = 2; 
$configGlobal['supp_current_exam_month_id'] = 2; //Updated on 04-08-2023


//Use only for theory purpuse start
$configGlobal['current_theory_session_id'] = "124";
$configGlobal['current_thoery_exam_month'] = "1";



//Use for result processing and toppers
$configGlobal['current_result_session_year_id'] = 124;
$configGlobal['current_result_session_month_id'] = 1;

//Use only for sessional purpuse end
$configGlobal['current_sessional_exam_year'] = "124";
$configGlobal['current_sessional_exam_month'] = 1; 


//Use only for books_requirement purpuse end
$configGlobal['current_books_requirement_exam_year'] = 124;
$configGlobal['current_books_requirement_exam_month'] = 1;

// Use only for update provisional Results 
$configGlobal['allow_update_provisional_result'] = true; 

//Use only for examination purpuse end


$configGlobal['request_client_ip'] = $ip;
$configGlobal['super_admin_id'] = "58";
$configGlobal['practicalexaminer'] = 63;
$configGlobal['theoryexaminer'] = 62;
$configGlobal['deo'] = 70;
$configGlobal['admin'] = 40;
$configGlobal['super_admin'] = 58;
$configGlobal['developer_admin'] = 71;
$configGlobal['secrecy_admin'] = 75;
$configGlobal['examination_department'] = 72;
$configGlobal['publication_department'] = 66;
$configGlobal['aicenter_id'] = 59;
$configGlobal['marksheet_verification'] = 78;
$configGlobal['evaluation_department'] = 74;
$configGlobal['secrecy_epartment'] = 73;
$configGlobal['evaluation_admin'] = 76;
$configGlobal['Examcenter'] = 60;
$configGlobal['Printer'] = 64;
$configGlobal['student'] = 41;
$configGlobal['examination_admin'] = 82;



$configGlobal[]['form_session_changed'] = array(
	$configGlobal['developer_admin'],
	$configGlobal['aicenter_id'],
	$configGlobal['deo'],
	$configGlobal['publication_department']	,
	$configGlobal['marksheet_verification']	,
	$configGlobal['Examcenter'],
	$configGlobal['secrecy_admin'],
	$configGlobal['super_admin'],
	$configGlobal['evaluation_admin'],
	$configGlobal['examination_department'],
	$configGlobal['Printer'],
	$configGlobal['student'],
	$configGlobal['practicalexaminer'],
	$configGlobal['examination_admin'],
	$configGlobal['evaluation_department'],
	$configGlobal['secrecy_epartment'],
	$configGlobal['theoryexaminer'],
);

$configGlobal[]['allowOnlyPreviousYears'] = array(
	
	
	
    
);
$configGlobal[]['allowOnlyCurrentYears'] = array(
	$configGlobal['deo'],
	$configGlobal['publication_department'],
	$configGlobal['practicalexaminer'],
	$configGlobal['student'],
	$configGlobal['evaluation_department'],
	$configGlobal['secrecy_epartment'],
	$configGlobal['theoryexaminer'],
	
);

$configGlobal[]['allowPreviousAndCureentYears'] = array(
	$configGlobal['aicenter_id'],
	$configGlobal['Examcenter'],
	$configGlobal['secrecy_admin'],
	$configGlobal['super_admin'],
	$configGlobal['evaluation_admin'],
	$configGlobal['examination_department'],
	$configGlobal['Printer'],
	$configGlobal['examination_admin']

);


$configGlobal['defaultPageLimit'] = 20;
$configGlobal['SuppDefaultStreamId'] = 2;
$configGlobal['aicenterSubjectWiseStream'] = 1;
$configGlobal['defaultCousre'] = 12;
$configGlobal['Examcenter'] = 60;

$configGlobal['supp_undertaking_msg'] = "मेरे द्वारा प्रदान की गई उपरोक्त सभी जानकारी सत्य है और अन्यथा मैं अपना आवेदन वापस लेने के लिए स्वीकार करता हूं। (All the above information provided by me is true and otherwise I accept to withdraw my application.)";

$configGlobal['supp_subject_count'] = 2;
$configGlobal['supp_stream'] = 1;

$configGlobal['exam1'] = 'March-May 2022';
$configGlobal['exam2'] = 'Oct-Nov 2021';
$configGlobal['result_date'] = '24-08-2023';
$configGlobal['result_month'] = 'March-May 2022';
$configGlobal['pastdata_document'] = 'pastdata';
$configGlobal["result_process_allowed_ips"] = '{"0":"10.68.181.229","1":"10.68.181.249","2":"10.68.181.122","3":"10.68.181.175","4":"10.68.181.51"}';

//test $domain = "rsosadmission.rajasthan.gov.in"; //To set live environment 

/* payment service start */
if($domain == '10.70.240.222' || $domain == '172.21.90.58' || $domain == 'rsosadmission.rajasthan.gov.in' || $domain == 'www.rsosadmission.rajasthan.gov.in'){ 
	$configGlobal['Emitra_adm_only_payment_service'] ="9551"; //For Live 9511
	$configGlobal['Emitra_environment'] ="live"; 
} else if($domain == '10.68.252.122' || $domain == '10.68.181.236' || $domain == "10.68.181.175:8080" || $domain == "10.68.181.229" ||$domain == '10.68.181.213' ) {
	$configGlobal['Emitra_adm_only_payment_service'] ="6336";//"6336";
	$configGlobal['Emitra_environment'] ="staging";
}   

if (@$configGlobal['Emitra_adm_only_payment_service'] == '9551') {
	$configGlobal['Emitra_live_verifyUrl'] = 'https://emitraapp.rajasthan.gov.in/aggregator/api/payment/status';
	$configGlobal['Emitra_live_URL'] = 'https://emitraapp.rajasthan.gov.in/aggregator/payment/start';
} else {
	$configGlobal['Emitra_staging_verifyUrl'] = 'http://emitrauat.rajasthan.gov.in/aggregator/api/payment/status';
	$configGlobal['Emitra_staging_URL'] = 'http://emitrauat.rajasthan.gov.in/aggregator/payment/start';
} 
$configGlobal['ga'] = "<script async src='https://www.googletagmanager.com/gtag/js?id=G-1TSDHVK57H'></script><script>window.dataLayer = window.dataLayer || [];function gtag(){dataLayer.push(arguments);}gtag('js', new Date());gtag('config', 'G-1TSDHVK57H');</script>";


/* For Live Admission Payment Start*/
	$configGlobal["EmitraService_9551_ENCKEY"] ='99D33C9478DC98B22F488D5674711';
	$configGlobal["EmitraService_9551_CHECKSUMKEY"] = 'rsik4e5K6K#4';
	$configGlobal["EmitraService_9551_COMMTYPE"] = "3"; 
	$configGlobal["EmitraService_9551_MERCHANTCODE"] = "RSOS1607";
	$configGlobal["EmitraService_9551_OFFICECODE"] = "RSOSHQ";
	$configGlobal["EmitraService_9551_REVENUEHEAD1"] = "483-{application_fee}";
	$configGlobal["EmitraService_9551_REVENUEHEAD2"] = "484-{form_commission}"; //60
	$configGlobal["EmitraService_9551_SUBSERVICEID"] = "";  
 
/* payment service end */ 

/* For Staging Admission Payment Start*/
	$configGlobal["EmitraService_6336_ENCKEY"] ='VNOCimQOgtp6AnpMcQLWcVOvVjva4p5y'; //VNOCimQOgtp6AnpMcQLWcVOvVjva4p5y
	$configGlobal["EmitraService_6336_CHECKSUMKEY"] = 'zS&vATW.BFXEjR,'; //12072022- zS&vATW.BFXEjR, //EmitraNew@2016
	$configGlobal["EmitraService_6336_COMMTYPE"] = "3";
	$configGlobal["EmitraService_6336_MERCHANTCODE"] = "RISLTEST";
	$configGlobal["EmitraService_6336_OFFICECODE"] = "RISLTESTHQ";
	$configGlobal["EmitraService_6336_REVENUEHEAD1"] = "863-{application_fee}";
	$configGlobal["EmitraService_6336_REVENUEHEAD2"] = "|865-{form_commission}";
	$configGlobal["EmitraService_6336_SUBSERVICEID"] = "";
/* For Staging Admission Payment End */
 
/* For Staging Admission Payment Start*/
$configGlobal["EmitraService_7910_ENCKEY"] ='99D33C9478DC98B22F488D5674711'; //VNOCimQOgtp6AnpMcQLWcVOvVjva4p5y
$configGlobal["EmitraService_7910_CHECKSUMKEY"] = 'rsik4e5K6K#4'; //12072022- zS&vATW.BFXEjR, //EmitraNew@2016
$configGlobal["EmitraService_7910_COMMTYPE"] = "3";
$configGlobal["EmitraService_7910_MERCHANTCODE"] = "FOREST0117";
$configGlobal["EmitraService_7910_OFFICECODE"] = "DIV003";
$configGlobal["EmitraService_7910_REVENUEHEAD1"] = "880-{application_fee}";
$configGlobal["EmitraService_7910_REVENUEHEAD2"] = "|900-{form_commission}";
$configGlobal["EmitraService_7910_SUBSERVICEID"] = "";
$configGlobal["serialNumberMasterValue"] = 100000;


$configGlobal['DOCUMENT_ROOT'] = $_SERVER['DOCUMENT_ROOT'];
$configGlobal['subFolder'] = $subFolder;

$configGlobal['DOCUMENT_ROOT_FOLDER_PATH'] = $_SERVER['DOCUMENT_ROOT'] . '/'. $subFolder;
$configGlobal['PAST_DATA_DOCUMENT'] =  $pastdatadocument= $configGlobal['DOCUMENT_ROOT_FOLDER_PATH']. '/public/documents/'. $configGlobal['pastdata_document'] . '/';

$configGlobal['CenterAllotmentStreamId'] = 2;
$configGlobal['current_exam_month_id'] = 2;
$configGlobal['defaultStreamId'] = 2;
$configGlobal["sessionalAbsentMark"] = 999;
$configGlobal["extraPermissionSymbol"] = "_sql_";
$configGlobal['current_material_genertion_stream'] = "2";



/* For Staging Admission Payment End */

if(isset($configGlobal['defaultStreamId']) && $configGlobal['defaultStreamId']==2){
	$configGlobal['current_practical_session'] = "OCT - NOV - ".date("Y") . " - " . date('Y', strtotime('+1 year'));
} else {
	$configGlobal['current_practical_session'] = "MARCH - MAY - ".date('Y', strtotime('-1 year')) . " - " . date("Y") ;
}

$result = array(); 
foreach ($configGlobal as $key => $value){ 
	if (is_array($value)) { 
	  $result = array_merge($result, $value); 
	} 
	else { 
	  $result[$key] = $value; 
	} 
} 
$configGlobal = $result;
 
$whiteListMasterIps = $configGlobal['whiteListMasterIps'];
if(in_array($ip,$whiteListMasterIps)){
	
}else{
	//echo "<h1><center>Under Maintenance ... <br>We'll come back soon!</center></h1>";die;
}
return $configGlobal; 
