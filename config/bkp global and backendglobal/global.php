<?php
$configGlobal = array();

$production_server = array(
	'10.70.240.222',
	'172.21.90.58',
	'rsosadmission.rajasthan.gov.in',
	'www.rsosadmission.rajasthan.gov.in',
	'172.21.90.58:8001',
	'172.21.90.58:8002',
	'172.21.90.58:8003',
	'172.21.90.58:8004',
	'172.21.90.58:8005',
	'172.21.90.58:8006',
	'172.21.90.58:8007',
	'172.21.90.58:8008',
	'172.21.90.58:8009',
	'172.21.90.58:8010',
	'172.21.90.58:8011',
	'172.21.90.58:8012',
	'172.21.90.58:8013',
	'172.21.90.58:8014'
);
// $controller = class_basename($request->route()->getController());
// $action = $request->route()->getActionMethod(); 
// dd($action);
 // dd($_SERVER);
/* Stop all other services start */
$isAllowTimited = true;
$tempAllowedPaths = array("/rsos/","/rsos/provisionalresult");
if($isAllowTimited && @$_SERVER['REQUEST_URI'] && in_array($_SERVER['REQUEST_URI'], $tempAllowedPaths)){
}else{
	// echo "<center><h2>We are coming soon..please wait....</h2></center>"; die;
}
/* Stop all other services end */


/* Stop on Mobile start */
if(str_contains(@$_SERVER['HTTP_USER_AGENT'],"Android")){
	//echo "<center>We are coming soon on your device..please wait....</center>"; die;
} 
/* Stop on Mobile end */



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
 

//$result_process_allowed_ips = '{"0":"10.68.181.236","1":"10.68.181.1751"}';

 
$currentDomain = $domain;
$subFolder = 'rsos';
$protocol = "https://"; 


if(@$_SERVER['REQUEST_SCHEME']){
	$protocol = $_SERVER['REQUEST_SCHEME'] . "://";
}
if(@$_SERVER['HTTP_HOST'] == '10.68.181.236' || @$_SERVER['HTTP_HOST'] == '10.68.252.122' || @$_SERVER['HTTP_HOST'] == '103.203.137.51'){
    $protocol = "http://";
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
		"DB_PORT" => 3307,
		"DB_DATABASE" => 'lrsos',
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
		"DB_PORT" => 3307,
		"DB_DATABASE" => 'lrsos',
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
// }else if($currentDomain == '10.70.240.222' || $currentDomain == '172.21.90.58' || $currentDomain == 'rsosadmission.rajasthan.gov.in' || $currentDomain == 'www.rsosadmission.rajasthan.gov.in'){	
}else if(in_array($currentDomain, $production_server)){	
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
		"APP_URL" => $protocol . "10.68.181.213/".$subFolder . "/"
	);
	$configGlobal[] = array(
		"DB_HOST" => '10.68.128.254', //10.68.181.175
		"DB_PORT" => 3306,
		"DB_DATABASE" => 'lrsos',
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
 
$configGlobal[]['allowedMacAddressList'] = array("70-5a-0f-3b-66-74","8c-ec-4b-5e-76-03");

$configGlobal[]['allowedBackButtonForRoles']=array(40,58,60,64,66,71,72,73,74,75,76,78,82);


// $configGlobal[]['allowpreviousyear'] = array("13" => "2017-18", "120" => "2018-19","121" => "2019-20","122" => "2020-21","123" => "2021-22","124" => "2022-23");




// $configGlobal['ajaxBaseUrl'] = $protocol . $currentDomain . "/".$subFolder . "/ajax/" ;
// $configGlobal['ajaxBaseUrl'] = url('/ajax/');
$configGlobal['logoutUrl'] = $protocol . $currentDomain . "/".$subFolder . "/logout";
$configGlobal['ajaxBaseUrl'] = "../ajax/";

$configGlobal['api_token'] = "IUAjJCVeJiooKWRvaXRj";//!@#$%^&*()doitc IUAjJCVeJiooKWRvaXRj IUAjJCVeJiooKWRohit
$configGlobal['api_token2'] = "IUAjJCVeJiooKWRohit";//!@#$%^&*()doitc IUAjJCVeJiooKWRvaXRj IUAjJCVeJiooKWRohit
$configGlobal['api_token3'] = "IUAjJCVeJiooJNRohit";//!@#$%^&*()doitc IUAjJCVeJiooKWRvaXRj IUAjJCVeJiooKWRohit
// $configGlobal['siteTitle'] = "राजस्थान स्टेट ओपन स्कूल, जयपुर ". date("Y") . "-" . date('Y', strtotime('+1 year'));
$configGlobal['siteTitle'] = "राजस्थान स्टेट ओपन स्कूल, जयपुर 2024-2025 ";
$configGlobal['test'] = "राजस्थान स्टेट ओपन स्कूल, जयपुर 2024-2025 ";
$configGlobal['title'] = "राजस्थान स्टेट ओपन स्कूल, जयपुर  2024-2025 ";
$configGlobal['starMark'] = "<span class='starmark' style='color:red;'> *</span>";

$configGlobal['SSO_API_USERNAME'] = 'madarsa.test';
$configGlobal['SSO_API_PASSWORD'] = 'Test@1234';
$configGlobal['CURRENT_IP'] = $ip;


// print_r($configGlobal);die;



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
$configGlobal['publication_department'] = 86;
$configGlobal['aicenter_id'] = 59;
$configGlobal['verifier_id'] = 84;
$configGlobal['marksheet_verification'] = 78;
$configGlobal['evaluation_department'] = 74;
$configGlobal['secrecy_epartment'] = 73;
$configGlobal['evaluation_admin'] = 76;
$configGlobal['Examcenter'] = 60;
$configGlobal['Printer'] = 64;
$configGlobal['student'] = 41;
$configGlobal['examination_admin'] = 69;
$configGlobal['report_admin'] = 65;
$configGlobal['academicofficer_id'] = 85;
$configGlobal['verifier_admin_id'] = 87;
$configGlobal['disadvantage_group_admin'] = 88;

$configGlobal[]['form_session_changed'] = array(
	$configGlobal['developer_admin'],
	$configGlobal['aicenter_id'],
	$configGlobal['verifier_id'],
	$configGlobal['verifier_admin_id'],
	$configGlobal['deo'],
	$configGlobal['publication_department']	,
	$configGlobal['marksheet_verification']	,
	//$configGlobal['Examcenter'],
	$configGlobal['secrecy_admin'],
	$configGlobal['super_admin'],
	$configGlobal['evaluation_admin'],
	$configGlobal['examination_department'],
	$configGlobal['Printer'],
	$configGlobal['student'],
	$configGlobal['practicalexaminer'],
	$configGlobal['examination_admin'],
	$configGlobal['evaluation_department'],
	$configGlobal['report_admin'],
	$configGlobal['academicofficer_id'],
	$configGlobal['secrecy_epartment'],
	$configGlobal['disadvantage_group_admin'],
	$configGlobal['theoryexaminer'], 
);

$configGlobal[]['allowOnlyPreviousYears'] = array(
	//$configGlobal['theoryexaminer'],
	//$configGlobal['practicalexaminer'],
	//$configGlobal['evaluation_department'],
	//$configGlobal['secrecy_epartment'],
	//$configGlobal['deo'],
	// $configGlobal['examination_department']
	
    
);
$configGlobal[]['allowOnlyCurrentYears'] = array(
	$configGlobal['verifier_id'],
	$configGlobal['deo'],
	$configGlobal['publication_department'],
	$configGlobal['verifier_id'],
	$configGlobal['disadvantage_group_admin'],
	$configGlobal['student'],
	$configGlobal['evaluation_department'],
	$configGlobal['practicalexaminer'],
	$configGlobal['theoryexaminer'],
	$configGlobal['secrecy_epartment']
	
);

$configGlobal[]['allowPreviousAndCureentYears'] = array(
	//$configGlobal['aicenter_id'],
	$configGlobal['practicalexaminer'],
	//$configGlobal['Examcenter'],
	//$configGlobal['secrecy_admin'],
	// $configGlobal['super_admin'],
	//$configGlobal['evaluation_admin'],
	$configGlobal['verifier_admin_id'],
	//$configGlobal['examination_department'],
	$configGlobal['Printer'],
	//$configGlobal['evaluation_department'],
	$configGlobal['examination_admin'],
	$configGlobal['academicofficer_id'],
); 

$configGlobal['defaultPageLimit'] = 20;
// $configGlobal['revalMarksDefaultPageLimit'] = 40;
$configGlobal['SuppDefaultStreamId'] = 2;
$configGlobal['aicenterSubjectWiseStream'] = 1;
$configGlobal['defaultCousre'] = 12;
$configGlobal['Examcenter'] = 60;

$configGlobal['supp_undertaking_msg'] = "मेरे द्वारा प्रदान की गई उपरोक्त सभी जानकारी सत्य है और अन्यथा मैं अपना आवेदन वापस लेने के लिए स्वीकार करता हूं। (All the above information provided by me is true and otherwise I accept to withdraw my application.)";

$configGlobal['fresh_form_undertaking_msg'] = "प्रमाणित किया जाता है कि आवेदन-पत्र में अंकित समस्त प्रविष्टियों की जाँच सावधानीपूर्वक संलग्न प्रमाण-पत्रों द्वारा कर ली गई है तथा समस्त प्रविष्टियां सही पाई गई एवं अभ्यर्थी आवेदित श्रेणी तथा पाठ्यक्रम के लिए आर.एस.ओ.एस. के नियमानुसार पात्र है। ";
$configGlobal['fresh_form_second_undertaking_msg'] = "<li> मै  माध्यमिक स्तर  के  पाठ्यक्रम पढने  में समर्थ  हूँ | I am able to pursue secondary level course.</li>
<li>मेने  विवरणिका में दी गई योग्यता, शर्ते  पढकर  समझ ली है में इसके योग्य हूँ |  I am eligible for Registration according to rules of RSOS.</li>
<li>मेने  सभी जरुरी सूचनाये और प्रमाण पत्र सही सही दिए है | में जानता हूँ कि यदि यह सूचनाये गलत या भ्रम में डालने वाली होती है तो राजस्थान स्टेट ओपन स्कूल द्वारा मेरे उम्मीदवारी समाप्त की जा सकती है |  I have furnished the necessary information/documents correctly. I understand that my candidature is liable to be cancelled by RSOS in the event of this information is found incorrect or misleading.</li>
<li> में आरएसओएस  के सभी नियमो का पालन करूँगा और सन्दर्भ केंद्र के अनुशासन  और मर्यादा को बनाये रखूँगा |   I shall be abide by all the rules and regulation of RSOS and shall maintain discipline and decorum of AI.</li>";


$configGlobal['fresh_form_undertaking_msg_student_pdf'] = "प्रमाणित किया जाता है कि आवेदन-पत्र में अंकित समस्त प्रविष्ठियाँ सही भरी गई है। मेरे द्वारा दी गई सभी प्रविष्टियों में कोई त्रुटि पाऐ जाने पर आवेदन रद्द होने का उत्तरदायित्व मेरा रहेगा। ";

$configGlobal['student_change_request_notification'] = "कृपया अपना आवेदन लॉक एंड सबमिट और भुगतान (यदि लागू हो) के साथ पूरा करें। अन्यथा विभाग द्वारा फॉर्म रिजेक्ट कर दिया जायेगा.(Please complete your application with Lock&Submit and Payment(if applicable). Otherwise form will be rejected by the department.)";
 



$configGlobal['supp_subject_count'] = 2;
$configGlobal['supp_stream'] = 1;

$configGlobal['exam1'] = 'March-May 2022';
$configGlobal['exam2'] = 'Oct-Nov 2021';
$configGlobal['result_date'] = '10-09-2024';//15-02-2024
$configGlobal['result_month'] = 'March-May 2022';
$configGlobal['pastdata_document'] = 'pastdata';

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

$configGlobal[]['fresh_ver_not_requried_doc_input'] = array(26,27);


$configGlobal["theory_copy_checking_exam_year"] = 126;
$configGlobal["theory_copy_checking_exam_month"] = 2;
$configGlobal["sessionalAbsentMark"] = 999;
$configGlobal["extraPermissionSymbol"] = "_sql_";
$configGlobal['student_doc_rejected_notification'] = "Your documents has been marked rejectect.Please contact to RSOS for further updates.";



/* For Staging Admission Payment End */

if(isset($configGlobal['defaultStreamId']) && $configGlobal['defaultStreamId']==2){
	$configGlobal['current_practical_session'] = "OCT - NOV - ".date("Y") . " - " . date('Y', strtotime('+1 year'));
} else {
	$configGlobal['current_practical_session'] = "MARCH - MAY - ".date('Y', strtotime('-1 year')) . " - " . date("Y") ;
}

//----------------- Start1
$filePath = config_path('backend_changeable_global.php');
$configGlobal2 = include  $filePath;
//----------------- End1

// dd($configGlobal2);
// dd($configGlobal);
$configGlobal = array_merge($configGlobal,@$configGlobal2);
// dd($configGlobal);

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
