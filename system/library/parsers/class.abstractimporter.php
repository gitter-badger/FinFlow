<?php
/**
 * FinFlow 1.0 - Importer interface
 * @author Adrian S. (adrian@studentmoneysaver.co.uk)
 * @version 1.0
 */

namespace FinFlow\Parsers;

abstract class AbstractImporter{
	abstract function config($cfg);
	abstract function set($key, $value=1);
	abstract function get($key);
	abstract function getColumns();
	abstract function import();
	abstract function getErrors();
	abstract function addError($message);
}