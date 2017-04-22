<?php

namespace App\Http\Middleware;

use Closure;

class ImageSize
{
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
            $url = $request->url();

            $info = pathinfo($url);
            $filename = $info['filename'];

            $params = explode('-', $filename);

            if (count($params) > 1) {
                $pattern = '#^[hw]{1}\d{1,}$#';

                foreach($params as $k => $v) {
                    if (0 !== $k) {
                        if (!preg_match($pattern, $v)) {
                            throw new \Exception('invalide image dimension');
                        }

                        if ('w' === substr($v, 0, 1)) {
                            $request->attributes->set('width', substr($v, 1));
                        }

                        $request->attributes->set('height', substr($v, 1));
                    }
                }
            }

            return $next($request);
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
