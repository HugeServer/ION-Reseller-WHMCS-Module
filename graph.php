<?php
   include_once 'config.php';
   include "functions.php";
   include "api.php";
   include_once( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . '../../../configuration.php');

   header("Cache-Control: no-cache, must-revalidate");

   $db_conn = mysql_connect( $db_host, $db_username, $db_password );
   if ( !$db_conn )
       throw new Exception( 'Unable to connect to DB' );

   $db_select = @mysql_select_db( $db_name, $db_conn );
   if ( !$db_select )
       throw new Exception( 'Unable to select WHMCS database' );

   if( !isset( $_GET['pid'] ) )
       throw new Exception( 'Argument missing' );

   $wid =trim(ion_decrypt( $_GET['pid'] ));

   $query = "SELECT ion_sid from ion_module WHERE whmcs_sid = '{$wid}'";
   $result = mysql_query( $query );

   if( mysql_num_rows( $result ) < 1 || strlen($wid) > 5 ) {
       die("not found");
   }

   $sid = mysql_fetch_array( $result )[0];
   $period = isset( $_GET['period'] ) ? $_GET['period'] : 'hour';

   header('Content-Type: image/PNG');
   echo APIClient::serverGraph( ION_API, array( 'serverID' => $sid, 'period' => $period, 'title' => $_GET['title'] ) );