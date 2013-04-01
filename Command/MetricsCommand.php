<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Command;

use Symfony\Component\Console\Output\OutputInterface;
use Trismegiste\Mondrian\Graph\Graph;
use Trismegiste\Mondrian\Analysis\CodeMetrics;

/**
 * MetricsCommand transforms a bunch of php files into a digraph
 * and make some code metrics about it
 *
 */
class MetricsCommand extends AbstractParse
{

    protected function getSubname()
    {
        return 'metrics';
    }

    protected function getFullDesc()
    {
        return 'Code metrics of source code';
    }

    protected function processGraph(Graph $graph, OutputInterface $output)
    {
        $stat = new CodeMetrics($graph);
        $metrics = $stat->getCardinal();

        $output->writeln('Classes: ' . $metrics['Class']);
        $output->writeln('Interfaces: ' . $metrics['Interface']);
        $output->writeln('Methods: ' . $metrics['Method']);
        $output->writeln('  - declared in classes:    ' . $metrics['MethodDeclaration']['Class']);
        $output->writeln('  - declared in interfaces: ' . $metrics['MethodDeclaration']['Interface']);
        $output->writeln('Implemented: ' . $metrics['Impl']);

        return null;
    }

}