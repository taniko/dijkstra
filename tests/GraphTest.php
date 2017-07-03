<?php
namespace Test;

use Taniko\Dijkstra\Graph;

class GraphTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test
     */
    public function loadCsv()
    {
        $graph = Graph::loadCsv(__DIR__.'/data/graph.csv');
        $route = $graph->search('s', 't');
        $cost  = $graph->cost($route);
        $this->assertEquals(['s', 'b', 'c', 'd', 't'], $route);
        $this->assertEquals(6, $cost);
    }

    public function testAdd()
    {
        $graph = new Graph();
        $graph->add('a', 'b', 1);
        $nodes = $graph->getNodes();
        $this->assertEquals(1, $nodes['a']['b']);
        $this->assertEquals(1, $nodes['b']['a']);
    }

    public function testCost()
    {
        $graph = new Graph();
        $graph->add('a', 'b', 1)
            ->add('b', 'c', 2)
            ->add('c', 'd', 3);
        $this->assertEquals(6, $graph->cost(['a', 'b', 'c', 'd']));
    }

    public function testCostCatchException()
    {
        $flag  = false;
        $graph = new Graph();
        $graph->add('a', 'b', 1)
            ->add('b', 'c', 2)
            ->add('c', 'd', 3);
        try {
            $graph->cost(['a', 'b', 'd']);
        } catch (\UnexpectedValueException $e) {
            $flag = true;
        } finally {
            $this->assertTrue($flag, 'not catched Exception');
        }
    }

    public function testSearch()
    {
        $graph = new Graph();
        $graph
            ->add('s', 'a', 1)
            ->add('s', 'b', 2)
            ->add('a', 'b', 2)
            ->add('a', 'c', 4)
            ->add('b', 'c', 2)
            ->add('b', 'd', 5)
            ->add('c', 'd', 1)
            ->add('c', 't', 3)
            ->add('d', 't', 1);
        $route = $graph->search('s', 't');
        $cost  = $graph->cost($route);
        $this->assertEquals(['s', 'b', 'c', 'd', 't'], $route);
        $this->assertEquals(6, $cost);
    }

    public function testSearchCatchException()
    {
        $flag  = false;
        $graph = new Graph();
        $graph
            ->add('a', 'b', 1)
            ->add('b', 'c', 2)
            ->add('c', 'd', 3)
            ->add('e', 'f', 2);
        try {
            $graph->search('a', 'f');
        } catch (\UnexpectedValueException $e) {
            $flag = true;
        } finally {
            $this->assertTrue($flag, 'not catched Exception');
        }
    }
}
