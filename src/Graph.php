<?php
namespace Taniko\Dijkstra;

class Graph
{
    private $nodes      = [];
    private $total_cost = 0;

    public function __construct()
    {
        $this->nodes      = [];
        $this->total_cost = 0;
    }

    public static function create() : Graph
    {
        return new Graph();
    }

    public static function loadCsv(string $filename) : Graph
    {
        $graph  = Graph::create();
        if (file_exists($filename)) {
            $file   = new \SplFileObject($filename);
            $file->setFlags(\SplFileObject::READ_CSV);
            foreach ($file as $line) {
                if (is_null($line[0])) {
                    break;
                }
                $graph->add(trim($line[0]), trim($line[1]), trim($line[2]));
            }
        }
        return $graph;
    }

    /**
     * add edge
     * @param string $a        Node A
     * @param string $b        Node B
     * @param int|float $distance distance
     * @return Taniko\Dijkstra\Graph
     */
    public function add(string $a, string $b, $distance) : Graph
    {
        if (!is_numeric($distance)) {
            return false;
        }
        $distance = floatval($distance);
        $this->total_cost   += $distance;
        $this->nodes[$a][$b] = $distance;
        $this->nodes[$b][$a] = $distance;
        return $this;
    }

    /**
     * remove edge
     * @param  string $a Node A
     * @param  string $b Node B
     * @return Taniko\Dijkstra\Graph
     */
    public function remove(string $a, string $b) : Graph
    {
        if (isset($this->nodes[$a][$b])) {
            unset($this->nodes[$a][$b]);
            unset($this->nodes[$b][$a]);
        }
        return $this;
    }

    /**
     * get node list
     * @return array node list
     */
    public function getNodes() : array
    {
        return $this->nodes;
    }

    /**
     * calculate cost of route
     * @param  array  $route
     * @return float
     */
    public function cost(array $route) : float
    {
        $result = 0;
        if (count($route) > 0) {
            $num = count($route) -1;
            for ($i = 0; $i < $num; $i++) {
                if (!isset($this->nodes[$route[$i]][$route[$i+1]])) {
                    throw new \UnexpectedValueException("edge from {$route[$i]} to {$route[$i+1]} does not exist");
                }
                $result += $this->nodes[$route[$i]][$route[$i+1]];
            }
        }
        return floatval($result);
    }

    /**
     * search route
     * @param  string $from node name
     * @param  string $to   node name
     * @return array node list
     */
    public function search(string $from, string $to) : array
    {
        if (!isset($this->nodes[$from]) || !isset($this->nodes[$to])) {
            throw new \UnexpectedValueException("node {$from} or node {$to} does not exist");
        }
        // initialization
        $nodes      = array_keys($this->nodes);
        $distance   = [];
        $parent     = [];
        $visit      = [];

        foreach ($this->nodes as $key => $value) {
            $distance[$key] = $this->total_cost +1;
        }
        $distance[$from] = 0;

        // set start node
        foreach ($this->nodes as $key => $val) {
            $parent[$key] = null;
        }
        foreach ($this->nodes[$from] as $key => $val) {
            $distance[$key] = $this->nodes[$from][$key];
            $parent[$key]   = $from;
        }
        $visit[] = $from;

        // search shortest route
        for (;;) {
            $min_distance   =   $this->total_cost + 1;
            $node           =   null;
            foreach (array_diff($nodes, $visit) as $key) {
                if ($distance[$key] < $min_distance) {
                    $node           =   $key;
                    $min_distance   =   $distance[$key];
                }
            }
            if ($node === $to) {
                break;
            } elseif (is_null($node)) {
                throw new \UnexpectedValueException("path from {$from} to {$to} does not exist");
            }
            foreach (array_diff(array_keys($this->nodes[$node]), $visit) as $key) {
                if ($distance[$key] > $distance[$node] + $this->nodes[$node][$key]) {
                    $distance[$key] = $distance[$node] + $this->nodes[$node][$key];
                    $parent[$key]   = $node;
                }
            }
            $visit[] = $node;
        }

        $result = [];
        for (;;) {
            $result[] = $node;
            if ($node === $from) {
                break;
            }
            $node = $parent[$node];
        }
        return array_reverse($result);
    }
}
