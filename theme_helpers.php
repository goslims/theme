<?php
use SLiMS\Theme\View;

if (!function_exists('extedLayout')) {
    function extendLayout(array $data = [], string $layoutPath = '')
    {
        return View::extendLayout(opsionalLayout: $layoutPath, data: $data);
    }
}

if (!function_exists('section')) {
    function section(string $name, string|Closure $content)
    {
        return View::registerSection($name, $content);
    }
}

if (!function_exists('_include')) {
    function _include(string $name)
    {
        return View::render($name);
    }
}

if (!function_exists('aquaUrl')) {
    function aquaUrl(string $url)
    {
        return AQUA_WEB_BASE . 'themes/aqua/' . $url;
    }
}

if (!function_exists('_yeild')) {
    function _yield(string $nameOfSection, string $default = '')
    {
        return iterator_to_array(View::yield($nameOfSection, $default))[0]??$default;
    }
}
