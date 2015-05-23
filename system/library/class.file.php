<?php
/**
 * FinFlow 1.0 - File upload/download/delete interface
 * @author Adrian S. (adrian@studentmoneysaver.co.uk)
 * @version 1.0
 */

namespace FinFlow;

class File{

	const TABLE       = 'fn_files';
	const TABLE_ASSOC = 'fn_op_attachments';

	/**
	 * A list of extensions allowed for files upload
	 * @var array
	 */
	protected static $allowed_extensions = array(
		'pdf', 'png', 'jpg', 'gif', 'doc', 'xls', 'docx', 'xlsx', 'psd', 'odt', 'ods', 'odf', 'txt', 'rtf', 'ppt', 'pptx', 'mp3', 'ogg', 'mp4', 'avi', 'mkv', 'zip', 'gz', 'tar', 'swf', 'fla', 'aac'
	);

	/**
	 * Extensions to mime type array
	 * @var array
	 */
	protected static $mime_types = array(
		'hqx'	=>	'application/mac-binhex40',
		'cpt'	=>	'application/mac-compactpro',
		'csv'	=>	'text/csv',
		'bin'	=>	'application/macbinary',
		'lzh'	=>	'application/octet-stream',
		'psd'	=>	'application/x-photoshop',
		'sea'	=>	'application/octet-stream',
		'dll'	=>	'application/octet-stream',
		'oda'	=>	'application/oda',
		'pdf'	=>	'application/pdf',
		'ai'	=>	'application/postscript',
		'eps'	=>	'application/postscript',
		'ps'	=>	'application/postscript',
		'smi'	=>	'application/smil',
		'smil'	=>	'application/smil',
		'mif'	=>	'application/vnd.mif',
		'xls'	=>	'application/msexcel',
		'ppt'	=>	'application/powerpoint',
		'wbxml'	=>	'application/wbxml',
		'wmlc'	=>	'application/wmlc',
		'dcr'	=>	'application/x-director',
		'dir'	=>	'application/x-director',
		'dxr'	=>	'application/x-director',
		'dvi'	=>	'application/x-dvi',
		'gtar'	=>	'application/x-gtar',
		'gz'	=>	'application/x-gzip',
		'phtml'	=>	'application/x-httpd-php',
		'phps'	=>	'application/x-httpd-php-source',
		'js'	=>	'application/x-javascript',
		'swf'	=>	'application/x-shockwave-flash',
		'sit'	=>	'application/x-stuffit',
		'tar'	=>	'application/x-tar',
		'tgz'	=>	'application/x-gzip-compressed',
		'xhtml'	=>	'application/xhtml+xml',
		'xht'	=>	'application/xhtml+xml',
		'zip'	=>  'application/x-zip-compressed',
		'mid'	=>	'audio/midi',
		'midi'	=>	'audio/midi',
		'mpga'	=>	'audio/mpeg',
		'mp2'	=>	'audio/mpeg',
		'mp3'	=>	'audio/mpeg3',
		'aif'	=>	'audio/x-aiff',
		'aiff'	=>	'audio/x-aiff',
		'wav'	=>	'audio/wave',
		'bmp'	=>	'image/bmp',
		'gif'	=>	'image/gif',
		'jpeg'	=>	'image/jpeg',
		'jpg'	=>	'image/jpeg',
		'jpe'	=>	'image/jpeg',
		'png'	=>	'image/png',
		'tiff'	=>	'image/tiff',
		'tif'	=>	'image/tiff',
		'css'	=>	'text/css',
		'html'	=>	'text/html',
		'htm'	=>	'text/html',
		'shtml'	=>	'text/html',
		'txt'	=>	'text/plain',
		'text'	=>	'text/plain',
		'log'	=>	'text/plain',
		'rtx'	=>	'text/richtext',
		'rtf'	=>	'text/rtf',
		'xml'	=>	'text/xml',
		'xsl'	=>	'text/xml',
		'mpeg'	=>	'video/mpeg',
		'mpg'	=>	'video/mpeg',
		'mpe'	=>	'video/mpeg',
		'qt'	=>	'video/quicktime',
		'mov'	=>	'video/quicktime',
		'avi'	=>	'video/x-msvideo',
		'movie'	=>	'video/x-sgi-movie',
		'doc'	=>	'application/msword',
		'word'	=>	'application/msword',
		'xl'	=>	'application/excel',
		'eml'	=>	'message/rfc822',
		'json'  => 'application/json',
	);

	protected static $errors   = array();
	protected static $warnings = array();

	public static $last_file_id;

	public static function save($path){

		global $fndb, $fnsql;

		if( @file_exists($path) ){

			$filename = $fndb->escape( basename($path) );
			$size     = filesize($path);
			$mime     = $fndb->escape( self::getMimeType($path) );
			$date     = date(FN_MYSQL_DATE, @filemtime($path));

			$fnsql->insert(self::TABLE, array('filename'=>$filename, 'mime_type'=>$mime, 'size'=>$size, 'date_created'=>$date));

			if( $fndb->execute_query( $fnsql->get_query() ) ){
				self::$last_file_id = $fndb->last_insert_id; return $fndb->last_insert_id;
			}

		}

		return false;

	}

	public static function get($id){
		//TODO...
	}

	public static function remove($id){
		//TODO...
	}

	public static function upload($file, $folder=null, $prefix='', $suffix='', $filename=null, $allowExtensions=array(), $autosave=true){

		if( is_array($file) and empty($file['error']) ){

			$folder = empty($folder) ? FN_UPLOADS_DIR : $folder;

			$ofilename   = $file['name'];
			$allowedExts = array_merge(self::$allowed_extensions, $allowExtensions);

			$extension = Util::get_file_extension($ofilename);
			$filename  = isset($filename) ? $filename : basename($ofilename, ('.' . $extension) );

			$filename = trim($filename, ".");

			if( !in_array($extension, $allowedExts) ){

				$allowed = self::$allowed_extensions;

				$last    = array_pop($allowed);
				$allowed = @implode(", ", $allowed);
				$allowed.= __t(' or %s', $last);

				self::addError(
					__t('The file %1s cannot be uploaded due to file type restrictions. Only %2s files are accepted . ', $file['name'], $allowed)
				);

				return false;

			}

			//set filename
			$upload_filename = ( $prefix . $filename . $suffix . '.' . $extension );

			//upload path
			$upload_path = ( rtrim($folder, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR .  $upload_filename );

			if( @move_uploaded_file($file['tmp_name'], $upload_path )  ){
				if( $autosave ) self::save($upload_path); return $upload_path; //success
			}
			else
				self::addError(
					__t('The folder %s cannot be written to. Check the folder permissions and try again.', $folder)
				);

			return false;

		}
		else if($file['error']){

			if( $file['error'] == UPLOAD_ERR_INI_SIZE ){
				$sysmaxsize  = Util::fmt_filesize(  Util::env_get_cfg('upload_max_filesize', TRUE) );
				$error       = "Fisierul {$file['name']} este prea mare. Sunt acceptate doar fisiere mai mici de {$sysmaxsize}";
			}

			if( $file['error'] == UPLOAD_ERR_CANT_WRITE )
				$error = "Fatal error: can't write to the temporary folder, set for file uploads. Please review your server's php.ini settings and try again.";

			if( $file['error'] == UPLOAD_ERR_EXTENSION )
				$error = "The {$file['name']} cound not be uploaded due to file extension restrictions.";

			if( empty($error) )
				$error = "Oups! Unknown error. Maybe " . urldecode($_GET['upload']) . " is not writable by the current php process.";

			self::addError( $error );

		}

		return false;

	}

	public static function download($id, $mime=false){
		//TODO...
	}

	public static function addError($msg){
		self::$errors[] = $msg;
	}

	public static function getErrors(){
		return self::$errors;
	}

	public static function getLastError(){
		return count( self::$errors ) ? ( self::$errors[ count(self::$errors)-1 ] ) : null;
	}

	public static function addWarning($msg){
		self::$warnings[] = $msg;
	}

	public static function getWarnings(){
		return self::$warnings;
	}

	public static function getMimeType($path){

		$ext = Util::get_file_extension($path);

		if ( function_exists('finfo_file') ) {

			$finfo = finfo_open(FILEINFO_MIME_TYPE);
			$mime  = finfo_file($finfo, $path);

			finfo_close($finfo);

			return $mime;
		}

		return isset( self::$mime_types[$ext] ) ? self::$mime_types[$ext] : 'application/octet-stream';

	}

	public static function getExtension($filename){
		return Util::get_file_extension($filename);
	}

	public static function associateTransaction($trans_id, $file_id){

		global $fndb, $fnsql;

		$trans_id = intval($trans_id);
		$file_id  = intval($file_id);

		$fnsql->insert(self::TABLE_ASSOC, array('trans_id'=>$trans_id, 'file_id'=>$file_id));

		return $fndb->execute_query( $fnsql->get_query() );

	}
}