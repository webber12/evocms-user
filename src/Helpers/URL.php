<?php
namespace EvolutionCMS\EvoUser\Helpers;

class URL
{
    public static function makeUrl(string $page = '', string $alias = '', string $args = '', string $scheme = ''): string
    {
        if (empty($page)) {
            return '';
        }

        if (is_numeric($page)) {
            // число это id документа, значит вызвать makeUrl из движка
            return UrlProcessor::makeUrl((int) $page, $alias, $args, $scheme);
        }

        if (!\Route::has($page)) {
            // нет в роутах, значит просто адрес
            return $page;
        }

        // есть в именованных роутах
        if ($args !== '') {
            // add ? or & to $args if missing
            $args = ltrim($args, '?&');
            $args = "?{$args}";
        }

        return route($page) . $args;
    }
}
