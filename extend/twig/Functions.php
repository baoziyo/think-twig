<?php

namespace twig;

use think\facade\Url;

class Functions
{
    // 生成验证码图片
    public static function captcha_src($id = '')
    {
        return Url::build('/captcha' . ($id ? "/{$id}" : ''));
    }

    public static function asset($path)
    {
        return '/' . $path . '?' . config('parameters.version');
    }

    // 使用think方法
    public static function think($app, $type, $param = '')
    {
        return app($app)->$type($param);
        /*switch ($type) {
            case 'server':
                $parseStr = app('request')->server($param);
                break;
            case 'get':
                $parseStr = app('request')->get($param);
                break;
            case 'post':
                $parseStr = app('request')->post($param);
                break;
            case 'cookie':
                $parseStr = app('cookie')->get($param);
                break;
            case 'session':
                $parseStr = app('session')->get($param);
                break;
            case 'env':
                $parseStr = app('request')->env($param);
                break;
            case 'request':
                $parseStr = app('request')->request($param);
                break;
            case 'const':
                $parseStr = strtoupper($param);
                break;
            case 'lang':
                $parseStr = app('lang')->get($param);
                break;
            case 'config':
                $parseStr = app('config')->get($param);
                break;
            default:
                $parseStr = '\'\'';
                break;
        }
        return $parseStr;*/
    }

    // 使用dataTag
    public static function data($name, $parameters = [])
    {
        $getDataTag = new \dataTag\GetDataTag();
        return $getDataTag->gitDataTag($name, $parameters);
    }

    // 生成url
    public static function url($link, $vars = '')
    {
        return url($link, $vars);
    }
}
