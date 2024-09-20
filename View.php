<?php
namespace SLiMS\Theme;

use Closure;

class View
{
    protected static ?View $instance = null;
    protected ?Loader $loader = null;
    protected ?object $info = null;

    public static function getInstance()
    {
        if (self::$instance === null) self::$instance = new View;
        return self::$instance;
    }

    public static function setBasePath(string $pathToTemplate)
    {
        self::getInstance()->loader = new Loader($pathToTemplate);
        self::getInstance()->info = new \stdClass;
        self::getInstance()->info->base = $pathToTemplate . DS;
        self::getInstance()->info->layouts = [
            'path' => $pathToTemplate . '/layouts/',
            'default' => 'main',
        ];

        self::getInstance()->info->sections = [
            'path' => $pathToTemplate . '/sections/'
        ];

        self::getInstance()->info->components = [
            'path' => $pathToTemplate . '/components/'
        ];

        return self::getInstance();
    }

    public static function extendLayout(array $data = [], string $opsionalLayout = '') {
        if (!empty($opsionalLayout))self::getInstance()->info->layouts['default'] = $opsionalLayout;
        
        self::getInstance()->info->layouts['properties'] = $data;
    }

    public static function registerSection(string $name, string|Closure $content) {
        self::getInstance()->info->sections['items'][$name] =  $content;
    }

    public static function yield(string $name, string $default = ''): \Generator {
        $found = false;
        foreach (self::getInstance()->info->sections['items']??[] as $key => $value) {
            if ($key === $name) {
                $found = true;
                if (is_callable($value)) yield $value();
                else yield $value;
            }
        }

        foreach (self::getInstance()->info->sections['components']??[] as $key => $value) {
            if ($key === $name) {
                $found = true;
                if (is_callable($value)) yield $value();
                else yield $value;
            }
        }

        if ($found === false) yield $default;
    }

    public static function render(string $viewNameOrPath, array $data = [])
    {
        self::getInstance()->loader->loadFile($viewNameOrPath, Blade::removeXss($data));
        return self::getInstance();
    }

    private function pathResolver(string $path)
    {
        return str_replace('.', DS, $path);
    }

    public function __toString()
    {
        $content = self::getInstance()->loader->getBuffer();

        if (isset(self::getInstance()->info->layouts['properties'])) {
            $layouts = self::getInstance()->info->layouts;
            $viewNameOrPath = $layouts['path'] . $layouts['default'];
            $layouts['properties']['content'] = $content;

            self::getInstance()->loader->loadFile($viewNameOrPath, Blade::removeXss($layouts['properties']));
            return self::getInstance()->loader->getBuffer();
        }
        
        return $content;
    }
}
