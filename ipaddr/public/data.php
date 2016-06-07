<?php
/*
*/
define("MSHOST", "dca-05.ms.cogentco.com");
define("MSUSER", "AdminSF");
define("MSPASS", "adminsf01");

// initialize status and result
$OK = false;
$DATA = array('ok' => false, 'msg' => array(), 'data' => array(), 'user' => isset($_SERVER["PHP_AUTH_USER"]) ? str_replace("@ms.cogentco.com","",strtolower($_SERVER["PHP_AUTH_USER"])) : false);

//if(isset($_SERVER["PHP_AUTH_USER"]))
//{
  // check arguments
  if(isset($_GET['cmd']))
  {
    switch($_GET['cmd'])
    {
      case 'list':
        $FILTER = array();
        if(isset($_GET['name']) && "" != $_GET['name']) $FILTER[]=sprintf("[CustomerName] LIKE '%%%s%%'", SF_MSSQL_quote(trim($_GET['name'])));
        if(isset($_GET['glid']) && "" != $_GET['glid']) $FILTER[]=sprintf("[GlobalLogoID] LIKE '%%%s%%'", SF_MSSQL_quote(trim($_GET['glid'])));
        if(isset($_GET['orderid']) && "" != $_GET['orderid']) $FILTER[]=sprintf("[OrderID] LIKE '%%%s%%'", SF_MSSQL_quote(trim($_GET['orderid'])));
        if(0 < count($FILTER)) { $OK=true; } else { $DATA['msg'][]="no filter given"; }
       break;
      case 'ports':
      case 'ip4alloc':
        if(!isset($_GET['glid']) || "" == $_GET['glid']) { $DATA['msg'][]="no GLID given"; } else { $OK=true; }
       break;
      default:
       $DATA['msg'][]="wrong parameter value";
    }
  } else $DATA['msg'][]="missing parameter";

  if($OK)
  {
    // connect to the database
    if(FALSE!==($msconn=mssql_connect(MSHOST, MSUSER, MSPASS, "")))
    {
      switch($_GET['cmd'])
      {
        case 'list':
          // get accounts
          $query = sprintf("SELECT DISTINCT [GlobalLogoID],[CustomerName] FROM [TLG].[dbo].[V_SDScoreCard] WHERE (%s) AND [ProvOrderStatus]='Completed' AND ([OrderCancelDt] IS NULL OR GETUTCDATE() < [OrderCancelDt]) AND [InterfaceTypeInt] IN (SELECT [InterfaceTypeID] FROM [TLG].[dbo].[OPMInterfaceType] WHERE 0 < CASE WHEN [Bandwidth] IS NULL THEN 0 WHEN 'N/A'=[Bandwidth] THEN 0 ELSE CAST([Bandwidth] AS DECIMAL(12,3)) END) AND [Layer] = 3", join(" OR ", $FILTER));
          if(FALSE!==($res=mssql_query($query, $msconn)))
          {
            if(!mssql_num_rows($res))
            {
              $DATA['msg'][]="no account with active L3 port order found";
            }
            else
            {
              $DATA['custcnt']=mssql_num_rows($res);
              while($row=mssql_fetch_assoc($res)) { $DATA['data'][]=array('glid' => $row['GlobalLogoID'], 'name' => $row['CustomerName']); }
              $DATA['ok']=true;
            }
            mssql_free_result($res);
          } else { $DATA['msg'][]=mssql_get_last_message(); }
         break;
        // get port orders
        case 'ports':
          $query = sprintf("SELECT [OrderID],[Region],[Market],[ServiceAddress],[CDR],[ProductCode] FROM [TLG].[dbo].[V_SDScoreCard] WHERE [GlobalLogoID]='%s' AND [ProvOrderStatus]='Completed' AND ([OrderCancelDt] IS NULL OR GETUTCDATE() < [OrderCancelDt]) AND [InterfaceTypeInt] IN (SELECT [InterfaceTypeID] FROM [TLG].[dbo].[OPMInterfaceType] WHERE 0 < CASE WHEN [Bandwidth] IS NULL THEN 0 WHEN 'N/A'=[Bandwidth] THEN 0 ELSE CAST([Bandwidth] AS DECIMAL(12,3)) END) AND [Layer] = 3", SF_MSSQL_quote($_GET['glid']));
          if(FALSE!==($res=mssql_query($query, $msconn)))
          {
            if(!mssql_num_rows($res))
            {
              $DATA['msg'][]="no port orders for ".$_GET['glid']." found";
            }
            else
            {
              $DATA['portcnt']=mssql_num_rows($res);
              $CDRs=array(); $CDRSUM=0;
              while($row=mssql_fetch_assoc($res))
              {
                $REGION=trim($row['Region']);
                $MARKET=trim($row['Market']);
                if(!isset($CDRs[$REGION])) $CDRs[$REGION]=array();
                if(!isset($CDRs[$REGION][$MARKET])) $CDRs[$REGION][$MARKET]=0;
                $CDRs[$REGION][$MARKET]+=floatval($row['CDR']);
                //--
                if(!isset($ORDERs[$REGION])) $ORDERs[$REGION]=array();
                if(!isset($ORDERs[$REGION][$MARKET])) $ORDERs[$REGION][$MARKET]=array();
                $ORDERs[$REGION][$MARKET][]=array('orderid' => trim($row['OrderID']), 'address' => trim($row['ServiceAddress']), 'cdr' => floatval($row['CDR']), 'pcode' => trim($row['ProductCode']));
              }
              $id=0;
              foreach(array_keys($CDRs) as $REGION)
               foreach(array_keys($CDRs[$REGION]) as $MARKET)
               {
                 $pid=$id;
                 $DATA['data'][]=array('id' => sprintf("%d", $id++), 'region' => $REGION, 'market' => $MARKET, 'cdrsum' => $CDRs[$REGION][$MARKET], 'pcode' => '', 'level' => '0', 'parent' => '', 'isLeaf' => false, 'expanded' => false, 'loaded' => true);
                 if(0 < count($ORDERs[$REGION][$MARKET]))
                  foreach($ORDERs[$REGION][$MARKET] AS $ORDER)
                   $DATA['data'][]=array('id' => sprintf("%d", $id++), 'region' => $ORDER['orderid'], 'market' => $ORDER['address'], 'cdrsum' => $ORDER['cdr'], 'pcode' => $ORDER['pcode'], 'level' => '1', 'parent' => sprintf("%d", $pid), 'isLeaf' => true, 'expanded' => false, 'loaded' => true);
                 $CDRSUM+=$CDRs[$REGION][$MARKET];
               }
              $DATA['footer']=array('region' => '', 'market' => 'Total CDR:', 'cdrsum' => $CDRSUM);
              $DATA['cdrsum']=$CDRSUM;
              $DATA['ok']=true;
            }
            mssql_free_result($res);
          } else { $DATA['msg'][]=mssql_get_last_message(); }
         break;
        // get IPv4 allocations
        case 'ip4alloc':
          $query = sprintf("SELECT [Starfish].[AdminSF].[INET_ITOR]([netaddr],[netmask]) AS [block],[orderid],CAST([count] AS INT) AS [count],[Price List] FROM [Starfish].[AdminSF].[ip_block] LEFT JOIN [TLG].[dbo].[V_SiebelOrders] ON [orderid]=[ORDER_NUM] WHERE [orderid] IN (SELECT [OrderID] FROM [TLG].[dbo].[V_SDScoreCard] WHERE [GlobalLogoID]='%s' AND [ProvOrderStatus]='Completed' AND ([OrderCancelDt] IS NULL OR GETUTCDATE() < [OrderCancelDt])) AND [version]=4 ORDER BY [orderid] ASC, [netmask] DESC", SF_MSSQL_quote($_GET['glid']));
          if(FALSE!==($res=mssql_query($query, $msconn)))
          {
            if(!mssql_num_rows($res))
            {
              $DATA['msg'][]="no IPv4 allocations for ".$_GET['glid']." found";
            }
            else
            {
              $ORDERs = array();
              $DATA['ipv4cnt'] = mssql_num_rows($res);
              $IP4SUM = 0;
              while($row = mssql_fetch_assoc($res))
              {
                $IP4ORDER = trim($row['orderid']);
                $IP4BLOCK = trim($row['block']);
                $IP4cCOUNT = intval($row['count']);
                $IP4pCOUNT = 0;
                $PCODE = trim($row['Price List']);
                if(FALSE === strpos($PCODE, "IPV4ALLOC") && !in_array($IP4ORDER,$ORDERs)) { if(4 == $IP4cCOUNT) { $IP4pCOUNT = 4; } else { $IP4pCOUNT = 8; } $IP4cCOUNT -= $IP4pCOUNT; $ORDERs[] = $IP4ORDER; }
                $DATA['data'][]=array('block' => $IP4BLOCK, 'orderid' => $IP4ORDER, 'ccount' => $IP4cCOUNT, 'pcount' => $IP4pCOUNT, 'pcode' => $PCODE);
                $IP4SUM += $IP4cCOUNT;
              }
              $DATA['footer'] = array('block' => 'Total IPv4', 'ccount' => sprintf("%d%s", $IP4SUM, (0 < $IP4SUM ? sprintf(" (/%.2f)", 32-(LOG($IP4SUM)/LOG(2))) : "")));
              $DATA['ipsum'] = $IP4SUM;
              $DATA['ok'] = true;
            }
            mssql_free_result($res);
          } else { $DATA['msg'][]=mssql_get_last_message(); }
         break;
      }
      mssql_close($msconn);
    }
    else $DATA['msg'][]=mssql_get_last_message();
  }
//} else $DATA['msg'][]="not authenticated";

// return result(s)
print(json_encode($DATA));

// quote strings for MSSQL
function SF_MSSQL_quote($STRING) { return(str_replace(array(chr(39)), array(chr(39).chr(39)), $STRING)); }

?>
