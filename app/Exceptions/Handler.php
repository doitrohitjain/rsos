<?php

namespace App\Exceptions;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Throwable;
use Config;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }
	
	 
    public function render($request, Throwable $e)
     {  
	   
		  //if ($this->isHttpException($e)== false) {
			//$url = $BACK_TO_SSO_URL = Config::get("global.BACK_TO_SSO_LOGOUT_URL");		
           // return redirect($url);
		  if($this->isHttpException($e)) {
            if ($e->getStatusCode() == 404) {
                return response()->view('error.404');
            }  
            if ($e->getStatusCode() == 500) {
                return response()->view('error.500');
             } 
            if ($e->getStatusCode() == 403) {
               return back()->with('error', '403 USER DOES NOT HAVE THE RIGHT PERMISSIONS.'); 
           }
		   
            		   
		}   
		  
		// if ($e->getCode() == 0 || $e->getCode() == 500) { 
		  //   return response()->view('error.500');
		// } 

		$this->renderable(function (\Spatie\Permission\Exceptions\UnauthorizedException $e, $request)	{  
	        return back()->with('error', '403 USER DOES NOT HAVE THE RIGHT PERMISSIONS.');   
		});
		
	    return parent::render($request, $e);
		
    } 
}



