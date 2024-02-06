<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use View;

class CommonContent
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
          
        $url = env("API_BASE_URL").'api/web/home-content/get';
        $response = Http::get($url);


        $this->commonContent = $response['payload'];

        // dd($this->commonContent);
        View::share('commonContent', $this->commonContent);

        return $next($request);
    }
}
