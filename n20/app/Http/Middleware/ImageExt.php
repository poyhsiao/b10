<?php

namespace App\Http\Middleware;

use Closure;
use Jenssegers\Agent\Agent;

class ImageExt
{
    private $_acceptExt = [
        'jpg',
        'png',
        'webp',
    ];

    private function _checkAccepts($header = '')
    {
        try {
            $pattern = '#image/webp#i';

            return preg_match($pattern, $header);
        } catch (\Exception $e) {
            \Log::error($e->getMessage(), [
                'method' => __METHOD__,
                'trace' => $e->getTrace(),
            ]);

            return false;
        }
    }

    private function _checkBrowser($browser = '')
    {
        try {
            $agent = new Agent();

            $browser = strtolower($agent->browser());
            $platform = strtolower($agent->platform());

            $v = explode('.', $agent->version($browser));

            $bversion = (int) $v[0];

            $pversion = (int) $agent->version($platform);

            if ('chrome' === $browser && $bversion > 22) {
                return true;
            }

            if ('opera' === $browser && $bversion > 11) {
                return true;
            }

            if ('opera mini' === $browser) {
                return true;
            }

            if ('opera mobile' === $browser && $bversion > 12) {
                return true;
            }

            if ('androidos' === $platform && $pversion > 4) {
                return true;
            }

            if ('chrome' === $browser && $bversion > 57) {
                return true;
            }

            if (preg_match('#Samsung Internet#i', $browser) && $bversion > 3) {
                return true;
            }

            if (preg_match('#QQ#i', $browser) && $bversion > 1.2) {
                return true;
            }

            return false;
        } catch (\Exception $e) {
            \Log::error($e->getMessage(), [
                'method' => __METHOD__,
                'trace' => $e->getTrace(),
            ]);
        }
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            $accepts = $request->header('accept', []);

            if ($this->_checkAccepts($accepts) || $this->_checkBrowser()) {
                $request->attributes->add(['ext' => 'webp']);
                return $next($request);
            }

            $url = $request->url();
            $info = pathinfo($url);

            if (array_key_exists('extension', $info) && in_array($info['extension'], $this->_acceptExt, false)) {
                $request->attributes->set('ext', $info['extension']);
                return $next($request);
            }


            return response()
                ->redirectToRoute('index');
        } catch (\Exception $e) {
            \Log::error($e->getMessage(), [
                'method' => __METHOD__,
                'trace' => $e->getTrace(),
            ]);

            return response()
                ->redirectToRoute('index');
        }
    }
}
