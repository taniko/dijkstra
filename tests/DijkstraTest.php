<?php
use Hrgruri\Dijkstra\Dijkstra;
use Hrgruri\Dijkstra\Graph;

class DijkstraTest extends \PHPUnit\Framework\TestCase
{
    public function testCreate()
    {
        $instance = Dijkstra::create();
        $this->assertInstanceOf(Graph::class, $instance);
    }

    public function testCSV()
    {
        $graph = Dijkstra::csv(__DIR__ .'/data/graph.csv');
        $this->assertInstanceOf(Graph::class, $graph);
        $route = $graph->search('s', 't');
        $cost  = $graph->cost($route);
        $this->assertEquals(['s', 'b', 'c', 'd', 't'], $route);
        $this->assertEquals(6, $cost);
    }
}
