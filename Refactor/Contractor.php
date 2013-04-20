<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Refactor;

use Trismegiste\Mondrian\Visitor;

/**
 * Contractor is ...
 *
 */
class Contractor
{

    /**
     * 
     *  
     * @param string[] $iter list of absolute path to files to parse
     * 
     */
    public function parse($iter)
    {
        $parser = new \PHPParser_Parser(new \PHPParser_Lexer());
        $prettyPrinter = new \PHPParser_PrettyPrinter_Default();
        $context = new Refactored();
        // passes
        $pass[0] = new Visitor\NewContractCollector($context);
        $pass[1] = new Visitor\ParamRefactor($context);
        $pass[2] = new Visitor\InterfaceExtractor($context);

        // for memory concerns, I'll re-parse files on each pass
        // (slower but lighter) and enriching the Context
        foreach ($pass as $collector) {

            $traverser = new \PHPParser_NodeTraverser();
            $traverser->addVisitor($collector);

            foreach ($iter as $fch) {
                $code = file_get_contents($fch);
                $stmts = $parser->parse($code);
                $traverser->traverse($stmts);
                if ($collector->isModified()) {
                    copy($fch, $fch . '.bak');
                    file_put_contents($fch, "<?php\n\n" . $prettyPrinter->prettyPrint($stmts));
                }
                if ($collector->hasGenerated()) {
                    $lst = $collector->getGenerated();
                    foreach ($lst as $name => $interf) {
                        $interfFch = dirname($fch) . DIRECTORY_SEPARATOR . $name . '.php';
                        file_put_contents($interfFch, "<?php\n\n" . $prettyPrinter->prettyPrint($interf));
                    }
                }
            }
        }
    }

}