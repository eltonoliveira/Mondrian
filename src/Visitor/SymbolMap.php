<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Visitor;

use Trismegiste\Mondrian\Transform\ReflectionContext;

/**
 * SymbolMap is a class to collect list of class/interface/method name
 *
 * It fills the Context with symbols
 */
class SymbolMap extends PublicCollector
{

    private $context;

    /**
     * Build the collector
     *
     * @param Context $ctx
     */
    public function __construct(ReflectionContext $ctx)
    {
        $this->context = $ctx;
    }

    /**
     * {@inheritDoc}
     */
    public function enterNode(\PHPParser_Node $node)
    {
        parent::enterNode($node);

        switch ($node->getType()) {

            case 'Stmt_TraitUse' :
                $this->importSignatureTrait($node);
                break;
        }
    }

    /**
     * {@inheritDoc}
     */
    protected function enterClassNode(\PHPParser_Node_Stmt_Class $node)
    {
        $this->context->initSymbol($this->currentClass, ReflectionContext::SYMBOL_CLASS);
        // extends
        if (!is_null($node->extends)) {
            $name = (string) $this->resolveClassName($node->extends);
            $this->context->initSymbol($name, ReflectionContext::SYMBOL_CLASS);
            $this->context->pushParentClass($this->currentClass, $name);
        }
        // implements
        foreach ($node->implements as $parent) {
            $name = (string) $this->resolveClassName($parent);
            $this->context->initSymbol($name, ReflectionContext::SYMBOL_INTERFACE);
            $this->context->pushParentClass($this->currentClass, $name);
        }
    }

    /**
     * {@inheritDoc}
     */
    protected function enterInterfaceNode(\PHPParser_Node_Stmt_Interface $node)
    {
        $this->context->initSymbol($this->currentClass, ReflectionContext::SYMBOL_INTERFACE);
        // extends
        foreach ($node->extends as $interf) {
            $name = (string) $this->resolveClassName($interf);
            $this->context->initSymbol($name, ReflectionContext::SYMBOL_INTERFACE);
            $this->context->pushParentClass($this->currentClass, $name);
        }
    }

    /**
     * {@inheritDoc}
     */
    protected function enterPublicMethodNode(\PHPParser_Node_Stmt_ClassMethod $node)
    {
        $this->context->addMethodToClass($this->currentClass, $node->name);
    }

    /**
     * Compiling the pass : resolving symbols in the context
     */
    public function afterTraverse(array $dummy)
    {
        $this->context->resolveSymbol();
    }

    protected function enterTraitNode(\PHPParser_Node_Stmt_Trait $node)
    {
        $this->context->initSymbol($this->currentClass, ReflectionContext::SYMBOL_TRAIT);
    }

    protected function importSignatureTrait(\PHPParser_Node_Stmt_TraitUse $node)
    {
        // @todo do not forget aliases
        foreach ($node->traits as $import) {
            $name = (string) $this->resolveClassName($import);
            $this->context->initSymbol($name, ReflectionContext::SYMBOL_TRAIT);
            $this->context->pushUseTrait($this->currentClass, $name);
        }
    }

}