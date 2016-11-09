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
        $stylesheets = collect($crawler->filter('link[rel=stylesheet]')->extract('href'));
        $files = $this->files;
        $styles = $stylesheets->map(function ($stylesheet) use ($files) {
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
