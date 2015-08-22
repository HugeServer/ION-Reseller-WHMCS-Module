<?php
/**
 * WHMCS v6 Module for HugeServer Resellers
 *
 * @author   HugeServer Networks, LLC - Development Team
 */
 
   include "config.php";
   require_once __DIR__ . "/functions.php";
   include "api.php";

   if( !isset( $_GET['sid'] ) ) {
       die('Argument missing');
   }

   $sid = (int) trim(ion_decrypt( $_GET['sid'] ));
   $res = APIClient::serverReboot( ION_API, array( 'serverID' => $sid ) );
   if( $res ) {
       header( "Location: " . $_SERVER['HTTP_REFERER'] . "&reboot=success" );
   } else {
       header( "Location: " . $_SERVER['HTTP_REFERER'] . "&reboot=failed" );
   }
