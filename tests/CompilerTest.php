<?php

namespace Rsvpify\Tests\LaravelInky;

use Mockery;
use Illuminate\Filesystem\Filesystem;
use Rsvpify\LaravelInky\InkyCompiler;
use Illuminate\View\Compilers\BladeCompiler;

class CompilerTest extends AbstractTestCase
{
    public function testCompile()
    {
        $compiler = $this->getCompiler();

        $compiler->getFiles()->shouldReceive('get')->once()
            ->with('path')->andReturn('html');

        $compiler->getBlade()->shouldReceive('compileString')->once()
            ->with('html')->andReturn('html');

        $compiler->getFiles()->shouldReceive('put')->once()
            ->with(__DIR__ . '/e13dbc54cb72a29f66053c494f2c456242d1fefa.php', 'html');

        $this->assertNull($compiler->compile('path'));
    }

    public function testInkyRow()
    {
        $compiler = $this->getCompiler();

        $compiler->getBlade()->shouldReceive('compileString')->once()
            ->with('<table class="row"><tbody><tr></tr></tbody></table>')
            ->andReturn('row');

        $this->assertEquals('row', $compiler->compileString('<row></row>'));
    }

    public function testInkyColumns()
    {
        $compiler = $this->getCompiler();

        $compiler->getBlade()->shouldReceive('compileString')->once()
            ->with('<th class="columns small-12 large-12 last first"><table><tr><th></th></tr></table></th>')
            ->andReturn('columns');

        $this->assertEquals('columns', $compiler->compileString('<columns></columns>'));

        // with attributes
        $compiler->getBlade()->shouldReceive('compileString')->once()
            ->with('<th class="columns small-12 large-6 last first"><table><tr><th></th></tr></table></th>')
            ->andReturn('columns');

        $this->assertEquals('columns', $compiler->compileString('<columns small="12" large="6"></columns>'));
    }

    public function testInkyContainer()
    {
        $compiler = $this->getCompiler();

        $compiler->getBlade()->shouldReceive('compileString')->once()
            ->with('<table class="container" align="center"><tbody><tr><td></td></tr></tbody></table>')
            ->andReturn('container');

        $this->assertEquals('container', $compiler->compileString('<container></container>'));
    }

    protected function getCompiler()
    {
        $blade = Mockery::mock(BladeCompiler::class);
        $files = Mockery::mock(Filesystem::class);
        $cachePath = __DIR__;

        return new InkyCompiler($blade, $files, $cachePath);
    }
}
