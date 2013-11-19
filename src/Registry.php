<?php

namespace minus;


/**
 * Registry
 */
class Registry extends Singleton implements \ArrayAccess, \Countable
{

	public static function set($index, $value)
	{
		$instance = static::instance();
		return $instance->offsetSet($index, $value);
	}

	public static function get($index, $default = null)
	{
		$instance = static::instance();
		return $instance->offsetExists($index)
			? $instance->offsetGet($index)
			: $default;
	}

	public static function exists($index)
	{
		$instance = static::instance();
		return $instance->offsetExists($index);
	}

	public static function remove($index)
	{
		$instance = static::instance();
		return $instance->offsetUnset($index);
	}


	public function offsetSet($offset, $value)
	{
		return $this->{$offset} = $value;
	}

	public function offsetGet($offset)
	{
		return isset($this->{$offset}) ? $this->{$offset} : null;
	}

	public function offsetExists($offset)
	{
		return isset($this->{$offset});
	}

	public function offsetUnset($offset)
	{
		unset($this->{$offset});
	}


	public function count()
	{
		return count((array) $this);
	}

}
