<?php

namespace Petecoop\LaravelInky;

use Illuminate\View\Engines\CompilerEngine;
use Illuminate\View\Compilers\CompilerInterface;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\DomCrawler\Crawler;
use TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;

class InkyCompilerEngine extends CompilerEngine
{
    protected $files;

    public function __construct(CompilerInterface $compiler, Filesystem $files)
    {
        parent::__construct($compiler);
        $this->files = $files;
    }

    public function get($path, array $data = [])
    {
        $results = parent::get($path, $data);

        $crawler = new Crawler($results);
        $stylesheets = $crawler->filter('link[rel=stylesheet]');

        // collect hrefs
        $stylesheetsHrefs = collect($stylesheets->extract('href'));

        // remove links
        $stylesheets->each(function (Crawler $crawler) {;
            foreach ($crawler as $node) {
                $node->parentNode->removeChild($node);
            }
        });

        $results = $crawler->html();

        // get the styles
        $files = $this->files;
        $styles = $stylesheetsHrefs->map(function ($stylesheet) use ($files) {
            $path = resource_path('assets/css/' . $stylesheet);
            return $files->get($path);
        })->implode("\n\n");

        $inliner = new CssToInlineStyles();
        return $inliner->convert($results, $styles);
    }
    
    public function getFiles()
    {
        return $this->files;
    }
}
