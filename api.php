<?php
/**
 * WHMCS v6 Module for HugeServer Resellers
 *
 * @author   HugeServer Networks, LLC - Development Team
 */

class APIClient
{
    public static $url = API_HOST;
    public static $file = false;
    public static $fileName = 'file.txt';
    public static $fileContentType = 'text/plain';

    public static function __callStatic( $method, $param )
    {
        if ( !extension_loaded('curl') ) {
            throw new Exception('cURL support is required',1);
        }
        if ( !isset( $method ) ) {
            throw new Exception( "Syntax Error", 3 );
        }
        if ( strpos( $param[0], "-" ) != 0 ) {
            $key = str_replace( '-', ':', $param[0] );
        } else if ( strpos( $param[0], ":" ) == 0 ) {
            throw new Exception( "API Key Not Correct", 2 ) ;
        } else {
            $key = $param[0];
        }
        $ch = curl_init();

        if ( isset( $param[1] ) && !( count( $param[1] ) == 1 && strpos( key( $param[1] ), 'ID') > 1 ) ) {
            curl_setopt( $ch, CURLOPT_POST, true );
            curl_setopt( $ch, CURLOPT_POSTFIELDS, $param[1] );
            curl_setopt( $ch, CURLOPT_URL, self::$url . $method );
        } else if( isset( $param[1] ) ) {
            curl_setopt( $ch, CURLOPT_URL, self::$url . $method . '/' . $param[1][ key($param[1]) ] );
        } else {
            curl_setopt( $ch, CURLOPT_URL, self::$url . $method );
        }

        curl_setopt( $ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC );
        curl_setopt( $ch, CURLOPT_USERPWD, $key );
        curl_setopt( $ch, CURLOPT_FOLLOWLOCATION,1 );
        curl_setopt( $ch, CURLOPT_MAXREDIRS, 2 );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
        try {
            $result = curl_exec( $ch );
        } catch ( RestException $e ) {
            throw new Exception( curl_error( $ch ), curl_errno( $ch ) );
        }
        if( $result === false ) {
            curl_close( $ch );
            throw new Exception( curl_error( $ch ), curl_errno( $ch ) );
        }
        if( self::$file ) {
            header("Cache-Control: public");
            header("Content-type: " . self::$fileContentType);
            header("Content-Description: File Transfer");
            header("Content-Disposition: attachment; filename=" . self::$fileName);
            header("Content-Type: application/octet-stream; ");
            header("Content-Transfer-Encoding: binary");
            self::$file = false;
            self::$fileName = 'file.txt';
            self::$fileContentType = 'text/plain';
            return $result;
        }
        if( (strpos( $result, "{" ) >= 0 && strpos( $result, "{" ) < 10 ) || curl_getinfo( $ch, CURLINFO_CONTENT_TYPE ) == 'application/json') {
            return json_decode($result, true);
        } else {
            return $result;
        }
    }
}


