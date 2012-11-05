<?php

class Aimya_Navigation extends My_Navigation_Container
{
	public function __construct($pages=null)
	{
		if (is_array($pages)||$pages instanceof Zend_Config)
		{
			$this->addPages($pages);
		}
		elseif (null!==$pages)
		{
			require_once 'Zend/Navigation/Exception.php';
			throw new Zend_Navigation_Exception('Invalid argument: $pages must be an array, an '.'instance of Zend_Config, or null');
		}
	}
}
