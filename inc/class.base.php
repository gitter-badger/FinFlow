<?php
/**
 * FinFlow 1.0 - Base Class
 * @author Adrian S. (http://adrian.silimon.eu/)
 * @version 1.0
 */

/**
 * Class fn_Base
 * @deprecated
 */
class fn_Base{

    public static function db_table_name($name, $prefix=null){
        $prefix = empty($prefix) ? FN_DB_PREFIX : $prefix; return ($prefix . $name);
    }

}