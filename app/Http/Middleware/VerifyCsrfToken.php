<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    protected $addHttpCookie = true;
    protected $except = [
        '/login',
        '/payments/response',
        '/mobile_view',
        '/mobile_view/mobile_view_hallticketbulview',
        '/payments/registration_fee',
        '/supp_payments/supp_response',
        '/supp_payments/supp_registration_fee',
        '/reval_payments/reval_response',
        '/reval_payments/reval_registration_fee',
        '/fsdv_payments/fsdv_response',
        '/fsdv_payments/fsdv_registration_fee',
        '/api_hall_ticket_for_mobile_single_enrollment_view',
		'/student/select_aicenter',
		'/student/new_term_conditions',
		'/student/self_registration',
		'/hall_ticket_api_download',
		'/marksheet_payments/marksheet_response',
		'/Change_Request_payments/change_request_response',
		'/supp_payments/supp_change_request_response',
		
    ];
}
