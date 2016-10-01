<?php
namespace Hrgruri\Dijkstra;
use Hrgruri\Dijkstra\Dijkstra;

class Dijkstra
{
    public static function create() : Graph
    {
        return new Graph();
    }

    public static function csv(string $filename)
    {
        if (!file_exists($filename)) {
            throw new \Exception("{$filename} was not found");
        }
        $graph  = Dijkstra::create();
        $file   = new \SplFileObject($filename);
        $file->setFlags(\SplFileObject::READ_CSV);
        foreach ($file as $line) {
            if (is_null($line[0])) {
                break;
            }
            $graph->add(trim($line[0]), trim($line[1]), trim($line[2]));
        }
        return $graph;
    }
}
