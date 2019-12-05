<?php

namespace Rsvpify\LaravelInky;

use Illuminate\Filesystem\Filesystem;
use Symfony\Component\DomCrawler\Crawler;
use Illuminate\View\Engines\CompilerEngine;
use Illuminate\View\Compilers\CompilerInterface;
use TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;

class InkyCompilerEngine extends CompilerEngine
{
    protected $files;

    public function __construct(CompilerInterface $compiler, Filesystem $files)
    {
        parent::__construct($compiler);
        $this->files = $files;
    }

    public function get($inkyFilePath, array $data = [])
    {
        // Compiles the inky template as if it were a regular blade file
        $html = parent::get($inkyFilePath, $data);

        // remove css stylesheet links from email's HTML
        $crawler = new Crawler();
        $crawler->addHtmlContent($html);
        $cssLinks = $crawler->filter('link[rel=stylesheet]');

        $cssLinks->each(function (Crawler $crawler) {
            foreach ($crawler as $node) {
                $node->parentNode->removeChild($node);
            }
        });

        $htmlWithoutLinks = $crawler->html();

        // This array of CSS files to be inlined in the email will be
        // provided via the user in the publishable config file
        $stylesheetsHrefs = collect(config('inky.stylesheets'));

        // Combine all stylesheets into 1 string of CSS
        $combinedStyles = $stylesheetsHrefs->map(function ($path) {
            // The publishable config file should have the stylesheets
            // referenced at public/$path but we want just the $path part of the URL here.
            $path = str_replace('public/', '', $path);

            return $this->files->get($path);
        })->implode("\n\n");

        $inliner = new CssToInlineStyles();

        return $inliner->convert($htmlWithoutLinks, $combinedStyles);
    }

    public function getFiles()
    {
        return $this->files;
    }
}
