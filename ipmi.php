<?php
   include "config.php";
   require_once __DIR__ . "/functions.php";
   include_once "api.php";

   if( !isset( $_GET['sid'] ) ) {
       die('Argument missing');
   }

   APIClient::$file = true;
   APIClient::$fileContentType = "application/x-java-jnlp-file";
   APIClient::$fileName = "IPMI-{$_GET['hostname']}.jnlp";
   $sid = (int) ion_decrypt( $_GET['sid'] );
   echo APIClient::serverIPMI( ION_API, array( 'serverID' => $sid ) );