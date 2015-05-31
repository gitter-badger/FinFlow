<?php
/**
 * FinFlow 1.0 - Import class implementation
 * @author Adrian S. (adrian@studentmoneysaver.co.uk)
 * @version 1.0
 */

namespace FinFlow;

use FinFlow\Parsers\CSVImporter;

class Import{

	protected static $supportedExtensions = array('csv');

	public static function getSupportedExtensions(){
		return self::$supportedExtensions;
	}

	public static function getImporter($path){

		if( file_exists($path) ){

			$ext = File::getExtension($path);

			if( in_array($ext, self::$supportedExtensions) ){

				switch($ext){

					case 'csv':
						return new CSVImporter($path);
					break;

				}

			}
			else{
				@unlink($path); throw new \Exception(__t('Extension %s is not supported.', $ext));
			}

		}
		else
			throw new \Exception(__t('File %s not found', $path));

	}

	public static function getFileImporter($path){
		//TODO...
	}

	public static function getGenericImporter($name){
		//TODO...
	}

}