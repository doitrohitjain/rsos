<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use App\Providers\TelescopeServiceProvider;
use Illuminate\Support\Facades\URL;
use DB;
use File;
use Log;

class AppServiceProvider extends ServiceProvider
{
		/**
		 * Register any application services.
		 *
		 * @return void
		 */
		/**
		 * Bootstrap any application services.
		 *
		 * @return void
		 */
		public function boot()
		{
			Schema::defaultStringLength(191); 
			$domain = "10.68.181.236"; 
			if(@$_SERVER['HTTP_HOST']){ 
				if(isset($_SERVER['HTTP_HOST']) && !empty($_SERVER['HTTP_HOST'])){
					$domain = $_SERVER['HTTP_HOST'];
				} 
			} 
			$currentDomain = $domain;
			$subFolder = 'lrsos';
			$protocol = "http://";
			if(@$_SERVER['REQUEST_SCHEME']){
				$protocol = $_SERVER['REQUEST_SCHEME'] . "//";
			}
			if($currentDomain == 'rsosadmission.rajasthan.gov.in' || $currentDomain == 'www.rsosadmission.rajasthan.gov.in'){	
				URL::forceScheme('https');
				$this->app['request']->server->set('HTTPS','on');
			} 
			
			// if($_SERVER['REMOTE_ADDR'] == '10.68.181.236'){
				// DB::listen(function($query) {
					// $path = storage_path('logs\query.log');
					// $content = '[' . date('Y-m-d H:i:s') . '] ' . $_SERVER['REQUEST_URI'] . ' ' . PHP_EOL . $query->sql . ' [' . implode(', ', $query->bindings) . ']' . PHP_EOL . PHP_EOL;
					// File::append(
						// $path,
						// $content
					// );
				// });
			// }
		}
}
