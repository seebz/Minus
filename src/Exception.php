<?php

namespace minus;


/**
 * Exception
 */
class Exception extends \Exception
{

	const ROUTE_NOT_FOUND      = 1;
	const CONTROLLER_NOT_FOUND = 2;
	const ACTION_NOT_FOUND     = 3;

}
