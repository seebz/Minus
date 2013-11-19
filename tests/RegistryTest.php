<?php

require_once 'PHPUnit/Autoload.php';
require_once dirname(__FILE__) . '/../src/Singleton.php';
require_once dirname(__FILE__) . '/../src/Registry.php';


/**
 * Test class for Registry.
 */
class RegistryTest extends PHPUnit_Framework_TestCase
{

	protected $_className = 'minus\Registry';


	/**
	 * Runs the test methods of this class.
	 */
	public static function main()
	{
		$suite = new PHPUnit_Framework_TestSuite(__CLASS__);
		$result = PHPUnit_TextUI_TestRunner::run($suite);
	}


	public function testMethodInstance()
	{
		$class = $this->_className;
		$this->assertTrue( class_exists($class), "Class `{$class}` don't exists" );

		$method = 'instance';
		$this->assertTrue( method_exists($class, $method), "Method `{$class}::{$method}()` don't exists" );
		$this->assertTrue( is_callable($class.'::'.$method), "Method `{$class}::{$method}()` isn't callable" );

		$instance = $class::$method();
		$this->assertInstanceOf($class, $instance);
	}


	public function dataProvider()
	{
		return array(
			array('true',   true),
			array('false',  false),
			// array('null',   null),
			array('number', 42),
			array('string', 'Hello world'),
			array('array',  array('one', 'two', 'three')),
			array('object', (object) array('key' => 'value')),
		);
	}

	/**
	 * @dataProvider dataProvider
	 */
	public function testArrayAccessMethods($index, $value)
	{
		$class = $this->_className;
		$instance = $class::instance();

		// offsetSet()
		$instance->offsetSet($index, $value);

		// offsetExists()
		$this->assertTrue( $instance->offsetExists($index) );

		// offsetGet()
		$this->assertSame( $value, $instance->offsetGet($index) );

		// offsetUnset()
		$instance->offsetUnset($index);
		$this->assertFalse( $instance->offsetExists($index) );
	}

	/**
	 * @dataProvider dataProvider
	 */
	public function testArrayAccessProperties($index, $value)
	{
		$class = $this->_className;
		$instance = $class::instance();

		// set
		$instance[$index] = $value;

		// isset
		$this->assertTrue( isset($instance[$index]) );

		// get
		$this->assertSame( $value, $instance[$index] );

		// unset
		unset($instance[$index]);
		$this->assertFalse( isset($instance[$index]) );
	}

	/**
	 * @dataProvider dataProvider
	 */
	// public function testMagicMethods($index, $value)
	// {
	// 	$class = $this->_className;
	// 	$instance = $class::instance();

	// 	// __set()
	// 	$instance->__set($index, $value);

	// 	// __isset()
	// 	$this->assertTrue( $instance->__isset($index) );

	// 	// __get()
	// 	$this->assertSame( $value, $instance->__get($index) );

	// 	// __unset()
	// 	$instance->__unset($index);
	// 	$this->assertFalse( $instance->__isset($index) );
	// }

	/**
	 * @dataProvider dataProvider
	 */
	public function testMagicProperties($index, $value)
	{
		$class = $this->_className;
		$instance = $class::instance();

		// set
		$instance->{$index} = $value;

		// isset
		$this->assertTrue( isset($instance->{$index}) );

		// get
		$this->assertSame( $value, $instance->{$index} );

		// unset
		unset($instance->{$index});
		$this->assertFalse( isset($instance->{$index}) );
	}

	/**
	 * @dataProvider dataProvider
	 */
	public function testStaticMethods($index, $value)
	{
		$class = $this->_className;

		// set()
		$class::set($index, $value);

		// exists()
		$this->assertTrue( $class::exists($index), "Index `{$index}` don't exists" );

		// get()
		$v = $class::get($index);
		$this->assertSame( $value, $v, "Value of index `{$index}` don't match" );

		// remove()
		$class::remove($index);
		$this->assertFalse( $class::exists($index) );
	}


	public function testCountable()
	{
		$class = $this->_className;
		$instance = $class::instance();

		$this->assertCount(0, $instance);

		$instance->one = 'one';
		$instance->two = 'two';

		$this->assertCount(2, $instance);

		unset($instance->one);
		unset($instance->two);

		$this->assertCount(0, $instance);
	}

}



// Call RegistryTest::main() if this source file is executed directly.
if (! defined('PHPUnit_MAIN_METHOD')) {
	RegistryTest::main();
}
