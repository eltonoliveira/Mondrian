<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Command;

use Symfony\Component\Console\Output\OutputInterface;
use Trismegiste\Mondrian\Graph\Graph;
use Trismegiste\Mondrian\Analysis\Cycle;

/**
 * CyclicCommand reduces a graph to its strongly connected components
 */
class CyclicCommand extends AbstractParse
{

    protected function getSubname()
    {
        return 'cycle';
    }

    protected function getFullDesc()
    {
        return parent::getFullDesc() . ' with cyclic coupling';
    }

    protected function processGraph(Graph $graph, OutputInterface $output)
    {
        return new Cycle($graph);
    }

}
