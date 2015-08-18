<?php
define( 'API_HOST' ,'https://ion.hugeserver.com/api/v2/' );
define( 'ION_API','usrID-Key' ); 
define( 'ION_HASH' ,'Change Me' );
define( 'DOWNLOAD_VPN','#' ); // url of vpn connection file
//------- VPN connection tutorial url
define( 'LINUX_VPN','#' );
define( 'WIN_VPN','#' );
define( 'MAC_VPN','#' );

// default access list for clients. this can be changed individually for each client on his/her service page
$accessList = array(
    'server_info'   => '1',
    'vpn'           => '1',
    'server_os'     => '1',
    'bw_graph'      => '1',
    'rdns'          => '1',
    'ipmi'          => '1',
    'bw_statics'    => '1',
    'ip'            => '1',
    'reboot'        => '1',
);
