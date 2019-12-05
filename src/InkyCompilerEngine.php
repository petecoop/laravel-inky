<?php

namespace Rsvpify\LaravelInky;

use Illuminate\Support\Str;
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
        $styles = $stylesheetsHrefs->map(function ($path) {

            //  if this appears to be a local asset, get it locally
            if (Str::startsWith($path, asset(''))) {
                $path = str_replace(asset(''), public_path('/'), $path);
            }

            // With the above logic the foundation css file is
            // going to be expected to be at http://rsvpify.com/public/$stylesheet
            return $this->files->get($path);
        })->implode("\n\n");

        $inliner = new CssToInlineStyles();
        return $inliner->convert($results, $styles);
    }
    
    public function getFiles()
    {
        return $this->files;
    }
}