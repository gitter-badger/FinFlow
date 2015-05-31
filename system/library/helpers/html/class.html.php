<?php
/**
 * FinFlow 1.0 - Html helpers collection class
 * @author adrian7 (adrian@studentmoneysaver.co.uk)
 * @version 1.0
 */

namespace FinFlow\Helpers\HTML;

class HTML extends Tag{

	const DOCTYPE = '<!DOCTYPE html>';

	public static function __tidy($html){
		//TODO...
	}

	public static function doctype(){
		return self::DOCTYPE;
	}

	public static function select($name, $options=array(), $attributes=array()){

		$inner      = '';
		$_defaults  = array(
			'name'=>$name,
			'id'  =>$name,
		);

		$attributes = array_merge($_defaults, $attributes);

		foreach($options as $opt=>$value)
			$inner.= self::make('option', array('value'=>$value), strval($opt));

		die($inner);

		return self::make('select', $attributes, $inner);
	}

	public static function input($name, $type='text', $attributes=array()){
		//TODO...
	}

	public static function textarea(){
		//TODO ...
	}

	public static function script(){
		//TODO...
	}

}