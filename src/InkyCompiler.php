<?php

namespace Rsvpify\LaravelInky;

use IncentFit\Inky\Inky;
use Illuminate\Filesystem\Filesystem;
use Illuminate\View\Compilers\Compiler;
use Illuminate\View\Compilers\CompilerInterface;

class InkyCompiler extends Compiler implements CompilerInterface
{
    protected $inky;

    protected $blade;

    protected $path;

    public function __construct(Compiler $blade, Filesystem $files, $cachePath)
    {
        parent::__construct($files, $cachePath);

        $this->blade = $blade;
        $this->inky = new Inky;
    }

    public function compile($path = null)
    {
        if ($path) {
            $this->setPath($path);
        }

        if (! is_null($this->cachePath)) {
            $contents = $this->compileString($this->files->get($this->getPath()));

            $this->files->put($this->getCompiledPath($this->getPath()), $contents);
        }
    }

    public function getPath()
    {
        return $this->path;
    }

    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    public function compileString($value)
    {
        return $this->blade->compileString($this->inky->releaseTheKraken($value));
    }

    public function getFiles()
    {
        return $this->files;
    }

    public function getBlade()
    {
        return $this->blade;
    }
}
