<?php

namespace Christhompsontldr\LaravelInky;

use Illuminate\View\Engines\CompilerEngine;
use Illuminate\View\Compilers\CompilerInterface;
use Symfony\Component\DomCrawler\Crawler;
use TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;

class InkyCompilerEngine extends CompilerEngine
{

    public function get($path, array $data = [])
    {
        $results = parent::get($path, $data);

        $crawler = new Crawler();
        $crawler->addHtmlContent($results);

        $stylesheets = $crawler->filter('link[rel=stylesheet]');

        // collect hrefs
        $stylesheetsHrefs = collect($stylesheets->extract('href'));

        // remove links
        $stylesheets->each(function (Crawler $crawler) {
            foreach ($crawler as $node) {
                $node->parentNode->removeChild($node);
            }
        });

        $results = $crawler->html();

        // get the styles
        $styles = $stylesheetsHrefs->map(function ($stylesheet) {
            //  if this appears to be a local asset, get it locally
            if (starts_with($stylesheet, asset(''))) {
                $stylesheet = str_replace(asset(''), public_path('/'), $stylesheet);
            }

            return file_get_contents($stylesheet);
        })->implode("\n\n");

        $inliner = new CssToInlineStyles();
        return $inliner->convert($results, $styles);
    }
}
