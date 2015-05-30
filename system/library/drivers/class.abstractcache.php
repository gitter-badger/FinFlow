<?php
/**
 * FinFlow 1.0 - Cache driver implementation
 * @author adrian7 (adrian@finflow.org)
 * @version 1.0
 */

namespace FinFlow\Drivers;

abstract class AbstractCache{
	abstract public function init($cfg=array());
	abstract function set($key, $value=1);
	abstract function get($key);
	abstract function remove($key);
	abstract function clean();
	abstract function stats();
}