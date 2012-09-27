<?php
namespace Heartsentwined\Assetwig\Twig\Extension\Render;

use Twig_Node as Node;
use Twig_Compiler as Compiler;

class RenderNode extends Node
{
    public function compile(Compiler $compiler)
    {
        $compiler
            ->addDebugInfo($this)
            ->write('echo $this->getEnvironment()->getExtension("Assetwig")->renderAction(')
            ->subcompile($this->getNode('expr'))
            ->raw(', ')
            ->subcompile($this->getNode('attributes'))
            ->raw(', ')
            ->subcompile($this->getNode('options'))
            ->raw(");\n");
    }
}
