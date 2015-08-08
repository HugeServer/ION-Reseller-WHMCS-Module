<?php
include 'config.php';
require_once __DIR__ . "/functions.php";
include "api.php";
include 'hooks.php';

function ion_ConfigOptions() {
    $configArray = array();
    return $configArray;
}

function ion_AdminCustomButtonArray() {
    $buttonArray = array(
        "Reboot" => "reboot",
    );
    return $buttonArray;
}


function ion_AdminServicesTabFields( $params ) {
    $hostname = urlencode($params['domain']);
    $pid = ion_mcrypt( $params['serviceid'] );

    $fieldsarray = array();
    //-------------------Server ID
    $ion_sid = null;
    $result = select_query( 'ion_module', 'ion_sid', array( 'whmcs_sid' => $params['serviceid'] ) );
    if( mysql_num_rows( $result ) > 0 ) {
        $ion_sid = mysql_fetch_array( $result )[0];
        $hash_ion_sid = ion_mcrypt($ion_sid);
    }

    $sList = APIClient::serverList( ION_API );
    if(key($sList) == 'error') {
       return '';
    }
    $sl = '';
    foreach( $sList as $v ) {
        if( $ion_sid == $v['server_id'] )
            $sl .= "<option selected='selected' value='{$v['server_id']}'>{$v['server_id']} - {$v[hostname]}</option>";
        else
            $sl .= "<option value='{$v['server_id']}'>{$v['server_id']} - {$v['hostname']}</option>";
    }

    if( is_null( $ion_sid ) ) {
        return array( 'Server List' => '<select name="ion_sid"><option value="0">None</option>' . $sl . '</select> <span style="color: red;">* Please select associated server</span>');
    }

    $fieldsarray['Associated Server'] = '<select name="ion_sid"  style="min-width: 300px;"><option value="0">None</option>' . $sl . '</select>';

   $clients = APIClient::resellerUserList( ION_API );

   $access = null;
   if(key($clients) !== 'error') {
      $cl = '';
      $ruid = '';
      $comment = '<span style="color:red;">* Required For Client VPN</span>';
      $result = select_query( 'ion_module', 'ruid,acl', array( 'ion_sid' => $ion_sid, 'whmcs_sid' => $params['serviceid'] ) );
      if( mysql_num_rows( $result ) > 0 ) {
         $row = mysql_fetch_array( $result, MYSQL_NUM );
         $ruid = $row[0];
         $acl = unserialize( $row[1] );
         $fvpn       = ($acl['vpn'])         ? 'checked' : '';
         $fsi        = ($acl['server_info']) ? 'checked' : '';
         $fsos       = ($acl['server_os'])   ? 'checked' : '';
         $fgraph     = ($acl['bw_graph'])    ? 'checked' : '';
         $frdns      = ($acl['rdns'])        ? 'checked' : '';
         $fipmi      = ($acl['ipmi'])        ? 'checked' : '';
         $fstatics   = ($acl['bw_statics'])  ? 'checked' : '';
         $fip        = ($acl['ip'])          ? 'checked' : '';
         $freboot    = ($acl['reboot'])      ? 'checked' : '';
      }
      foreach( $clients as $v ) {
         if( $ruid == $v['id'] ) {
            $comment = '';
            $cl .= "<option selected='selected' value='{$v['id']}'>{$v['id']} - {$v['email']}</option>";
         } else {
            $cl .= '<option value="'. $v['id'] .'">' . $v['id'] . ' - ' . $v['email'] . '</option>';
         }
      }
      $access = "<label for='ch_vpn'>VPN</label> <input type='checkbox' name='ch_vpn' id='ch_vpn' {$fvpn}> <label for='ch_ipmi'>IPMI</label> <input type='checkbox' name='ch_ipmi' id='ch_ipmi' {$fipmi}> <label for='ch_graph'>Bandwidth Graph</label> <input type='checkbox' name='ch_graph' id='ch_graph' {$fgraph}> <label for='ch_statics'>Bandwidth Statics</label> <input type='checkbox' name='ch_statics' id='ch_statics' {$fstatics}> <label for='ch_os'>OS</label> <input type='checkbox' name='ch_os' id='ch_os' {$fsos}> <label for='ch_ip'>IP</label> <input type='checkbox' name='ch_ip' id='ch_ip' {$fip}> <label for='ch_reboot'>Reboot</label> <input type='checkbox' name='ch_reboot' id='ch_reboot' {$freboot}> <label for='ch_info'>Server Info</label> <input type='checkbox' name='ch_info' id='ch_info' {$fsi}> <label for='ch_rdns'>rDNS</label> <input type='checkbox' name='ch_rdns' id='ch_rdns' {$frdns}> ";
      $fieldsarray['Reseller User ID'] = '<select name="ruid"  style="min-width: 300px;"><option value="0">None</option>' . $cl . '</select> ' . $comment;
   }
   echo '3';

   $serverInfo = APIClient::serverInfo( ION_API, array( 'serverID' => $ion_sid ) );
   if(key($serverInfo) != 'error') {
      $fieldsarray['Server Specification'] = "<table class='datatable' width='40%'><tbody><tr><th width='30%'>Name</th><th>Value</th></tr></tbody><tr><td>Hostname:</td><td>".ion_metafilter($serverInfo['hostname']) .  "</td></tr><tr><td>Chassis</td><td>" . ion_metafilter($serverInfo['chassis']) . "</td></tr><tr><td>Main board</td><td>" . ion_metafilter($serverInfo['mainboard']) . "</td></tr><tr><td>Processor</td><td>" . ion_metafilter($serverInfo['processor']) . "</td></tr><tr><td>Memory</td><td>" . ion_metafilter($serverInfo['memory']) . "</td></tr><tr><td>Total Memory</td><td>" . ion_metafilter($serverInfo['total_memory']) . "</td></tr><tr><td>Internal Hard Drive:</td><td>" . ion_metafilter($serverInfo['drive0']) . "</td></tr><tr><td>Drive1</td><td>" . ion_metafilter($serverInfo['drive1']) . "</td></tr><tr><td>Drive2</td><td>" . ion_metafilter($serverInfo['drive2']) . "</td></tr><tr><td>Drive3</td><td>" . ion_metafilter($serverInfo['drive3']) . "</td></tr><tr><td>Drive4</td><td>" . ion_metafilter($serverInfo['drive4']) . "</td></tr><tr><td>Raid Level</td><td>" . ion_metafilter($serverInfo['raid_level']) . "</td></tr><tr><td>Uplink</td><td>" . ion_metafilter($serverInfo['uplink']) . "</td></tr></table>";
   }

    $os = APIClient::serverOS( ION_API, array( 'serverID' => $ion_sid ) );
    if(key($os) != 'error') {
       $fieldsarray['Server OS'] = (!is_array($os)) ? '<b>' . $os . '</b>' : '';
    }

    $ip = APIClient::serverAllIPs( ION_API, array( 'serverID' => $ion_sid ) );
    if(key($ip) != 'error') {
       $ips = '';
       foreach( $ip as $v ) {
           $ips .= '<tr><td>' . $v . '</td></tr>';
       }
       $fieldsarray['Server IPs'] = ($ips != '') ? '<table class="datatable" width="50%"><tbody><tr><th width="30%">IP</th></tr></tbody>' . $ips . '</table>' : '';
    }

    $bws = APIClient::serverBwStatics( ION_API, array( 'serverID' => $ion_sid, 'period' => 'current' ) ) ;
    if(key($clients) != 'error') {
       $bws['method'] = ( $bws['method'] == '95th' ) ? '95th Percentile' : $bws['method'];
       $pbws = APIClient::serverBwStatics( ION_API, array( 'serverID' => $ion_sid, 'period' => 'prev' ) ) ;
       $pbws['method'] = ( $pbws['method'] == '95th' ) ? '95th Percentile' : $pbws['method'];

       $bws['95th_outbound'] = formatBytes($bws['95th_outbound']);
       $pbws['95th_outbound'] = formatBytes($pbws['95th_outbound']);
       $bws['95th_inbound'] = formatBytes($bws['95th_inbound']);
       $pbws['95th_inbound'] = formatBytes($pbws['95th_inbound']);
       $bws['outbound_traffic'] = formatBytes($bws['outbound_traffic']);
       $pbws['outbound_traffic'] = formatBytes($pbws['outbound_traffic']);
       $bws['inbound_traffic'] = formatBytes($bws['inbound_traffic']);
       $pbws['inbound_traffic'] = formatBytes($pbws['inbound_traffic']);
       $fieldsarray['Bandwidth Statics'] = "<table class=\"datatable\" width='100%'><tr><th width='30%' ></th><th width='30%'>Current</th><th width='30%'>Previous</th></tr><tr><td>Date Range:</td><td>{$bws['date_range']}</td><td>{$pbws['date_range']}</td></tr><tr><td>Included Bandwidth:</td><td>{$bws['included_bandwidth']}</td><td>{$pbws['included_bandwidth']}</td></tr><tr><td>Inbound Traffic:</td><td>{$bws['inbound_traffic']}</td><td>{$pbws['inbound_traffic']}</td></tr><tr><td>Outbound Traffic:</td><td>{$bws['outbound_traffic']}</td><td>{$pbws['outbound_traffic']}</td></tr><tr><td>95th Inbound:</td><td>{$bws['95th_inbound']}</td><td>{$pbws['95th_inbound']}</td></tr><tr><td>95th Outbound:</td><td>{$bws['95th_outbound']}</td><td>{$pbws['95th_outbound']}</td></tr><tr><td>Overage Rate:</td><td>{$bws['overage_rate']}</td><td>{$pbws['overage_rate']}</td></tr></table>";
    }

   $fieldsarray['IPMI Console'] = '<b> <input type="button" onclick="location.href=\'../modules/servers/ion/ipmi.php?hostname=' . $hostname . '&sid='. $hash_ion_sid .'\'" value="Launch IPMI Console"> * VPN Connection is required.</b>';
   $fieldsarray['Bandwidth Graph'] = '<br><script>function showGraph() {var periodel = document.getElementById("period");var gimg = document.getElementById("gimg");var url;var period = periodel.options[periodel.selectedIndex].value;gimg.src ="../modules/servers/ion/graph.php?pid=' . $pid . '&title=' . $hostname . '&period=" + period;}</script><img id="gimg" width="65%" src="../modules/servers/ion/graph.php?period=hour&title=' . $hostname . '&pid=' . $pid . '"><br><br><b>Period:</b> <select style="width: 200px;" id="period" onchange="showGraph()"><option value="hour">Hour</option><option value="day">Day</option><option value="week">Week</option><option value="month">Month</option></select><br><br>';
   if(!is_null($access)) {
      $fieldsarray['Access List'] = $access;
   }

   return $fieldsarray;
}

function ion_metafilter( $var ) {
    return (($var == 'select' || $var == 'none' || $var == '')?('-'):($var));
}

function ion_AdminServicesTabFieldsSave($params) {
    require( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . '../../../configuration.php');
    global $accessList;


    $db_conn = mysql_connect( $db_host, $db_username, $db_password );
    if ( empty( $db_conn ) ) throw new Exception( 'Unable to connect to DB' );

    $db_select = @mysql_select_db( $db_name, $db_conn );
    if ( empty( $db_select ) ) throw new Exception( 'Unable to select WHMCS database' );
    $vpn    = isset($_POST['ch_vpn'])       ? '1' : '0';
    $si     = isset($_POST['ch_info'])      ? '1' : '0';
    $ipmi   = isset($_POST['ch_ipmi'])      ? '1' : '0';
    $rdns   = isset($_POST['ch_rdns'])      ? '1' : '0';
    $ip     = isset($_POST['ch_ip'])        ? '1' : '0';
    $os     = isset($_POST['ch_os'])        ? '1' : '0';
    $reboot = isset($_POST['ch_reboot'])    ? '1' : '0';
    $bws    = isset($_POST['ch_statics'])   ? '1' : '0';
    $graph  = isset($_POST['ch_graph'])     ? '1' : '0';

    $acl    = array(
        'server_info'   => $si,
        'vpn'           => $vpn,
        'server_os'     => $os,
        'bw_graph'      => $graph,
        'rdns'          => $rdns,
        'ipmi'          => $ipmi,
        'bw_statics'    => $bws,
        'ip'            => $ip,
        'reboot'        => $reboot,
    );

    $query = "CREATE TABLE IF NOT EXISTS ion_module ( `whmcs_sid` int(11) NOT NULL, `ion_sid` int(11) NOT NULL, `ruid` int(11) NOT NULL, `acl` varchar(512) DEFAULT '" . serialize($accessList) . "')";
    $result = mysql_query( $query );

    mysqli_close( $db_conn );

    $whmcs_sid  = (int) $params['serviceid'];
    $ion_sid    = (int) $_POST['ion_sid'];
    $ruid       = isset( $_POST['ruid'] ) ? $_POST['ruid'] : 0;

    if( $ion_sid == 0 ) {
        full_query("DELETE FROM ion_module where whmcs_sid = " . $whmcs_sid );
        return;
    }

    $result = full_query( "SELECT * FROM ion_module where ion_sid = {$ion_sid} OR whmcs_sid = {$whmcs_sid}" );

    if( mysql_num_rows( $result ) > 0 ) {
        $result = full_query("DELETE FROM ion_module where whmcs_sid = {$whmcs_sid} OR ion_sid = {$ion_sid}");
        insert_query( 'ion_module', array(
            "whmcs_sid" => $whmcs_sid,
            "ion_sid"   => $ion_sid,
            "ruid"      => $ruid,
            "acl"       => serialize( $acl)
        ) );
        //$result = full_query("UPDATE ion_module set whmcs_sid = {$whmcs_sid},ion_sid = {$ion_sid} where whmcs_sid = {$whmcs_sid} or ion_sid = {$ion_sid} ");
    } else {
        insert_query( 'ion_module', array(
            "whmcs_sid" => $whmcs_sid,
            "ion_sid"   => $ion_sid,
            "ruid"      => $ruid,
            "acl"       => serialize( $accessList )
        ) );
    }
}

/*------------------------------Client*/

function ion_ClientArea($params) {
    $result = select_query( 'ion_module', 'ion_sid,acl', array( 'whmcs_sid' => $params['serviceid'] ) );
    $ion_sid = null;

    if( mysql_num_rows( $result ) > 0 ) {
        $row = mysql_fetch_array( $result , MYSQL_NUM );
        $ion_sid = $row[0];
        $acl = unserialize( $row[1] );
    } else {
        return;
    }

    //------------APIs
    $ip = APIClient::serverAllIPs( ION_API, array( 'serverID' => $ion_sid ) );
    if(key($ip) !== 'error') {
       $ips = '';
       foreach( $ip as $v ) {
          $ips .= $v . '<br>';
       }
       if( isset( $_GET['reboot'] ) ) {
          if( $_GET['reboot'] == "success" ) {
             echo "<script>alert('Reboot Successful.')</script>";
          } else if ( $_GET['reboot'] == "failed" ) {
             echo "<script>alert('Operation Failed! Please try again.')</script>";
          }
       }
    } else {
       $acl['ip'] = 0;
    }

    $os = APIClient::serverOS( ION_API, array( 'serverID' => $ion_sid ) );
    if(key($os) == 'error') {
       $acl['os'] = 0;
    }

    $serverInfo = APIClient::serverInfo( ION_API, array( 'serverID' => $ion_sid ) );
    if(key($serverInfo) == 'error') {
       $acl['server_info'] = 0;
    }

    $bws = APIClient::serverBwStatics( ION_API, array( 'serverID' => $ion_sid, 'period' => 'current', 'title' => $params['domain'] ) ) ;
    if(key($bws) !== 'error') {
       $bws['method'] = ( $bws['method'] == '95th' ) ? '95th Percentile' : $bws['method'];
       $pbws = APIClient::serverBwStatics( ION_API, array( 'serverID' => $ion_sid, 'period' => 'prev' ) ) ;
       $pbws['method'] = ( $pbws['method'] == '95th' ) ? '95th Percentile' : $pbws['method'];

       $bws['95th_outbound'] = formatBytes($bws['95th_outbound']);
       $pbws['95th_outbound'] = formatBytes($pbws['95th_outbound']);
       $bws['95th_inbound'] = formatBytes($bws['95th_inbound']);
       $pbws['95th_inbound'] = formatBytes($pbws['95th_inbound']);
       $bws['outbound_traffic'] = formatBytes($bws['outbound_traffic']);
       $pbws['outbound_traffic'] = formatBytes($pbws['outbound_traffic']);
       $bws['inbound_traffic'] = formatBytes($bws['inbound_traffic']);
       $pbws['inbound_traffic'] = formatBytes($pbws['inbound_traffic']);
    } else {
       $acl['bw_statics'] = 0;
    }

    $button  = ($acl['reboot'])     ? '<a class="btn btn-danger" style="margin:10px;" href="modules/servers/ion/reboot.php?sid=' . ion_mcrypt( $ion_sid ) . "' onclick=\"return confirm('Are you sure to Reboot Server?');\">Reboot</a>" : '';
    $button .= ($acl['ipmi'])       ? '<a class="btn btn-success" style="margin:10px;" href="modules/servers/ion/ipmi.php?sid=' . ion_mcrypt( $ion_sid ) . '&hostname=' . urlencode($params['domain']) . '">IPMI</a>' : '';

    $button .= ($acl['vpn'])        ? "<a class='btn btn-primary' style='margin:10px;' href='clientarea.php?action=productdetails&id={$_GET['id']}&modop=custom&a=vpn'>Set VPN</a>" : '';
    $button .= ($acl['rdns'])       ? "<a class='btn btn-warning' style='margin:10px;' href='clientarea.php?action=productdetails&id={$_GET['id']}&modop=custom&a=rdns'>rDNS</a>" : '';
    $button .= ($acl['bw_graph'])   ? "<a class='btn btn-info' style='margin:10px;' href='clientarea.php?action=productdetails&id={$_GET['id']}&modop=custom&a=graph'>Bandwidth Graph</a>" : '';
    $code = '';
    $code .= ($acl['server_info']) ? "<p><h4 style='margin-bottom: 20px;'>Server Specification</h4><span><table width='100%' style='text-align: left;'><tr><th>Host Name:</th><td>".ion_metafilter($serverInfo['hostname']) .  "</td></tr><tr><th>Chassis</th><td>" . ion_metafilter($serverInfo['chassis']) . "</td></tr><tr><th>Main board</th><td>" . ion_metafilter($serverInfo['mainboard']) . "</td></tr><tr><th>Processor</th><td>" . ion_metafilter($serverInfo['processor']) . "</td></tr><tr><th>Memory</th><td>" . ion_metafilter($serverInfo['memory']) . "</td></tr><tr><th>Total Memory</th><td>" . ion_metafilter($serverInfo['total_memory']) . "</td></tr><tr><th>Internal Hard Drive:</th><td>" . ion_metafilter($serverInfo['drive0']) . "</td></tr><tr><th>Drive1</th><td>" . ion_metafilter($serverInfo['drive1']) . "</td></tr><tr><th>Drive2</th><tD>" . ion_metafilter($serverInfo['drive2']) . "</td></tr><tr><th>Drive3</th><td>" . ion_metafilter($serverInfo['drive3']) . "</td></tr><tr><th>Drive4</th><td>" . ion_metafilter($serverInfo['drive4']) . "</td></tr><tr><th>Raid Level</th><td>" . ion_metafilter($serverInfo['raid_level']) . "</td></tr><tr><th>Uplink</th><td>" . ion_metafilter($serverInfo['uplink']) . "</td></tr></table></span></p>" : '';
    $code .= ($acl['server_os'])   ? "<div class='container row-fluid' style='text-align: left;margin-top: 25px;padding-top: 25px;border-top: 1px solid #ccc;'><div class='col-md-6 span6'><h4>Server IPs: </h4><span>{$ips}</span></div>" : '<div>';
    $code .= ($acl['ip'])          ? "<div class='col-md-6 span6'><h4>Server OS: </h4><span>{$os}</span></div></div>" : '</div>';
    $code .= ($acl['bw_statics'])  ? "<p style='margin-top: 25px;padding-top:25px;border-top: 1px solid #ccc;'><h4 style='margin-bottom: 20px;'>Bandwidth Statics:</h4><span><table width='100%' style='text-align: left;'><tr><th width='30%'></th><th width='30%'>Current</th><th width='30%'>Previous</th></tr><tr><th>Date Range:</th><td>{$bws['date_range']}</td><td>{$pbws['date_range']}</td></tr><tr><th>Included Bandwidth:</th><td>{$bws['included_bandwidth']}</td><td >{$pbws['included_bandwidth']}</td></tr><tr><th>Inbound Traffic:</th><td>{$bws['inbound_traffic']}</td><td>{$pbws['inbound_traffic']}</td></tr><tr><th>Outbound Traffic:</th><td>{$bws['outbound_traffic']}</td><td>{$pbws['outbound_traffic']}</td></tr><tr><th>95th Inbound:</th><td>{$bws['95th_inbound']}</td><td>{$pbws['95th_inbound']}</td></tr><tr><th>95th Outbound:</th><td>{$bws['95th_outbound']}</td><td>{$pbws['95th_outbound']}</td></tr></table></span>" : '';
    $code .=                         "<p style='margin-top: 25px;padding-top:25px;border-top: 1px solid #ccc;'>{$button}</p>";
    return $code;
}


function ion_ClientAreaCustomButtonArray( $params ) {
    $result = select_query( 'ion_module', 'acl', array( 'whmcs_sid' => $params['serviceid'] ) );

    if( mysql_num_rows( $result ) > 0 ) {
        $acl = unserialize( mysql_fetch_array( $result , MYSQL_NUM )[0] );
    } else {
        return;
    }
    $button = [];

    $button['Bandwidth Graph']    = ($acl['bw_graph'])    ? 'graph'   : null;
    $button['Set VPN']              = ($acl['vpn'])         ? 'vpn'     : null;
    $button['rDNS']                 = ($acl['rdns'])        ? 'rdns'    : null;
    //$button['IPMI']                 = ($acl['ipmi'])        ? 'ipmi'    : null;
    //$button['Server Reboot']        = ($acl['reboot'])      ? 'reboot'  : null;

    foreach( $button as $k => $v ) {
        if(is_null( $v ) )
            unset( $button[$k] );
    }

    return $button;
}

function TerminateAccount( $params ) {
    $result = select_query( 'ion_module', 'ion_sid', array( 'whmcs_sid' => $params['serviceid'] ) );
    $ion_sid = 0;
    if( mysql_num_rows( $result ) > 0 ) {
        $ion_sid = mysql_fetch_array( $result, MYSQL_NUM )[0];
    }
    if( $ion_sid == 0 )
        return;
    return APIClient::requestCancellation( ION_API, array( 'serverID' => $ion_sid, 'message' => 'This server has been cancelled through our billing system and we are no longer being paid by our client for this server. Please cancel it.' ) );
}


//------------Functions

function ion_graph($params) {
    //-------------------------------
    $pagearray = array(
        'templatefile' => 'graph',
        'vars'          => array(
            'domain'    => $params['domain'],
            'sid'       => $params['serviceid'],
            'hostname'  => urlencode($params['domain']),
        )
    );
    return $pagearray;
}

function ion_rdns( $params ) {
    $result = select_query( 'ion_module', 'ion_sid', array( 'whmcs_sid' => $params['serviceid'] ) );
    $ion_sid = 0;
    if( mysql_num_rows( $result ) > 0 ) {
        $ion_sid = mysql_fetch_array( $result, MYSQL_NUM )[0];
    }
    if( $ion_sid == 0 )
        return;
    //-------------------------------
    $pagearray = array(
        'templatefile'  => 'rdns',
        'vars'          => array(
            'sid'   => $ion_sid,
            'domain'    => $params['domain']
        )
    );
    return $pagearray;
}

function ion_vpn( $params ) {
    $result = select_query( 'ion_module', 'ruid', array( 'whmcs_sid' => $params['serviceid'] ) );
    $ruid = 0;
    if( mysql_num_rows( $result ) > 0 ) {
        $ruid = mysql_fetch_array( $result )[0];
        $ruid = ( $ruid == 0 ) ? 'error' : $ruid;
    } else {
        $ruid = 'error';
    }

    $pagearray = array(
        'templatefile' => 'vpn',
        'vars' => array(
            'ruid'      => $ruid,
            'domain'    => $params['domain']
        ),
    );
    return $pagearray;
}
function ion_reboot( $params ) {
    $result = select_query( 'ion_module', 'ion_sid', array( 'whmcs_sid' => $params['serviceid'] ) );
    $ion_sid = 0;
    if( mysql_num_rows( $result ) > 0 ) {
        $ion_sid = mysql_fetch_array($result)[0];
    }
    if( $ion_sid == 0 )
        return;
    if( APIClient::serverReboot( ION_API, array( 'serverID' => $ion_sid ) ) )
        return 'success';
    else
        return 'Error. please try again.';


}
