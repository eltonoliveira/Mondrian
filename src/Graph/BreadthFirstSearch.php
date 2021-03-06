<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Graph;

/**
 * BreadthFirstSearch is a decorator for digraph to find a directed path between
 * two vertices.
 *
 * Uses the breadth first search method : shortest path and avoid cycle
 *
 * Note : this is my own algorithm, I find it ugly and not DRY
 */
class BreadthFirstSearch extends Algorithm
{

    protected $stack = array();

    public function searchPath(Vertex $src, Vertex $dst)
    {
        $this->stack = array();
        $start = new \SplObjectStorage();
        $step = $this->graph->getEdgeIterator($src);
        foreach ($step as $succ) {
            $edge = $step->getInfo();
            if (!isset($edge->visited)) {
                $start[$edge] = null;
            }
        }
        $last = $this->recursivSearchPath($start, $dst);
        if (!is_null($last)) {
            array_unshift($this->stack, $last);
        }
        return $this->stack;
    }

    protected function recursivSearchPath(\SplObjectStorage $step, Vertex $dst)
    {
        $nextLevel = new \SplObjectStorage();
        foreach ($step as $e) {
            $e->visited = true;
            if ($e->getTarget() == $dst) {
                return $e;
            }
            $choice = $this->graph->getEdgeIterator($e->getTarget());
            foreach ($choice as $succ) {
                $edge = $choice->getInfo();
                if (!isset($edge->visited)) {
                    $nextLevel[$edge] = $e;
                }
            }
        }

        if (count($nextLevel)) {
            $ret = $this->recursivSearchPath($nextLevel, $dst);
            if (!is_null($ret)) {
                array_unshift($this->stack, $ret);
                return $nextLevel[$ret];
            }
        }
    }

    /**
     * Reset visited state of edges
     */
    protected function resetVisited()
    {
        foreach ($this->getEdgeSet() as $e) {
            unset($e->visited);
        }
    }

}
