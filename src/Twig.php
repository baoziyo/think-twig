<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2015 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: yunwuxin <448901948@qq.com>　
// +----------------------------------------------------------------------

namespace think\view\driver;

use DirectoryIterator;
use think\Loader;
use Twig\Environment;
use Twig\Loader\ArrayLoader;
use Twig\Loader\FilesystemLoader;
use Twig\Loader\LoaderInterface;
use Twig\RuntimeLoader\FactoryRuntimeLoader;
use Twig\TwigFilter;
use Twig\TwigFunction;
use yunwuxin\twig\Extension;
use twig\Filter;
use twig\Functions;

class Twig
{
    // 模板引擎参数
    protected $config = [
        'view_base' => '',
        // 模板起始路径
        'view_path' => '',
        // 模板文件后缀
        'view_suffix' => 'twig',
        // 模板文件名分隔符
        'view_depr' => '/',
        'cache_path' => '',
        'strict_variables' => true,
        'auto_add_function' => false,
        'base_template_class' => 'Twig_Template',
        'functions' => [],
        'filters' => [],
        'globals' => [],
        'runtime' => [],
    ];

    public function __construct($config = [])
    {
        $this->config($config);

        if (empty($this->config['cache_path'])) {
            $this->config['cache_path'] = app()->getRuntimePath() . 'temp/';
        }

        if (empty($this->config['view_path'])) {
            $this->config['view_path'] = app()->getModulePath() . 'view/';
        }
    }

    /**
     * 模板引擎配置项
     *
     * @access public
     * @param array|string $name
     * @param mixed $value
     */
    public function config($name, $value = null)
    {
        if (is_array($name)) {
            $this->config = array_merge($this->config, $name);
        } else {
            $this->config[$name] = $value;
        }
    }

    protected function getTwigConfig()
    {
        return [
            'debug' => app()->isDebug(),
            'auto_reload' => app()->isDebug(),
            'cache' => $this->config['cache_path'],
            'strict_variables' => $this->config['strict_variables'],
            'base_template_class' => $this->config['base_template_class'],
        ];
    }

    protected function addFunctions(Environment $twig)
    {
        $twig->registerUndefinedFunctionCallback(function ($name) {
            if (function_exists($name)) {
                return new TwigFunction($name, $name);
            }

            return false;
        });
    }

    protected function getTwig(LoaderInterface $loader)
    {
        $twig = new Environment($loader, $this->getTwigConfig());

        if ($this->config['auto_add_function']) {
            $this->addFunctions($twig);
        }

        if (!empty($this->config['globals'])) {
            foreach ($this->config['globals'] as $name => $global) {
                $twig->addGlobal($name, $global);
            }
        }

        if (class_exists('twig\Functions')) {
            $class = new Functions();
            $l = get_class_methods($class);
            foreach ($l as $m) $twig->addFunction(new TwigFunction($m, [$class, $m]));
        }

        if (class_exists('twig\Filter')) {
            $class = new Filter();
            $l = get_class_methods($class);
            foreach ($l as $m) $twig->addFilter(new TwigFilter($m, [$class, $m]));
        }

        if (!empty($this->config['runtime'])) {
            $twig->addRuntimeLoader(new FactoryRuntimeLoader($this->config['runtime']));
        }

        $twig->addExtension(new Extension());

        return $twig;
    }

    public function fetch($template, $data = [], $config = [])
    {
        if ($config) {
            $this->config($config);
        }

        $loader = new FilesystemLoader($this->config['view_path']);

        if (config('app.multi_module')) {
            $modules = $this->getModules();
            foreach ($modules as $module) {
                if ($this->config['view_base']) {
                    $view_dir = $this->config['view_base'] . $module;
                } else {
                    $view_dir = app()->getAppPath() . $module . DIRECTORY_SEPARATOR . 'view';
                }
                if (is_dir($view_dir)) {
                    $loader->addPath($view_dir, $module);
                }
            }
        }

        $twig = $this->getTwig($loader);

        $template = $this->parseTemplate($template);

        $twig->display($template, $data);
    }

    public function display($template, $data = [], $config = [])
    {
        if ($config) {
            $this->config($config);
        }
        $key = md5($template);
        $loader = new ArrayLoader([$key => $template]);

        $twig = $this->getTwig($loader);

        $twig->display($key, $data);
    }

    private function parseTemplate($template)
    {
        $request = request();

        $depr = $this->config['view_depr'];

        $controller = Loader::parseName($request->controller());

        if ($controller && 0 !== strpos($template, '/')) {
            if ('' == $template) {
                // 如果模板文件名为空 按照默认规则定位
                $template = str_replace('.', DIRECTORY_SEPARATOR, $controller) . $depr . $request->action();
            } elseif (false === strpos($template, '/')) {
                $template = str_replace('.', DIRECTORY_SEPARATOR, $controller) . $depr . $template;
            }
        }

        return str_replace('/', $depr, $template) . '.' . $this->config['view_suffix'];
    }

    private function getModules()
    {
        $modules = [];
        $oDir = new DirectoryIterator(app()->getAppPath());
        $deny_modules = config('app.deny_module_list');
        foreach ($oDir as $file) {
            if ($file->isDir() && !$file->isDot() && !in_array($file->getFilename(), $deny_modules)) {
                $modules[] = $file->getFilename();
            }
        }
        return $modules;
    }
}
