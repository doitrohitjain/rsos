<?php

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| The first thing we will do is create a new Laravel application instance
| which serves as the "glue" for all the components of Laravel, and is
| the IoC container for the system binding all of the various parts.
|
*/

//$app = new Illuminate\Foundation\Application(
    //$_ENV['APP_BASE_PATH'] ?? dirname(__DIR__)
//);
$app = new Illuminate\Foundation\Application(
    realpath(__DIR__.'/../')
);

 
/*
|--------------------------------------------------------------------------
| Bind Important Interfaces
|--------------------------------------------------------------------------
|
| Next, we need to bind some important interfaces into the container so
| we will be able to resolve them when needed. The kernels serve the
| incoming requests to this application from both the web and CLI.
|
*/

$app->singleton(
    Illuminate\Contracts\Http\Kernel::class,
    App\Http\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

/*
|--------------------------------------------------------------------------
| Return The Application
|--------------------------------------------------------------------------
|
| This script returns the application instance. The instance is given to
| the calling script so we can separate the building of the instances
| from the actual running of the application and sending responses.
|
 
 */ 
if(@$_SERVER['HTTP_HOST']){ 
    if($_SERVER['HTTP_HOST'] == '10.68.252.67' ){ 
        $app->loadEnvironmentFrom('.env.10.68.252.67');	
  }else if($_SERVER['HTTP_HOST'] == '172.21.90.58' ){ 
        $app->loadEnvironmentFrom('.env.172.21.90.58');		  
    }else if($_SERVER['HTTP_HOST'] == 'rsosadmission.rajasthan.gov.in' ){ 
        if($_SERVER['REMOTE_ADDR'] == '10.68.181.236'){
            $app->loadEnvironmentFrom('.env.debugon.rsosadmission.rajasthan.gov.in');
        }else{
            $app->loadEnvironmentFrom('.env.rsosadmission.rajasthan.gov.in');
        } 
    }else if($_SERVER['HTTP_HOST'] == '10.68.181.213'){
        $app->loadEnvironmentFrom('.env.10.68.181.249');		  
    }else if($_SERVER['HTTP_HOST'] == 'localhost:91'){ 
        $app->loadEnvironmentFrom('.env.10.68.181.236');		  
    }else if($_SERVER['HTTP_HOST'] == 'localhost:92'){ 
        $app->loadEnvironmentFrom('.env.10.68.181.236');		  
    }else if($_SERVER['HTTP_HOST'] == '10.68.181.175:8080'){
        $app->loadEnvironmentFrom('.env.10.68.181.175');
    }else if($_SERVER['HTTP_HOST'] == '10.68.25c.122'){
        $app->loadEnvironmentFrom('.env.10.68.252.122');
    }else if($_SERVER['HTTP_HOST'] == '10.68.181.229'){
        $app->loadEnvironmentFrom('.env.10.68.181.229');
    }else if($_SERVER['HTTP_HOST'] == '172.21.90.58'){
        $app->loadEnvironmentFrom('.env.172.21.90.58');
    }
}else{ 
    $app->loadEnvironmentFrom('.env');
} 
return $app;
