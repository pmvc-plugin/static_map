<?php
namespace PMVC\PlugIn\static_map;

use PHPUnit_Framework_TestCase;

\PMVC\Load::plug([], ['../']);

class StaticMapTest extends PHPUnit_Framework_TestCase
{
    private $_plug = 'static_map';
    function testPlugin()
    {
        ob_start();
        print_r(\PMVC\plug($this->_plug));
        $output = ob_get_contents();
        ob_end_clean();
        $this->assertContains($this->_plug,$output);
    }

    public function testToStreet()
    {
        $map = \PMVC\plug($this->_plug);
        $map['center'] = '46.414382,10.013988';
        $expected = 'https://maps.googleapis.com/maps/api/streetview?location=46.414382%2C10.013988&size=640x300';
        $this->assertEquals($expected, $map->toStreet());
    }

}
