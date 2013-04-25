<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Transform;

use Trismegiste\Mondrian\Transform\Context;
use Trismegiste\Mondrian\Graph\Vertex;

/**
 * ContextTest tests for vertex mapping Context
 */
class VertexContextTest extends \PHPUnit_Framework_TestCase
{

    protected $context;

    protected function setUp()
    {
        $this->context = new Context();
    }

    public function getVertexMock()
    {
        foreach (array('class', 'interface', 'impl', 'method', 'param') as $pool) {
            $v[] = array($pool, $this->getMockBuilder('Trismegiste\Mondrian\Graph\Vertex')
                        ->disableOriginalConstructor()
                        ->getMock());
        }

        return $v;
    }

    /**
     * @dataProvider getVertexMock
     */
    public function testEmpty($pool, Vertex $v)
    {
        $this->assertNull($this->context->findVertex($pool, 'Unknown'));
    }

    /**
     * @dataProvider getVertexMock
     */
    public function testFindVertex($pool, Vertex $v)
    {
        $this->context->indicesVertex($pool, 'idx', $v);
        $this->assertEquals($v, $this->context->findVertex($pool, 'idx'));
    }

    /**
     * @dataProvider getVertexMock
     */
    public function testExistsVertex($pool, Vertex $v)
    {
        $this->assertNull($this->context->findVertex($pool, 'idx'));
        $this->context->indicesVertex($pool, 'idx', $v);
        $this->assertEquals($v, $this->context->findVertex($pool, 'idx'));
    }

}