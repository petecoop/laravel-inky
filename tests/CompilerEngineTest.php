<?php

namespace Petecoop\Tests\LaravelInky;

use Petecoop\LaravelInky\InkyCompilerEngine;
use Illuminate\View\Compilers\CompilerInterface;
use Illuminate\Filesystem\Filesystem;
use Mockery;

class CompilerEngineTest extends AbstractTestCase
{
    public function testRender()
    {
        $engine = $this->getEngine();
        $path = __DIR__.'/stubs/test';
        
        $engine->getCompiler()->shouldReceive('isExpired')->once()
            ->with($path)->andReturn(false);
            
        $engine->getCompiler()->shouldReceive('getCompiledPath')->once()
            ->with($path)->andReturn($path);
        
        $this->assertContains('<p>testy</p>', $engine->get($path));
    }
    
    public function testCssInline()
    {
        $engine = $this->getEngine();
        $path = __DIR__.'/stubs/inline';
        
        $engine->getCompiler()->shouldReceive('isExpired')->once()
            ->with($path)->andReturn(false);
            
        $engine->getCompiler()->shouldReceive('getCompiledPath')->once()
            ->with($path)->andReturn($path);
        
        $engine->getFiles()->shouldReceive('get')->once()
            ->with(resource_path('assets/css/test'))
            ->andReturn('body {color:red;}');
            
        $this->assertContains('<body style="color: red;">', $engine->get($path));
    }
    
    public function testStyleInline()
    {
        $engine = $this->getEngine();
        $path = __DIR__.'/stubs/inlinestyle';
        
        $engine->getCompiler()->shouldReceive('isExpired')->once()
            ->with($path)->andReturn(false);
            
        $engine->getCompiler()->shouldReceive('getCompiledPath')->once()
            ->with($path)->andReturn($path);
            
        $this->assertContains('<body style="color: blue;">', $engine->get($path));
    }
    
    protected function getEngine()
    {
        $compiler = Mockery::mock(CompilerInterface::class);
        $files = Mockery::mock(Filesystem::class);
        
        return new InkyCompilerEngine($compiler, $files);
    }
    
}