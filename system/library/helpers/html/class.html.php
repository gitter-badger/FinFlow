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

	public static function title($s){
		//TODO...
	}

	public static function meta($name, $contents=''){
		//TODO...
	}

	public static function head($inner){
		//TODO...
	}

	public static function script($src, $type='text/javascript', $async=true, $attributes=array()){
		//TODO...
	}

	public static function link($rel, $media='all', $attributes=array()){
		//TODO...
	}

	public static function body($s){
		//TODO...
	}

	public static function select($name, $options=array(), $selected='', $attributes=array()){

		$inner      = '';
		$attributes = parse_args($attributes);
		$_defaults  = array(
			'name'=>$name,
			'id'  =>$name,
		);

		$attributes = array_merge($_defaults, $attributes);

		foreach($options as $value=>$option){

			if( $selected ){
				if( ( is_array($selected) and in_array($value, $selected) ) or ( $selected == $value ) )
					$props = array('value'=>$value, 'selected'=>'selected');
				else
					$props = array('value'=>$value);
			}
			else
				$props = array('value'=>$value);

			$inner.= self::make('option', $props, " $option ");

		}


		return self::make('select', $attributes, $inner);

	}

	public static function input($name, $type='text', $value=null, $attributes=array()){

		$attributes = parse_args($attributes);
		$_defaults  = array(
			'type'  => strval($type),
			'name'  => $name,
			'id'    => $name,
			'size'  => 45,
			'value' => strval($value)
		);

		$attributes = array_merge($_defaults, parse_args($attributes));

		return self::make('input', $attributes);
	}

	public function radio($name, $value, $checked, $attributes=array()){

		$attributes = parse_args($attributes);
		$_defaults  = array(
			'type'  => 'radio',
			'name'  => $name,
			'id'    => $name,
			'value' => strval($value)
		);

		if( $checked )
			$attributes['checked'] = 'checked';

		$attributes = array_merge($_defaults, $attributes);

		return self::make('input', $attributes);

	}

	public function check($name, $value, $checked, $attributes=array()){
		return self::radio($name, $value, $checked, array_merge($attributes, array('type'=>'checkbox')));
	}

	public static function textarea($name, $contents='', $attributes=array()){
		$_defaults  = array(
			'name'  => $name,
			'id'    => $name,
			'cols'  => 45,
			'rows'  => 4,
		);

		if( ! empty($contents) )
			$contents = esc_html($contents);

		$attributes = array_merge($_defaults, $attributes);

		return self::make('textarea', $attributes, $contents);

	}

	public static function table($attributes){

	}

}