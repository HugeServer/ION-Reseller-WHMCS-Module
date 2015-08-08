<?php
include_once "config.php";
include_once "functions.php";
include_once "api.php";

function vpn_hooks($vars)
{
   if( $_GET['a'] != 'vpn')
      return;
   $var = array();
   $var['menu'] = "<a href='clientarea.php?action=productdetails&id={$_GET['id']}'>{$vars['domain']}</a>";
   $var['error'] = 'none';
   $var['password'] = '';

   $var['ruid'] = $vars['ruid'];
   $var['rid'] = APIClient::resellerID( ION_API );
   $var['hashruid'] = ion_mcrypt( $var['ruid'] );

   if( isset( $_POST['password'], $_POST['userid'] ) ) {
      $var['password'] = trim( $_POST['password'] );
      if( strlen( $var['password'] ) < 6 ) {
         $var['error'] = 'password';
      } else {
         $var['uid'] = ion_decrypt( urldecode( $_POST['userid'] ) );
         $result = APIClient::resellerSetVPN( ION_API, array( 'password' => $var['password'], 'userID' => $var['uid'] ) );
         if( is_array( $result ) ) {
            $var['error'] = 'failed';
            $var['msg'] = $result['error']['message'];
         } else {
            $var['error'] = 'success';
         }
      }
   }

   $var['content'] = '';
   if(trim(DOWNLOAD_VPN) != '#') {
      $var['content'] = '<a style="margin-bottom: 20px;" href="' . DOWNLOAD_VPN . '">Download VPN</a><hr>';
   }
   if( trim(LINUX_VPN) != '#' ||  trim(WIN_VPN) != '#' ||  trim(MAC_VPN) != '#') {
      $var['content'] .= '<h4>How to Connect:</h4>';
      $var['content'] .= (trim(LINUX_VPN) != '#' && trim(LINUX_VPN) != '') ? '<p><a href="' . LINUX_VPN . '">Linux</a></p>' : '';
      $var['content'] .= (trim(WIN_VPN) != '#' && trim(WIN_VPN) != '') ? '<p><a href="' . WIN_VPN . '">Windows</a></p>' : '';
      $var['content'] .= (trim(MAC_VPN) != '#' && trim(MAC_VPN) != '') ? '<p><a href="' . MAC_VPN . '">Mac</a></p>' : '';
   }
   return $var;
}

function graph_hooks($vars) {
   if( $_GET['a'] != 'graph')
      return;
   $var['pid'] = ion_mcrypt( $_GET['id'] );
   $var['menu'] = "<a href='clientarea.php?action=productdetails&id={$_GET['id']}'>{$vars['domain']}</a>";
   $var['title'] = $vars['hostname'];
   return $var;
}

function rdns_hooks($vars) {
   if( $_GET['a'] != 'rdns')
      return;
   $var = array();
   $var['menu'] = "<a href='clientarea.php?action=productdetails&id={$_GET['id']}'>{$vars['domain']}</a>";
   $var['id'] = $_GET['id'];

   if( isset( $_GET['ip'] ) ) {
      $varp['rdns_ip'] = 'yes';
      $var['menu'] = "<a href='clientarea.php?action=productdetails&id={$_GET['id']}'>{$vars['domain']}</a> / <a href='clientarea.php?action=productdetails&id={$_GET['id']}&modop=custom&a=rdns'>rDns</a>";
   } else {
      $var['rdns_ip'] = 'no';
   }

   if( isset( $_POST['ip'], $_POST['ttl'], $_POST['content'] ) ) {
      $ips = $_POST['ip'];
      $record = $_POST['content'];
      $ttl = $_POST['ttl'];
      $var['post'] = 'yes';
      
      $arr = [];
      for( $i=0; $i < count( $ips ); $i++ ) {
         $arr[$i]['ip'] = $ips[$i];
         $arr[$i]['ttl'] = $ttl[$i];
         $arr[$i]['content'] = $record[$i];
      }
      $result = APIClient::setRDNS( ION_API, array( 'args' => json_encode( $arr ) ) );

      if( is_array( $result ) ) {
         $var['error'] = 'failed';
         $var['rdns_message'] = $result['error']['message'];
      } else {
         $var['error'] = 'success';
      }
      $sid = $vars['sid'];
      $ips = APIClient::serverAllIPs( ION_API, array( 'serverID' => $sid ) ) ;
      $ipi ='';
      $var['content'] = '';
      foreach( $ips as $ip ) {
         $ipi = APIClient::ipCalc( ION_API, array( 'ip' => $ip ) );
         $var['content'] .= '<tr><td><a href="clientarea.php?action=productdetails&id=' . $_GET['id'] . '&modop=custom&a=rdns&ip=' . urlencode( $ip ) . '">' . $ip . '</a></td><td>' . correct( $ipi['primary_IP'], $ip ) . '</td><td>' . correct( $ipi['last_IP'], $ip ) . '</td></tr>';
      }
   } else if( isset( $_GET['ip'] ) ) {
      $sid = $vars['sid'];
      $ips = APIClient::serverAllIPs( ION_API, array( 'serverID' => $sid ) );
      $flag = in_array( $_GET['ip'], $ips );
      $var['ip'] = $_GET['ip'];
      $var['ip_decode'] = urlencode($_GET['ip']);
      if( $flag ) {
         $rdns = APIClient::getRDNS( ION_API, array( 'ip' => $_GET['ip'] ) ) ;
         if( isset( $rdns['error'] ) ) {
            $var['fetch'] = 'no';
         } else {
            $var['fetch'] = 'yes';
            $var['record'] = '';
            foreach( $rdns as $k => $rr ) {
               $var['record'] .= '<tr><td style="vert-align: middle;">' . $rr['ip'] . '<input type="hidden" value="' . $rr['ip'] .'" name="ip[]"></td>';
               $var['record'] .= '<td><input class="form-control" style="width:90%;" type="text" value="' . ($rr['ttl'] != '' ? $rr['ttl'] : '14400') . '" name="ttl[]"></td>';
               $var['record'] .= '<td><input type="text" value="' . $rr['content'] . '" name="content[]" style="width:90%;" class="form-control"></td></tr>';
            }
         }
      } else {
         $var['error'] = 'access';
      }
   } else {
      $sid = $vars['sid'];
      $ips = APIClient::serverAllIPs( ION_API, array( 'serverID' => $sid ) ) ;
      $ipi ='';
      $var['content'] = '';
      foreach( $ips as $ip ) {
         $ipi = APIClient::ipCalc( ION_API, array( 'ip' => $ip ) );
         $var['content'] .= '<tr><td><a href="clientarea.php?action=productdetails&id=' . $_GET['id'] . '&modop=custom&a=rdns&ip=' . urlencode( $ip ) . '">' . $ip . '</a></td><td>' . correct( $ipi['primary_IP'], $ip ) . '</td><td>' . correct( $ipi['last_IP'], $ip ) . '</td></tr>';
      }
   }
   return $var;
}

function correct( $fl, $ip ) {
   if( isset( $fl ) )
      return $fl;
   else
      return $ip;
}

add_hook('ClientAreaPage', 1, 'vpn_hooks');
add_hook('ClientAreaPage', 1, 'graph_hooks');
add_hook('ClientAreaPage', 1, 'rdns_hooks');
