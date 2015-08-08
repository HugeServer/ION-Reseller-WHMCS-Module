<?php
include_once __DIR__ . "/config.php";
function ion_mcrypt( $value ) {
   $hash = ION_HASH;
   $key = pack('H*', $hash);
   $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
   $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
   $ciphertext = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $value, MCRYPT_MODE_CBC, $iv);
   $ciphertext = $iv . $ciphertext;
   return urlencode( base64_encode($ciphertext) );
}
function ion_decrypt( $value ) {
   $hash = ION_HASH;
   $key = pack( 'H*',  $hash );
   $iv_size = mcrypt_get_iv_size( MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC );
   $ciphertext_dec = base64_decode( $value );
   $iv_dec = substr( $ciphertext_dec, 0, $iv_size );
   $ciphertext_dec = substr( $ciphertext_dec, $iv_size );
   return mcrypt_decrypt( MCRYPT_RIJNDAEL_128, $key, $ciphertext_dec, MCRYPT_MODE_CBC, $iv_dec);
}

function formatBytes($bytes, $precision = 2)
{
   $units = array('B', 'KB', 'MB', 'GB', 'TB');
   $bytes = max($bytes, 0);
   $pow = floor(($bytes ? log($bytes) : 0) / log(1000));
   $pow = min($pow, count($units) - 1);
   $bytes /= pow(1000, $pow);
   return round($bytes, $precision) . ' ' . $units[$pow];
}