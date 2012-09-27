<?php
namespace Heartsentwined\Assetwig\Twig\Extension\Render;

use Heartsentwined\Assetwig\Twig\Extension\Render\RenderNode;
use Twig_TokenParser;
use Twig_Token as Token;
use Twig_Node_Expression_Array as ExpressionArray;

class TokenParser extends Twig_TokenParser
{
    public function parse(Token $token)
    {
        $expr = $this->parser->getExpressionParser()->parseExpression();
        // attributes
        if ($this->parser->getStream()->test(Token::NAME_TYPE, 'with')) {
            $this->parser->getStream()->next();
            $attributes = $this->parser->getExpressionParser()->parseExpression();
        } else {
            $attributes = new ExpressionArray(array(), $token->getLine());
        }
        $options = new ExpressionArray(array(), $token->getLine());
        $this->parser->getStream()->expect(Token::BLOCK_END_TYPE);

        return new RenderNode(array(
            'expr'          => $expr,
            'attributes'    => $attributes,
            'options'       => $options,
        ), array(), $token->getLine(), $this->getTag());
    }

    public function getTag()
    {
        return 'render';
    }
}
