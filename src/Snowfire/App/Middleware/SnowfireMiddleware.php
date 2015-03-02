<?php namespace Snowfire\App\Middleware;

use Closure;

class SnowfireMiddleware {

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
        if ( ! \Snowfire::isRequestFromSnowfire() && ! config('snowfire.debug'))  {
            return \Response::make('Please request this url from a Snowfire component', 500);
        }

        $accountsRepository = app()->make('\Snowfire\App\Repositories\AccountsRepository');

        if (config('snowfire.debug')) {

            // In debug mode, use the first account id
            $app = $accountsRepository->first();

        } else {

            // Load Snowfire account based on URL
            parse_str($request->getQueryString(), $query);
            $app = $accountsRepository->getByKey($query['key']);

        }

        app()->make('view')->composer('*', function($view) use ($app)
        {
            $view->snowfire = $app;
        });

        app()->instance('snowfire', $app);

		return $next($request);
	}

}
