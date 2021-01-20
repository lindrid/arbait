<?php

namespace App\Http\Middleware;

use App\User;
use Closure;

class CheckPrivilege
{
    private $uri;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $route = $request->route();
        if (array_key_exists('any', $route->parameters))
        {
            $uri = str_replace(
                '{any}', $route->parameters['any'],
                $route->uri
            );
            $this->setUri($uri);
        }
        else
        {
            $this->setUri($route->uri);
        }
        $error = false;
        $errorMsg = '';

        if ($this->uriContain('apps') ||
           $this->uriContain('app/show') ||
           $this->uriContain('api/application/index') ||
           $this->uriContain('api/application/show'))
        {
           if (!User::can('WatchApplications'))
           {
               $error = true;
               $errorMsg = 'Вы не можете смотреть заявки';
           }
        }
        else if (
            $this->uriContain('app/create') ||
            $this->uriContain('api/application/store'))
        {
           if (!User::can('CreateApplications'))
           {
               $error = true;
               $errorMsg = 'Вы не можете создавать заявки';
           }
        }
        else if ($this->uriContain('api/application/delete'))
        {
           if (!User::can('DeleteApplications'))
           {
               $error = true;
               $errorMsg = 'Вы не можете удалять заявки';
           }
        }
        else if (
            $this->uriContain('app/edit') ||
            $this->uriContain('api/application/update') ||
            $this->uriContain('api/application/edit'))
        {
            if (!User::can('EditApplications'))
            {
                $error = true;
                $errorMsg = 'Вы не можете изменять заявки';
            }
        }
        else if (
            $this->uriContain('api/application/assign/worker') ||
            $this->uriContain('api/application/delete/worker') ||
            $this->uriContain('api/application/end') ||
            $this->uriContain('api/application/save') ||
            $this->uriContain('api/application/ready/to/pay') ||
            $this->uriContain('api/application/rollback') ||
            $this->uriContain('api/application/worker/got/money') ||
            $this->uriContain('api/application/payedbyclient') ||
            $this->uriContain('api/worker/card'))
        {
            if (!User::can('ChangeApplicationInsides'))
            {
                $error = true;
                $errorMsg = 'Вы не можете вносить изменения в заявки';
            }
        }
        else if (
            $this->uriContain('accountancy/create') ||
            $this->uriContain('accountancy/detail/') ||
            $this->uriContain('api/accountancy/index') ||
            $this->uriContain('api/accountancy/store')
        )
        {
            if (!User::can('WatchMoversReport'))
            {
                $error = true;
                $errorMsg = 'Вы не можете смотреть отчет';
            }
            else if ($this->uriContain('api/accountancy/store'))
            {
                if (!User::can('EditMoversReport'))
                {
                    $error = true;
                    $errorMsg = 'Вы не можете вносить изменения в отчет';
                }
            }
        }
        else if ($this->uriContain('api/accountancy/app/delete'))
        {
            if (!User::can('EditMoversDetailing'))
            {
                $error = true;
                $errorMsg = 'Вы не можете вносить изменения в детализацию';
            }
        }
        else if (
            $this->uriContain('accountancy/instagram/create') ||
            $this->uriContain('accountancy/instagram/detail/day') ||
            $this->uriContain('api/accountancy/instagram/stat') ||
            $this->uriContain('api/accountancy/instagram/get-publics') ||
            $this->uriContain('api/accountancy/instagram/index') ||
            $this->uriContain('api/accountancy/instagram/store')
        )
        {
           if (!User::can('WatchInstaReport'))
           {
               $error = true;
               $errorMsg = 'Вы не можете смотреть отчет по инстраграмму';
           }
           else if ($this->uriContain('api/accountancy/instagram/store'))
           {
               if (!User::can('EditInstaReport'))
               {
                   $error = true;
                   $errorMsg = 'Вы не можете вносить изменения в отчет по инстраграмму';
               }
           }
        }
        else if ($this->uriContain('api/accountancy/instagram/delete/last'))
        {
            if (!User::can('EditInstaReport'))
            {
                $error = true;
                $errorMsg = 'Вы не можете вносить изменения в отчет по инстраграмму';
            }
        }
        else if ($this->uriContain('api/accountancy/instagram/pic/delete'))
        {
            if (!User::can('EditInstaDetailing'))
            {
                $error = true;
                $errorMsg = 'Вы не можете вносить изменения в детализацию по инстраграмму';
            }
        }

        //$request->attributes->add(['route' => $route]);

        if ($error)
        {
            User::saveRedirectAddress($this->getUri());
            return \Redirect::route('error', ['errorMsg' => $errorMsg]);
        }

        return $next($request);
    }

    private function setUri($uri)
    {
        $this->uri = $uri;
    }

    private function getUri()
    {
        return $this->uri;
    }

    private function uriContain($query)
    {
        return (strpos($this->uri, $query) !== false);
    }
}
