<?php

namespace Rsvpify\Tests\LaravelInky;

use Mockery;
use Illuminate\Filesystem\Filesystem;
use Rsvpify\LaravelInky\InkyCompilerEngine;
use Illuminate\View\Compilers\CompilerInterface;

class CompilerEngineTest extends AbstractTestCase
{
    public function testRender()
    {
        $engine = $this->getEngine();
        $path = __DIR__ . '/stubs/test';

        $engine->getCompiler()->shouldReceive('isExpired')->once()
            ->with($path)->andReturn(false);

        $engine->getCompiler()->shouldReceive('getCompiledPath')->once()
            ->with($path)->andReturn($path);

        $this->assertStringContainsString('<p>testy</p>', $engine->get($path));
    }

    public function testCssInline()
    {
        config(
            [
                'inky.stylesheets' => [
                    'testFoundationFile',
                ],
            ]
        );

        $engine = $this->getEngine();
        $path = __DIR__ . '/stubs/inline';

        $engine->getCompiler()->shouldReceive('isExpired')->once()
            ->with($path)->andReturn(false);

        $engine->getCompiler()->shouldReceive('getCompiledPath')->once()
            ->with($path)->andReturn($path);

        $engine->getFiles()->shouldReceive('get')->once()
            ->with('testFoundationFile')
            ->andReturn('body {color:red;}');

        $html = $engine->get($path);

        $this->assertStringContainsString('<body style="color: red;">', $html);
        $this->assertStringNotContainsString('<link rel="stylesheet"', $html);
    }

    public function testStyleInline()
    {
        $engine = $this->getEngine();
        $path = __DIR__ . '/stubs/inlinestyle';

        $engine->getCompiler()->shouldReceive('isExpired')->once()
            ->with($path)->andReturn(false);

        $engine->getCompiler()->shouldReceive('getCompiledPath')->once()
            ->with($path)->andReturn($path);

        $html = $engine->get($path);

        $this->assertStringContainsString('<body style="color: blue;">', $html);
        $this->assertStringNotContainsString('<script', $html);
    }

    public function testKeepsDisplayNone()
    {
        $engine = $this->getEngine();
        $path = __DIR__ . '/stubs/displaynone';

        $engine->getCompiler()->shouldReceive('isExpired')->once()
            ->with($path)->andReturn(false);

        $engine->getCompiler()->shouldReceive('getCompiledPath')->once()
            ->with($path)->andReturn($path);

        $html = $engine->get($path);

        $this->assertStringContainsString('<p style="display: none;">testy</p>', $html);
    }

    protected function getEngine()
    {
        $compiler = Mockery::mock(CompilerInterface::class);
        $filesystem = Mockery::mock(Filesystem::class);

        return new InkyCompilerEngine($compiler, $filesystem);
    }
}
