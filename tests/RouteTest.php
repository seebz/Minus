<?php

require_once 'PHPUnit/Autoload.php';
require_once dirname(__FILE__) . '/../src/Minus/Route.php';


/**
 * Test class for Route.
 */
class RouteTest extends PHPUnit_Framework_TestCase
{

    /**
     * Runs the test methods of this class.
     */
    public static function main()
    {
        $suite = new PHPUnit_Framework_TestSuite(__CLASS__);
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }



    /**
     * @dataProvider providerTo
     */
    public function testTo($to, $expected)
    {
        $route = new Minus\Route('', $to);

        $this->assertEquals($expected, $route->to());
    }

    public function providerTo()
    {
        $data = array();
        foreach($this->datasTo as $k => $v)
            $data[] = array($k, $v);
        return $data;
    }

    // $to => $expected
    protected $datasTo = array(
        'controller'                                 => array('controller' => 'controller'),
        'controller#action'                          => array('controller' => 'controller', 'action' => 'action'),
        'controller#action()'                        => array('controller' => 'controller', 'action' => 'action'),
        'controller#action(id)'                      => array('controller' => 'controller', 'action' => 'action', 'id' => 'id'),
        'controller#action(arg1, arg2, argX)'        => array('controller' => 'controller', 'action' => 'action', 'arguments' => array('arg1', 'arg2', 'argX')),
        'module\controller'                          => array('module' => 'module', 'controller' => 'controller'),
        'module/controller'                          => array('module' => 'module', 'controller' => 'controller'),
        'module/controller#action'                   => array('module' => 'module', 'controller' => 'controller', 'action' => 'action'),
        'module/controller#action(id)'               => array('module' => 'module', 'controller' => 'controller', 'action' => 'action', 'id' => 'id'),
        'module/controller#action(arg1, arg2, argX)' => array('module' => 'module', 'controller' => 'controller', 'action' => 'action', 'arguments' => array('arg1', 'arg2', 'argX')),
    );



    /**
     * @dataProvider providerParseHome
     */
    public function testParseHome($path, $expected)
    {
        $route = new Minus\Route('/', 'pages#home', array('format' => false));

        $result = $route->parse($path);
        $this->assertEquals($expected, $result);
    }

    public function providerParseHome()
    {
        $data = array();
        foreach($this->datasParseHome as $k => $v)
            $data[] = array($k, $v);
        return $data;
    }

    // $path => $expected
    protected $datasParseHome = array(
        ''     => array('controller' => 'pages', 'action' => 'home', 'format' => 'html'),
        '/'    => array('controller' => 'pages', 'action' => 'home', 'format' => 'html'),
        '/404' => false,
    );



    /**
     * @dataProvider providerParse404
     */
    public function testParse404($path, $expected)
    {
        $route = new Minus\Route('404', 'application#error(404)');

        $result = $route->parse($path);
        $this->assertEquals($expected, $result);
    }

    public function providerParse404()
    {
        $data = array();
        foreach($this->datasParse404 as $k => $v)
            $data[] = array($k, $v);
        return $data;
    }

    // $path => $expected
    protected $datasParse404 = array(
        '404'      => array('controller' => 'application', 'action' => 'error', 'id' => '404', 'format' => 'html'),
        '/404'     => array('controller' => 'application', 'action' => 'error', 'id' => '404', 'format' => 'html'),
        '/404.txt' => array('controller' => 'application', 'action' => 'error', 'id' => '404', 'format' => 'txt'),
    );



    /**
     * @dataProvider providerParseX
     */
    public function testParseX($path, $expected)
    {
        $route = new Minus\Route('/:controller(/:action(/:id))', '', array('defaults' => array('action' => 'index')));

        $result = $route->parse($path);
        $this->assertEquals($expected, $result);
    }

    public function providerParseX()
    {
        $data = array();
        foreach($this->datasParseX as $k => $v)
            $data[] = array($k, $v);
        return $data;
    }

    // $path => $expected
    protected $datasParseX = array(
        '/my-controller'                     => array('controller' => 'my-controller', 'action' => 'index', 'format' => 'html'),
        '/my-controller/my-action'           => array('controller' => 'my-controller', 'action' => 'my-action', 'format' => 'html'),
        '/my-controller/my-action/42'        => array('controller' => 'my-controller', 'action' => 'my-action', 'id' => '42', 'format' => 'html'),
        '/my-controller/my-action/my-id'     => false,
        '/my-controller.txt'                 => array('controller' => 'my-controller', 'action' => 'index', 'format' => 'txt'),
        '/my-controller/my-action.txt'       => array('controller' => 'my-controller', 'action' => 'my-action', 'format' => 'txt'),
        '/my-controller/my-action/42.txt'    => array('controller' => 'my-controller', 'action' => 'my-action', 'id' => '42', 'format' => 'txt'),
        '/my-controller/my-action/my-id.txt' => false,
    );



    /**
     * @dataProvider providerMatchHome
     */
    public function testMatchHome($params)
    {
        $route = new Minus\Route('/', 'pages#home', array('format' => false));

        $result = $route->match($params);
        $this->assertEquals('/', $result);
    }

    public function providerMatchHome()
    {
        $data = array();
        foreach($this->datasMatchHome as $k => $v)
            $data[] = array($v);
        return $data;
    }

    // $params
    protected $datasMatchHome = array(
        array('controller' => 'pages', 'action' => 'home'),
        array('controller' => 'pages', 'action' => 'home', 'format' => 'html'),
    );



    /**
     * @dataProvider providerMatch404
     */
    public function testMatch404($params)
    {
        $route = new Minus\Route('404', 'application#error(404)');

        $result = $route->match($params);
        $this->assertEquals('/404', $result);
    }

    public function providerMatch404()
    {
        $data = array();
        foreach($this->datasMatch404 as $k => $v)
            $data[] = array($v);
        return $data;
    }

    // $params
    protected $datasMatch404 = array(
        array('controller' => 'application', 'action' => 'error', 'id' => '404'),
        array('controller' => 'application', 'action' => 'error', 'id' => '404', 'format' => 'html'),
    );



    /**
     * @dataProvider providerMatchX
     */
    public function testMatchX($params, $expected)
    {
        $route = new Minus\Route('/:controller(/:action(/:id))', '', array('defaults' => array('action' => 'index')));

        $result = $route->match($params);
        $this->assertEquals($expected, $result);
    }

    public function providerMatchX()
    {
        $data = array();
        foreach($this->datasMatchX as $k => $v)
            $data[] = array($v, $k);
        return $data;
    }

    // $expected => $params
    protected $datasMatchX = array(
        '/my-controller'                  => array('controller' => 'my-controller'),
        '/my-controller'                  => array('controller' => 'my-controller', 'action' => 'index'),
        '/my-controller'                  => array('controller' => 'my-controller', 'action' => 'index', 'format' => 'html'),
        '/my-controller/my-action'        => array('controller' => 'my-controller', 'action' => 'my-action'),
        '/my-controller/my-action'        => array('controller' => 'my-controller', 'action' => 'my-action', 'format' => 'html'),
        '/my-controller/my-action/42'     => array('controller' => 'my-controller', 'action' => 'my-action', 'id' => '42'),
        '/my-controller/my-action/42'     => array('controller' => 'my-controller', 'action' => 'my-action', 'id' => '42', 'format' => 'html'),
        '/my-controller.txt'              => array('controller' => 'my-controller', 'format' => 'txt'),
        '/my-controller.txt'              => array('controller' => 'my-controller', 'action' => 'index', 'format' => 'txt'),
        '/my-controller/my-action.txt'    => array('controller' => 'my-controller', 'action' => 'my-action', 'format' => 'txt'),
        '/my-controller/my-action/42.txt' => array('controller' => 'my-controller', 'action' => 'my-action', 'id' => '42', 'format' => 'txt'),
    );

}



// Call RouteTest::main() if this source file is executed directly.
if (! defined('PHPUnit_MAIN_METHOD')) {
    RouteTest::main();
}
