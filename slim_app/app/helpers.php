<?php
/* Global Helper Functions */
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Odan\Session\SessionInterface;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;
use Jenssegers\Blade\Blade;
// "./app/Logger.php",
use App\Logger;

# app
if (!function_exists('app')) {
  function app() {
    return $_SERVER['app'];
  }
}

# view
if (!function_exists('view')) {
  function view(ResponseInterface $res, $tpl, $v = []) {
    $cache = __DIR__ . '/../tmp/views_cache';
    $views = __DIR__ . '/../res/views';
    $blade = (new Blade($views, $cache))->make($tpl, $v);
    $res->getBody()->write($blade->render());
    return $res;
  }
}

# redirect
if (!function_exists('redirect')) {
  function redirect($uri) {
    @header("Location: $uri", true, 301);
    exit();
  }
}

# asset
if (!function_exists('asset')) {
    function asset($path) {
        return env('APP_URL') . "/{$path}";
    }
}

# collect
if (!function_exists('collect')) {
    function collect($items) {
        return new Collection($items);
    }
}

# factory
if (!function_exists('factory')) {
    function factory(string $model, int $count = 1) {
        $factory = new Factory;
        return $factory($model, $count);
    }
}

# env
if (!function_exists('env')) {
    function env($key, $default = null) {
        $value = getenv($key) ?? null;
        throw_when(
          $value === null && $default === null,
          "{$key} is not a defined .env variable and has not default value"
        );
        return $value === null ? $default : $value;
    }
}

# base_path
if (!function_exists('base_path')) {
    function base_path($path = '') {
        $dir = setting('root', __DIR__ . "/../");
        return "$dir/$path";
    }
}

# public_path
if (!function_exists('public_path')) {
    function public_path($path = '') {
        $dir = setting('public', 'public');
        return "$dir/$path";
    }
}

# resources_path
if (!function_exists('resources_path')) {
    function resources_path($path = '') {
        return base_path("res/$path");
    }
}

# app_path
if (!function_exists('app_path')) {
    function app_path($path = '') {
        return base_path("app/$path");
    }
}

# media_path
if (!function_exists('media_path')) {
    function media_path($path = '') {
        $dir = setting('media', 'cdn');
	return base_path("$dir/$path");
    }
}

# media_url
if (!function_exists('media_url')) {
    function media_url($path = '') {
        return "/media/$path";
    }
}

# tmp_path
if (!function_exists('tmp_path')) {
    function tmp_path($path = '') {
        $dir = setting('temp', base_path('tmp'));
        return "$dir/$path";
    }
}

# dd
if (!function_exists('dd')) {
    function dd() {
        array_map(function ($content) {
            echo "<pre>";
            var_dump($content);
            echo "</pre>";
            echo "<hr>";
        }, func_get_args());
        die;
    }
}

# dump
if (!function_exists('dump')) {
  function dump($v = null, $pre = false) {
    ob_start();
    var_dump($v);
    $content = ob_get_contents();
    ob_end_clean();
    return $pre ? '<pre>' . $content . '</pre>' : $content;
  }
}

# throw_when
if (!function_exists('throw_when')) {
    function throw_when(bool $fails, string $message, string $exception = Exception::class) {
        if (!$fails) return;
        throw new $exception($message);
    }
}

# class_basename
if (!function_exists('class_basename')) {
    function class_basename($class) {
        $class = is_object($class) ? get_class($class) : $class;
        return basename(str_replace('\\', '/', $class));
    }
}

# setting
if (!function_exists('setting')) {
    function setting($key = null, $value = null) {
        $settings = app()->getContainer()->get('settings');
        return $settings[$key] ?? $value;
    }
}

# session
if (!function_exists('session')) {
    function session($key = null, $value = null) {
        $session = app()->getContainer()->get(SessionInterface::class);
        if (isset($session)) {
          return $session->get($key) ?? $value;
        }
        return $value;
    }
}

# data_get
if (!function_exists('data_get')) {
    /**
     * Get an item from an array or object using "dot" notation.
     * @param  mixed  $target
     * @param  string|array|int|null  $key
     * @param  mixed  $default
     * @return mixed
     */
    function data_get($target, $key, $default = null) {
        if (is_null($key)) {
            return $target;
        }
        $key = is_array($key) ? $key : explode('.', $key);
        while (! is_null($segment = array_shift($key))) {
            if ($segment === '*') {
                if ($target instanceof Collection) {
                    $target = $target->all();
                } elseif (! is_array($target)) {
                    return value($default);
                }
                $result = [];
                foreach ($target as $item) {
                    $result[] = data_get($item, $key);
                }
                return in_array('*', $key) ? Arr::collapse($result) : $result;
            }
            if (Arr::accessible($target) && Arr::exists($target, $segment)) {
                $target = $target[$segment];
            } elseif (is_object($target) && isset($target->{$segment})) {
                $target = $target->{$segment};
            } else {
                return value($default);
            }
        }
        return $target;
    }
}

# data_set
if (!function_exists('data_set')) {
    /**
     * Set an item on an array or object using dot notation.
     * @param  mixed  $target
     * @param  string|array  $key
     * @param  mixed  $value
     * @param  bool  $overwrite
     * @return mixed
     */
    function data_set(&$target, $key, $value, $overwrite = true) {
        $segments = is_array($key) ? $key : explode('.', $key);
        if (($segment = array_shift($segments)) === '*') {
            if (! Arr::accessible($target)) {
                $target = [];
            }
            if ($segments) {
                foreach ($target as &$inner) {
                    data_set($inner, $segments, $value, $overwrite);
                }
            } elseif ($overwrite) {
                foreach ($target as &$inner) {
                    $inner = $value;
                }
            }
        } elseif (Arr::accessible($target)) {
            if ($segments) {
                if (! Arr::exists($target, $segment)) {
                    $target[$segment] = [];
                }
                data_set($target[$segment], $segments, $value, $overwrite);
            } elseif ($overwrite || ! Arr::exists($target, $segment)) {
                $target[$segment] = $value;
            }
        } elseif (is_object($target)) {
            if ($segments) {
                if (! isset($target->{$segment})) {
                    $target->{$segment} = [];
                }
                data_set($target->{$segment}, $segments, $value, $overwrite);
            } elseif ($overwrite || ! isset($target->{$segment})) {
                $target->{$segment} = $value;
            }
        } else {
            $target = [];
            if ($segments) {
                data_set($target[$segment], $segments, $value, $overwrite);
            } elseif ($overwrite) {
                $target[$segment] = $value;
            }
        }
        return $target;
    }
}

# csrf
if (!function_exists('csrf')) {
  /**
   * Generate a CSRF token form field.
   * @return \Illuminate\Support\HtmlString
   */
  function csrf() {
      return new HtmlString('<input type="hidden" name="_token" value="' . csrf_token() . '">');
  }
}

# csrf_token
if (!function_exists('csrf_token')) {
    /**
     * Get the CSRF token value.
     * @return string
     * @throws \RuntimeException
     */
    function csrf_token() {
        $session = app()->getContainer()->get(SessionInterface::class);
        if (isset($session)) {
            $token = $session->get('token');
            if (!$token) {
                $token = token(40);
                $session->set('token', $token);
            }
            return $token;
        }
        throw new RuntimeException('Application session store not set.');
    }
}

# token
if (!function_exists('token')) {
    /**
     * Generate a unique token string.
     * @param int $length
     * @return string
     */
    function token(int $length = 32): string {
        return bin2hex(random_bytes($length));
    }
}

# is_xhr_req
/** Enshure the request made with JS API:
* X-Requested-With: XmlHttpRequest
* Content-Type: application/json
*/
if (!function_exists('is_xhr_req')) {
    function is_xhr_req(ServerRequestInterface $req): bool {
        return (
            $req->getHeaderLine('X-Requested-With') === 'XMLHttpRequest'
                || preg_match('!application/json!', $req->getHeaderLine('Content-Type'))
        );
    }
}

# res_json
if (!function_exists('res_json')) {
    function res_json(ResponseInterface $res, array $data, int $status = null) {
        $res->getBody()->write(json_encode($data));
        if ($status) {
            return res_json_header($res->withStatus($status));
            #return $res->withHeader('Content-Type', 'application/json')->withStatus($status);
        } else {
            #return $res->withHeader('Content-Type', 'application/json');
            return res_json_header($res);
        }
    }
}
if (!function_exists('res_json_header')) {
    function res_json_header(ResponseInterface $res) {
        return $res->withHeader('Content-Type', 'application/json');
    }
}

# res_redirect
if (!function_exists('res_redirect')) {
    function res_redirect(ResponseInterface $res, string $url) {
        return $res->withStatus(301)->withHeader('Location', $url);
    }
}

# log
if (!function_exists('_log')) {
    function _log($msg, $title = '', $level = 'info') {
        $level = $level ? strtolower($level) : 'info';
        if (!is_scalar($msg)) {
          $msg = dump($msg);
        }
        $msg = $title === '' ? $msg : "[$title] " . $msg;
        switch ($level) {
            default:
                \App\Logger::info($msg);
                break;
            case 'notice':
            case 'note':
                \App\Logger::notice($msg);
                break;
            case 'debug':
                \App\Logger::debug($msg);
                break;
            case 'warning':
                \App\Logger::warning($msg);
                break;
            case 'error':
                \App\Logger::error($msg);
                break;
            case 'fatal':
                \App\Logger::fatal($msg);
                break;
        }
    }
}

# log
if (!function_exists('_log_truncate')) {
    function _log_truncate() {
        \App\Logger::truncate();
    }
}
