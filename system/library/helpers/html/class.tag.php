<?php
/**
 * FinFlow 1.0 - [file description]
 * @author adrian7 (adrian@studentmoneysaver.co.uk)
 * @version 1.0
 */

namespace FinFlow\Helpers\HTML;

class Tag{

	const ATTRS_TPL_HINT      = '{__attributes__}';
	const INNER_HTML_TPL_HINT = '{__inner__}';

	protected static $voidTags = array(
		'area', 'base', 'br', 'col', 'command', 'embed', 'hr', 'img', 'input', 'keygen', 'link', 'meta', 'param', 'source', 'track', 'wbr'
	);

	/**
	 * Extracts the first tag name found
	 * @param $html
	 * @return string
	 */
	protected static function getName($html){
		$html = strpos($html, '<') === false ? $html : substr($html, strpos($html, '<')+1);
		$html = strpos($html, '>') === false ? $html : str_replace('>', ' ', $html);
		$html = strpos($html, ' ') === false ? $html : substr($html, 0, strpos($html, ' '));

		return trim($html);
	}

	protected static function isVoid($tag){
		$tag = trim( strtolower( self::getName($tag) ) ); return in_array($tag, self::$voidTags);
	}

	protected static function setAttributes($tag, $attributes=array(), $autoclose=false){

		$tagName     = self::getName($tag);
		$attrs       = parse_args($attributes);
		$is_template = ( strpos($tag, self::ATTRS_TPL_HINT) === false ) ? false : true;

		if( ! $is_template ){

			if( strpos($tag, "<{$tagName} ") === false ); else{ //insert attributes after  the tag name
				$tag = str_replace("<{$tagName} ", ("<{$tagName} " . self::ATTRS_TPL_HINT . ' '), $tag);
			}

			if( strpos($tag, "<{$tagName}>") === false ); else{ //empty tag
				$tag = str_replace("<{$tagName}>", ("<{$tagName} " . self::ATTRS_TPL_HINT . '>'), $tag);
			}

		}

		$is_template = ( strpos($tag, self::ATTRS_TPL_HINT) === false ) ? false : true;

		$attrstring = '';

		foreach($attrs as $name=>$value)
			$attrstring.= ( ' '. $name . '="' . esc_attr( strval($value) ) . '"' );

		if( $is_template )
			return str_replace(self::ATTRS_TPL_HINT, trim($attrstring), $tag);

		if( strpos($tag, "<{$tagName}") === false )
			$tag = trim( self::open($tagName) );

		$tag.= $attrstring;

		return ( $autoclose ? self::close($tag) : $tag );

	}

	protected static function setAttribute($tag, $name, $value=1, $autoclose=false){
		return self::setAttributes($tag, array($name=>$value), $autoclose);
	}

	protected static function inner($tag, $html, $autoclose=true){

		$tagName     = self::getName($tag);
		$tag         = trim($tag);
		$is_template = ( strpos($tag, self::ATTRS_TPL_HINT) === false ) ? false : true;

		if( self::isVoid($tagName) )
			return self::close($tag);

		if( strpos($tag, "<{$tagName}") === false ) //tag not opened
			$tag = trim( self::open($tagName) );

		if( ! $is_template and ( strpos($tag, '>') === false ) ){ //missing attributes enclosing
			$tag.= ( '>' . self::INNER_HTML_TPL_HINT );
		}

		$is_template = ( strpos($tag, self::INNER_HTML_TPL_HINT) === false ) ? false : true;

		if( $is_template )
			$tag = str_replace(self::INNER_HTML_TPL_HINT, $html, $tag);
		else
			$tag.= $html;

		return $autoclose ? self::close($tag) : $tag;

	}

	protected static function open($tag){

		$tag = self::getName($tag);
		$tag = strtolower(trim($tag));

		return "<{$tag}";
	}

	protected static function close($tag){

		$name= self::getName($tag);
		$name = strtolower(trim($name));

		$openings_count   = substr_count($tag, "<{$name}");
		$enclosings_count = self::isVoid($name) ? substr_count($tag, "/>") : substr_count($tag, "</{$name}");

		if( $enclosings_count != $openings_count )
			return in_array($name, self::$voidTags) ? ( $tag . "/>") : ( $tag . ( ( strpos($tag, '>') === false ) ? ("></{$name}>") : ( "</{$name}>" ) ) );

		return $tag; //tags seem to be valid

	}

	protected static function make($tag, $attributes=array(), $innerhtml='', $template=false){

		$tagName = strtolower( self::getName($tag) );
		$tag     = ( "<{$tagName} " . self::ATTRS_TPL_HINT );

		if( self::isVoid($tagName) ){
			$tag .= '/>'; $innerhtml = null;
		}
		else
			$tag .= ( '>' . self::INNER_HTML_TPL_HINT . "</{$tagName}>" );

		if( $template ){ //build a tag template
			return $tag;
		}

		$tag = self::setAttributes($tag, $attributes, false);

		if( $innerhtml )
			$tag = self::inner($tag, $innerhtml);

		return $tag;

	}

	public static function __tag($name, $attributes=array(), $innerhtml=''){
		return self::make($name, $attributes, $innerhtml);
	}

}