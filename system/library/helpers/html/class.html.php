<?php
/**
 * FinFlow 1.0 - Html helpers collection class
 * @author adrian7 (adrian@studentmoneysaver.co.uk)
 * @version 1.0
 */

namespace FinFlow\Helpers\HTML;

class HTML extends Tag{

	const DOCTYPE = '<!DOCTYPE html>';

	/**
	 * Tidies html
	 * @param $html
	 * @param array $config
	 * @param $encoding
	 * @throws \Exception
	 * @return string
	 */
	public static function __tidy($html, $config=array('indent'=>true, 'vertical-space'=>'no', 'wrap'=>0, 'output-xhtml'=>false, 'show-body-only'=>true), $encoding='utf8'){
		if( class_exists('tidy') ){

			$tidy = new \tidy();
			return $tidy->repairString($html, $config, $encoding);

		}
		else
			throw new \Exception("Tidy operation requires PHP Tidy extension.");
	}

	public static function __pretty($html){
		return self::__tidy($html, $config=array('indent'=>true, 'wrap'=>200, 'vertical-space'=>'yes', 'output-xhtml'=>false));
	}

	public static function __tag($name, $inner='', $attributes=array()){
		return self::make($name, $attributes, $inner);
	}

	public static function doctype(){
		return self::DOCTYPE;
	}

	public static function title($s){
		return self::make('title', array(), $s);
	}

	public static function meta($name, $contents=''){
		return self::make('meta', array('name'=>$name, 'contents'=>$contents));
	}

	public static function head($inner){
		return self::make('head', array(), $inner);
	}

	public static function script($src, $type='text/javascript', $inner='', $attributes=''){

		$attributes = array_merge($attributes, array( 'type' => $type) );

		if( ! empty($src) )
			$attributes['src'] = $src;

		return self::make('script', $attributes, $inner='');

	}

	public static function link($href, $rel='stylesheet', $media='all', $attributes=array()){

		$attributes = array_merge(
			$attributes,
			array(
				'rel'   => $rel,
				'media' => $media,
				'href'  => $href,
			)
		);

		return self::make('link', $attributes);

	}

	public static function body($inner, $attributes=array()){
		return self::make('body', $attributes, $inner);
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

	public static function tr($cells=array(), $celltype='td', $attributes=array()){

		$cells      = parse_args($cells);
		$celltype   = ( $celltype == 'th' ) ? $celltype : 'td';
		$html  = '';

		if( count($cells) ) foreach($cells as $index=>$data){
			$html.= self::make($celltype, null, $data);
		}

		return self::make('tr', $attributes, $html);

	}

	public static function table($inner, $attributes=array()){
		return self::make('table', $attributes, $inner);
	}
}