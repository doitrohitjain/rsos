<?php 

use Illuminate\Support\Facades\Route;

$currentController = class_basename(Route::current()->controller); // Get the current controller name
$currentAction = Route::currentRouteAction(); // Get the current controller and action
// echo $currentController . ' ' . $currentAction;

$allowedControllers = [     
    'ApplicationController@applicationverifyerdashboard',
    'ApplicationController@printer',
    'ApplicationController@applicationdashboard',
    'ApplicationController@applicationacademicofficer_dashboard',
    'ApplicationController@student',
    'ApplicationController@examcenter',
    'ApplicationController@examiner',
    'ApplicationController@theroy_examiner',
    'ApplicationController@evaluation',
    'UserController@dashboard',
    'ApplicationController@secrecy',
    'ApplicationController@practical_examiner',
    'ApplicationController@marksheetverificationdashboard',
    'ApplicationController@examination_admin',
    'ApplicationController@application_verifier_admin_dashboard',
    'ApplicationController@rsos_officer_grade_1',
    'ApplicationController@rsos_officer_grade_2',
    'ApplicationController@rsos_officer_grade_3',
    'ApplicationController@rsos_officer_grade_4',
    'ApplicationController@rsos_officer_grade_5',
    'ApplicationController@publication_dept',
    'ApplicationController@deo',
    'ApplicationController@secrecy',
    'ApplicationController@examination_department'
]; 
?>

@if (in_array($currentController . '@' . explode('@', $currentAction)[1], $allowedControllers)) 
	<div class="col s12"> 
		<div class="container">
			<div class="seaction" style="padding-left:250px;">
				<img class="" src="{{asset('public/app-assets/images/new.jpg')}}" height="20" alt="materialize logo"/>
				<span class="" style="color: #fff !important;border-radius: 4px;background-color: #e91e63!important;font-size:16px;">
					<span id="mainalert"></span>
					&nbsp;
				</span>
					<i class="material-icons tooltipped" data-position="bottom" data-tooltip="डैशबोर्ड में आसान नेविगेशन के लिए कॉलेप्सिबल लेबल किए गए हैं।  यह स्क्रीन की जगह बचाने और डैशबोर्ड को व्यवस्थित बनाए रखने में मदद करता है। लेबल वाले आइटम्स के साथ, उपयोगकर्ता आसानी से किसी भी विशेष सेक्शन को पहचान कर वहां नेविगेट कर सकते हैं। (Collapsible labels are provided in the dashboard for easy navigation. These help save screen space and keep the dashboard organized. With labeled items, users can easily identify and navigate to any specific section.) ">info</i>
				</span>
			</div>
		</div>
	</div>
@endif