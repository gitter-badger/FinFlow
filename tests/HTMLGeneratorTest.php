<?php
/**
 * FinFlow 1.0 - Tests for the HTML generator
 * @author adrian7 (adrian@studentmoneysaver.co.uk)
 * @version 1.0
 */

include_once '../system/library/autoload.php';
include_once '../system/thirdparty/autoload.php';

use FinFlow\Helpers\HTML\HTML;

class HTMLGeneratorTest extends PHPUnit_Framework_TestCase{

	public function testGenerateDoctype(){
		$this->assertEquals('<!DOCTYPE html>', HTML::doctype());
	}

}