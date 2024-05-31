<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
class ResourceControlMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // NAMESPACE
        if (str_contains($request->path(),'Namespaces') && ($request->method() == 'POST' || str_contains($request->path(),"/New"))) {
            if (str_contains(auth()->user()->resources,'Namespaces') || str_contains(auth()->user()->resources,'*')) {
                if ((str_contains(auth()->user()->verbs, 'create') || str_contains(auth()->user()->verbs,'*'))) {
                    return $next($request);
                }
            }
        }

        if (str_contains($request->path(),'Namespaces') && ($request->method() == 'DELETE')) {
            if (str_contains(auth()->user()->resources,'Namespaces') || str_contains(auth()->user()->resources,'*')) {
                if ((str_contains(auth()->user()->verbs, 'delete') || str_contains(auth()->user()->verbs,'*'))) {
                    return $next($request);
                }
            }
        }
        
        // PODS
        if (str_contains($request->path(),'Pods') && ($request->method() == 'POST' || str_contains($request->path(),"/New"))) {
            if (str_contains(auth()->user()->resources,'Pods') || str_contains(auth()->user()->resources,'*')) {
                if ((str_contains(auth()->user()->verbs, 'create') || str_contains(auth()->user()->verbs,'*'))) {
                    return $next($request);
                }
            }
        }

        if (str_contains($request->path(),'Pods') && ($request->method() == 'DELETE')) {
            if (str_contains(auth()->user()->resources,'Pods') || str_contains(auth()->user()->resources,'*')) {
                if ((str_contains(auth()->user()->verbs, 'delete') || str_contains(auth()->user()->verbs,'*'))) {
                    return $next($request);
                }
            }
        }

        // DEPLOYMENTS
        if (str_contains($request->path(),'Deployments') && ($request->method() == 'POST' || str_contains($request->path(),"/New"))) {
            if (str_contains(auth()->user()->resources,'Deployments') || str_contains(auth()->user()->resources,'*')) {
                if ((str_contains(auth()->user()->verbs, 'create') || str_contains(auth()->user()->verbs,'*'))) {
                    return $next($request);
                }
            }
        }

        if (str_contains($request->path(),'Deployments') && ($request->method() == 'DELETE')) {
            if (str_contains(auth()->user()->resources,'Deployments') || str_contains(auth()->user()->resources,'*')) {
                if ((str_contains(auth()->user()->verbs, 'delete') || str_contains(auth()->user()->verbs,'*'))) {
                    return $next($request);
                }
            }
        }

        // SERVICES
        if (str_contains($request->path(),'Services') && ($request->method() == 'POST' || str_contains($request->path(),"/New"))) {
            if (str_contains(auth()->user()->resources,'Services') || str_contains(auth()->user()->resources,'*')) {
                if ((str_contains(auth()->user()->verbs, 'create') || str_contains(auth()->user()->verbs,'*'))) {
                    return $next($request);
                }
            }
        }

        if (str_contains($request->path(),'Services') && ($request->method() == 'DELETE')) {
            if (str_contains(auth()->user()->resources,'Services') || str_contains(auth()->user()->resources,'*')) {
                if ((str_contains(auth()->user()->verbs, 'delete') || str_contains(auth()->user()->verbs,'*'))) {
                    return $next($request);
                }
            }
        }
        // INGRESSES
        if (str_contains($request->path(),'Ingresses') && ($request->method() == 'POST' || str_contains($request->path(),"/New"))) {
            if (str_contains(auth()->user()->resources,'Ingresses') || str_contains(auth()->user()->resources,'*')) {
                if ((str_contains(auth()->user()->verbs, 'create') || str_contains(auth()->user()->verbs,'*'))) {
                    return $next($request);
                }
            }
        }

        if (str_contains($request->path(),'Ingresses') && ($request->method() == 'DELETE')) {
            if (str_contains(auth()->user()->resources,'Ingresses') || str_contains(auth()->user()->resources,'*')) {
                if ((str_contains(auth()->user()->verbs, 'delete') || str_contains(auth()->user()->verbs,'*'))) {
                    return $next($request);
                }
            }
        }

        // BACKUPS
        if (str_contains($request->path(),'Backups')) {
            if (str_contains(auth()->user()->resources,'Backups') || str_contains(auth()->user()->resources,'*')) {
                if ((str_contains(auth()->user()->verbs, 'create') || str_contains(auth()->user()->verbs,'*'))) {
                    return $next($request);
                }
            }
        }

        // BACKUPS
        if (str_contains($request->path(),'CustomResources')) {
            if (str_contains(auth()->user()->resources,'CustomResources') || str_contains(auth()->user()->resources,'*')) {
                if ((str_contains(auth()->user()->verbs, 'create') || str_contains(auth()->user()->verbs,'*'))) {
                    return $next($request);
                }
            }
        }

        abort(403);
    }
}
