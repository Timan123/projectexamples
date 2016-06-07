<!DOCTYPE html>
<html>
 <head>
  <meta charset="UTF-8">
  <title>Customer IPv4 usage</title>
  <style>
   body { background-color: white; margin: 0; padding: 3px; }
   table#search { border: none; }
   .ui-pager-control { padding-right: 4px; height: 100%; }
   div#gview_rules span.ui-jqgrid-title { display: block; }
   div#gview_rules span.ui-jqgrid-title div { display: inline-block; width: 50%; text-align: center; }
   td#pmore_right table.navtable { padding: 1px 4px; float: right; }
   td.pricing { text-align: center; font-weight:bold; padding-top:1em; }
   div#gbox_clist25 .footrow td:nth-child(2) { text-align: right; }
   table#clist25 > tbody > tr.legacy > td { background-color: #D3FFD3; }
   table#clist25 > tbody > tr.lowport > td { background-color: #A4C8EB; }
   table#clist25 > tbody > tr.highport > td { background-color: #FF8080; }
   .legend {font-family: Lucida Grande,Lucida Sans,Arial,sans-serif; font-size:12px}
   table#page { padding-bottom : 20px; }
   div#navlinks a { display : block; float : right ; padding : 4px 8px 4px 8px; }
  </style>
  <link rel="stylesheet" type="text/css" media="screen" href="css/redmond/jquery-ui.min.css" />
  <link rel="stylesheet" type="text/css" media="screen" href="css/ui.jqgrid.css" />
  <link rel="stylesheet" type="text/css" media="screen" href="css/jgrowl-1.12.2.css" />
  <script type="text/javascript" src="js/jquery-1.11.0.min.js"></script>
  <script type="text/javascript" src="js/jquery-ui-1.10.4.min.js"></script>
  <script type="text/javascript" src="js/i18n/grid.locale-en.js"></script>
  <script type="text/javascript" src="js/jquery.jqGrid.min.js"></script>
  <script type="text/javascript" src="js/jgrowl-1.12.2.js"></script>
 </head>
 <body>

  <div id="navlinks">
    <a href="/customers">Customers</a>
    <a href="/blocks">Blocks</a>
    <a href="/orders">Orders</a>
  </div>
  <table id="page" align="center">
   <tr><td colspan="2" align="center">
    <form name="IPUSE" id="ipuse">
     <table id="search" class="ui-widget-content">
      <tr><td colspan="2" align="center">NO WILDCARDS.</br>Partial matching done automatically.</td></tr>
      <tr><th class="ui-widget-header">Customer Name:</th><td><input type="text" name="name" /></td></tr>
      <tr><td colspan="2" align="center" class="ui-th-column">-- or --</td></tr>
      <tr><th class="ui-widget-header">Global Logo ID:</th><td><input type="text" name="glid" /></td></tr>
      <tr><td colspan="2" align="center">-- or --</td></tr>
      <tr><th class="ui-widget-header">Order ID:</th><td><input type="text" name="orderid" /></td></tr>
     </table>
    </form>
   </td></tr>
   <tr><td colspan="2" align="center">
    <table id="clist47"></table>
    <div id="plist47"></div>
   </td></tr>
   <tr><td valign="top">
    <table id="clist34"></table>
    <div id="plist34"></div>
   </td><td valign="top">
    <table id="clist25"></table>
    <div id="plist25"></div>
	<span class="legend">green=auto ipv4 alloc order, blue=small port order, red=large port order, should be billed</span>
   </td></tr>

   <tr>
    <td valign="top" align="center">
     <table id="rules"></table>
    </td>
    <td valign="middle" align="center">
     <table>
      <tr>
       <td><form name="MORE"><table id="more"></table><div id="pmore"></div></form></td>
       <td><form name="LESS"><table id="less"></table><div id="pless"></div></form></td>
       <td><form><input type="button" value="go!" /></form></td>
      </tr>
     </table>
    </td>
   </tr>
   
   <tr ><td colspan="2" class="pricing"><span id="CDR"></span></td></tr>
   <tr ><td colspan="2" class="pricing"><span id="IPSum"></span><br>
		   <span id="MRC"></span> <span id="Pricing"></span><br>
		   <span id="OutstandingMRC"></span><br>
		   <span id="ProjectLink"></span>
	   </td>
   </tr>
   
  </table>
  <script type="text/javascript">
    $(document).ready(function()
    {
      // --- set jGrowl defaults ---
      $.jGrowl.defaults.position = 'bottom-right';
      $.jGrowl.defaults.sticky = false;
      $.jGrowl.defaults.life = 10000;
      // --- account list ---
      jQuery("#clist47").jqGrid(
      {
        data: [],
        datatype: "local",
        height: "6",
        width: 'auto',
        colNames:['GLID','Account Name'],
        colModel:[ 
          {name:'glid',index:'glid', width:100, sorttype:"text"},
          {name:'name',index:'name', width:350, sorttype:"text"}
        ],
        scroll: 1,
        hidegrid: false,
        pager: "#plist47",
        viewrecords: true,
        caption: "Accounts with active L3 port order matching above filter",
        onSelectRow: function(rowid,status)
        {
          if(status)
          {
            var row = $('#clist47').jqGrid('getRowData',rowid);
            // reset and get the port order list
            CDRsum=0, IP4sum=0; MRC=0;
            CDRmore=0, IP4more=0; GLID=row.glid;
            $("#pricing").empty();
            $("div#plist34 td#plist34_right").empty();
            $("#more").jqGrid("clearGridData").jqGrid("setGridParam", { data: [ { cdr: '', mska: '' } ] }).trigger("reloadGrid");
            $("div#gview_clist34 tr.footrow td[aria-describedby='clist34_region']").empty();
            $("div#gview_clist34 tr.footrow td[aria-describedby='clist34_market']").empty();
            $("div#gview_clist34 tr.footrow td[aria-describedby='clist34_cdrsum']").empty();
            $("#clist34").jqGrid("clearGridData").trigger("reloadGrid");
            $("#load_clist34").show();
            $.get("data.php", { cmd: "ports", glid: GLID }, function(data)
            {
              $("#load_clist34").hide();
              if(data.ok)
              {
                $("#clist34").jqGrid("setGridParam", { datastr: JSON.stringify(data.data) }).trigger("reloadGrid");
                $("div#plist34 td#plist34_right").text(data.portcnt+" port"+(1 < data.portcnt ? "s" : ""));
                //CDRsum = data.cdrsum;
                //DisplayPricing();
              }
              else { $.jGrowl(data.msg.join("<br />"), { theme: 'error', header: 'get Port Orders failed' } ); }
              $("div#gview_clist34 tr.footrow td[aria-describedby='clist34_region']").text(data.footer.region);
              $("div#gview_clist34 tr.footrow td[aria-describedby='clist34_market']").text(data.footer.market);
              $("div#gview_clist34 tr.footrow td[aria-describedby='clist34_cdrsum']").text(data.footer.cdrsum);
            }, "json");
            // reset and get the IPv4 allocation list
            $("div#plist25 td#plist25_right").empty();
            $("#clist25").jqGrid("clearGridData").jqGrid("setGridParam", { userData: { block: '', ccount: ''} }).trigger("reloadGrid");
            $("#load_clist25").show();
            HitAPI(row.glid);
//			$.get("http://ipaddr/api/getIPInfo/"+row.glid, { }, function(data)
//            {
//              $("#load_clist25").hide();
//              if(data.ok)
//              {
//                $("#clist25").jqGrid("setGridParam", { data: data.data, userData: { pcode: 'Total IPv4', block: data.footer.ccount } }).trigger("reloadGrid");
//                $("div#plist25 td#plist25_right").text(data.ipv4cnt+" IPv4 allocation"+(1 < data.ipv4cnt ? "s" : ""));
//                IP4sum = (typeof data.ipsum !== 'undefined') ? data.ipsum : 0;
//                MRC = (typeof data.mrc !== 'undefined') ? parseFloat(data.mrc) : 0;
//                DisplayPricing();
//              }
//              else { $.jGrowl(data.msg.join("<br />"), { theme: 'error', header: 'get IPv4 allocations failed' } ); }
//            }, "json");
          }
        }
      });
      $("#clist47").parents('div.ui-jqgrid-bdiv').css("max-height","150px");
      $("#gview_clist47 div.ui-jqgrid-bdiv").css('height', 'auto');
      // --- form handling ---
      // *** submit on enter or change ***
      $("form#ipuse input").keypress(function(e) { if(13 == e.which) { $("form#ipuse").submit(); return false; } });
      // *** form submission ***
      $("form#ipuse").on("submit", function()
      {
        // reset account list
        $("div#plist47 td#plist47_right").empty();
        $("#clist47").jqGrid("clearGridData").jqGrid("setGridParam", {data: [] }).trigger("reloadGrid");
        $("#gview_clist47 div.ui-jqgrid-bdiv").css('height','6px');
        // reset port order list
        $("div#plist34 td#plist34_right").empty();
        $("div#gview_clist34 tr.footrow td[aria-describedby='clist34_region']").empty();
        $("div#gview_clist34 tr.footrow td[aria-describedby='clist34_market']").empty();
        $("div#gview_clist34 tr.footrow td[aria-describedby='clist34_cdrsum']").empty();
        $("#clist34").jqGrid("clearGridData").trigger("reloadGrid");
        // reset ipv4 allocation list
        $("div#plist25 td#plist25_right").empty();
        $("#clist25").jqGrid("clearGridData").jqGrid("setGridParam", { userData: { block: '', ccount: ''} }).trigger("reloadGrid");;
        // check for filter values or stop
        if("" != $.trim(document.IPUSE.name.value) || "" != $.trim(document.IPUSE.glid.value) || "" != $.trim(document.IPUSE.orderid.value))
        {
          $("#load_clist47").show();
          $.get("data.php", { cmd: "list", name: document.IPUSE.name.value, glid: document.IPUSE.glid.value, orderid: document.IPUSE.orderid.value }, function(data)
          {
            $("#load_clist47").hide();
            if(data.ok)
            {
              $("#clist47").jqGrid("setGridParam", { data: data.data }).trigger("reloadGrid");
              $("div#plist47 td#plist47_right").text(data.custcnt+" account"+(1 < data.custcnt ? "s" : "")+" with active L3 port(s) matched the filter");
            }
            else { $.jGrowl(data.msg.join("<br />"), { theme: 'error', header: 'get Account list failed' } ); }
            $("#gview_clist47 div.ui-jqgrid-bdiv").css('height',(0 == data.data.length ? '6px' : (5 < data.data.length ? '120px' : 'auto')));
          }, "json");
        }
        return false;
      });
      $("#plist47_left").remove();
      $("#plist47_center").remove();

      // --- port order list ---
      jQuery("#clist34").jqGrid(
      {
        datastr: [],
        datatype: "jsonstring",
        height: "auto",
        colNames:['Region','ProductCode','Market','CDR'],
        colModel:[ 
          {name:'region', index:'region', width:140, sorttype:"text"},
          {name:'pcode', index:'pcode', width:160, sorttype:"text"},
          {name:'market', index:'market', width:180, sorttype:"text"},
          {name:'cdrsum', index:'cdrsum', width:50, align:"right"}
        ],
        treeGrid: true,
        treeGridModel: "adjacency",
        ExpandColumn: "region",
        hidegrid: false,
        pager: "#plist34",
        viewrecords: false,
        caption: "port orders",
        footerrow: true
      }).jqGrid('navGrid',"#plist34",
      {
        edit:false, add:false, del:false, search:false, refresh:false, position:'left'
      }).jqGrid('navButtonAdd',"#plist34",
      {
        caption: "Expand/Collapse All",
        buttonicon: "ui-icon-triangle-2-n-s",
        onClickButton: function() { $("#clist34").find(".treeclick").trigger('click'); },
        position: "left",
        title: "Expand/Collapse All",
        cursor: "pointer"
      });
      $("#plist34_center").remove();
      $("div#gview_clist34 tr.footrow td[aria-describedby='clist34_market']").css('text-align','right');

      // --- IPv4 allocations list ---
      jQuery("#clist25").jqGrid(
      {
        data: [],
        userData: { pcode: '', block: '' },
        datatype: "local",
        height: "auto",
        colNames:['OrderID','ProductCode','Block','Port Order','','',''],
        colModel:[ 
          {name:'orderid', index:'orderid', width:100, sorttype:"text"},
          {name:'pcode', index:'pcode', width:160, sorttype:"text"},
          {name:'block', index:'block', width:110, align:"right", sorttype:"text"},
          {name:'port_order', index:'port_order', width:100, align:"right", sorttype:"text"},
          {name:'autoipv4order', hidden:true, index:'autoipv4order'},
		  {name:'interfacetypeint', hidden:true, index:'interfacetypeint'},
		  {name:'count', hidden:true, index:'count'}
        ],
        hidegrid: false,
        rowNum: 9999,
        pager: "#plist25",
        viewrecords: false,
        caption: "IPv4 allocations",
        footerrow: true,
        userDataOnFooter: true,
        afterInsertRow: function(id, data)
        {
          if(true===data.autoipv4order) { 
			  $('table#clist25 > tbody > tr:nth-child('+(parseInt(id)+1)+')').addClass('legacy'); 
		  }
		  if(data.interfacetypeint != 47 && data.count > 8) { $('table#clist25 > tbody > tr:nth-child('+(parseInt(id)+1)+')').addClass('highport'); }
		  if(data.interfacetypeint != 47 && data.count <= 8) { $('table#clist25 > tbody > tr:nth-child('+(parseInt(id)+1)+')').addClass('lowport'); }
		  
        }
      });
      $("#plist25_center").remove();

      // --- pricing rules ---
      jQuery("#rules").jqGrid(
      {
        data: [
         { cdr_from: '', cdr_to: '<1', standard: 'n/a', premium: 'Up to /19' },
         { cdr_from: 1, cdr_to: 100, standard: 'Up to /24', premium: 'Up to /19' },
         { cdr_from: 200, cdr_to: 900, standard: 'Up to /23', premium: 'Up to /19' },
         { cdr_from: 1000, cdr_to: 9000, standard: 'Up to /22', premium: 'Up to /19' },
         { cdr_from: '10,000', cdr_to: 'More', standard: 'Up to /21', premium: 'Up to /19' }
        ],
        datatype: "local",
        height: "auto",
        colNames:['From','To','Standard','Premium'],
        colModel:[ 
          {name:'cdr_from', index:'cdr_from', width:92, align:"center", sortable:false},
          {name:'cdr_to', index:'cdr_to', width:91, align:"center", sortable:false},
          {name:'standard', index:'stadard', width:91, align:"center", sortable:false},
          {name:'premium', index:'premium', width:91, align:"center", sortable:false}
        ],
        hidegrid: false,
        rowNum: 9999,
        viewrecords: false,
        caption: '<div>CDR in Mbps</div><div>IPv4 Maximum Allocation</div>'
      });

      // --- additional orders ---
      jQuery("#more").jqGrid(
      {
        data: [ { cdr: '', mska: '' } ],
        datatype: "local",
        height: "auto",
        colNames:['','CDR','IPs'],
        colModel:[ 
          {name:'num', index:'num', formatter:FormatterMoreNum, width:25, align:"center", sortable:false},
          {name:'cdr', index:'cdr', formatter:FormatterMoreCDR, width:90, align:"center", sortable:false},
          {name:'mska', index:'mska', formatter:FormatterMoreMSK, width:60, align:"center", sortable:false}
        ],
        hidegrid: false,
        rowNum: 9999,
        pager: "#pmore",
        viewrecords: false,
        caption: 'additional orders'
      }).jqGrid('navGrid',"#pmore",
      {
        edit:false, add:false, del:false, search:false, refresh:false, position:'left'
      }).jqGrid('navButtonAdd',"#pmore",
      {
        caption: "More",
        buttonicon: "ui-icon-plus",
        onClickButton: function() { var data=$("#more").jqGrid("getGridParam","data"); console.log(data); data.push({ cdr: '', mska: '' }); $("#more").jqGrid("setGridParam", { data: data }).trigger("reloadGrid"); },
        position: "right",
        title: "Add",
        cursor: "pointer"
      });
      $("#pmore_center").remove();
      $("#pmore_right table.navtable").css("float","right");

      // --- removal of orders ---
      jQuery("#less").jqGrid(
      {
        data: [ { cdr: '', mska: '' } ],
        datatype: "local",
        height: "auto",
        colNames:['','CDR','IPs'],
        colModel:[ 
          {name:'num', index:'num', formatter:FormatterLessNum, width:25, align:"center", sortable:false},
          {name:'cdr', index:'cdr', formatter:FormatterLessCDR, width:90, align:"center", sortable:false},
          {name:'mska', index:'mska', formatter:FormatterLessMSK, width:60, align:"center", sortable:false}
        ],
        hidegrid: false,
        rowNum: 9999,
        pager: "#pless",
        viewrecords: false,
        caption: 'removal of orders'
      }).jqGrid('navGrid',"#pless",
      {
        edit:false, add:false, del:false, search:false, refresh:false, position:'left'
      }).jqGrid('navButtonAdd',"#pless",
      {
        caption: "More",
        buttonicon: "ui-icon-plus",
        onClickButton: function() { var data=$("#less").jqGrid("getGridParam","data"); console.log(data); data.push({ cdr: '', mska: '' }); $("#less").jqGrid("setGridParam", { data: data }).trigger("reloadGrid"); },
        position: "right",
        title: "Add",
        cursor: "pointer"
      });
      $("#pless_center").remove();
      $("#pless_right table.navtable").css("float","right");
    });

    var CDRsum=0, IP4sum=0, MRC=0;
    var CDRmore=0, IP4more=0;
    function FormatterMoreNum(value, options, rowObject) { return('#' + options.rowId); }
    function FormatterMoreCDR(value, options, rowObject) { return('<input onchange="UpdateMore()" type="text" size="4" name="cdr'+(options.rowId-1)+'" value="'+(value)+'" /> Mbps'); }
    function FormatterMoreMSK(value, options, rowObject) { return('/ <input onchange="UpdateMore()" type="text" size="2" maxlength="2" name="msk'+(2==options.pos ? 'a' : 'b')+(options.rowId-1)+'" value="'+(value)+'" />'); }
    function UpdateMore()
    {
      var fname, fvalue, fok, REG = /[\d\s]+/;
      var data=$("#more").jqGrid("getGridParam","data");
      CDRmore=0; IP4more=0;
      for(var r=0, rmax=data.length; r < rmax; r++)
      {
        fname='cdr'+r; fvalue=0;
        if(document.MORE.elements[fname])
        {
          fok=false;
          if(''==document.MORE.elements[fname].value) { fok = true; data[r].cdr = ''; }
          else if(REG.test(document.MORE.elements[fname].value))
          {
            fvalue = parseInt(document.MORE.elements[fname].value);
            if(isNaN(fvalue)) { data[r].cdr=''; } else { fok = true; data[r].cdr = fvalue; }
          }
          if(fok) { $('form[name=MORE] input[name='+fname+']').css('background-color',''); } else { $('form[name=MORE] input[name='+fname+']').css('background-color', 'red'); }
        }
        CDRmore = CDRmore + (isNaN(fvalue) ? 0 : fvalue);
        fname='mska'+r; fvalue=0;
        if(document.MORE.elements[fname])
        {
          fok=false;
          if(''==document.MORE.elements[fname].value) { fok = true; data[r].mska = ''; }
          else if(REG.test(document.MORE.elements[fname].value))
          {
            fvalue = parseInt(document.MORE.elements[fname].value);
            if(isNaN(fvalue) || !(16 <= fvalue && fvalue <= 32)) { data[r].mska=''; fvalue = 0; } else { fok = true; data[r].mska = fvalue; }
          }
          if(fok) { $('form[name=MORE] input[name='+fname+']').css('background-color',''); } else { $('form[name=MORE] input[name='+fname+']').css('background-color', 'red'); }
        }
        IP4more = IP4more + (0 == fvalue ? 0 : Math.pow(2,32-fvalue));
		HitAPI(GLID);
      }
      $("#more").jqGrid("setGridParam", { data: data });
      //DisplayPricing();
    }
    var CDRless=0, IP4less=0;
    function FormatterLessNum(value, options, rowObject) { return('#' + options.rowId); }
    function FormatterLessCDR(value, options, rowObject) { return('<input onchange="UpdateLess()" type="text" size="4" name="cdr'+(options.rowId-1)+'" value="'+(value)+'" /> Mbps'); }
    function FormatterLessMSK(value, options, rowObject) { return('/ <input onchange="UpdateLess()" type="text" size="2" maxlength="2" name="msk'+(2==options.pos ? 'a' : 'b')+(options.rowId-1)+'" value="'+(value)+'" />'); }
    function UpdateLess()
    {
      var fname, fvalue, fok, REG = /[\d\s]+/;
      var data=$("#less").jqGrid("getGridParam","data");
      CDRless=0; IP4less=0;
      for(var r=0, rmax=data.length; r < rmax; r++)
      {
        fname='cdr'+r; fvalue=0;
        if(document.LESS.elements[fname])
        {
          fok=false;
          if(''==document.LESS.elements[fname].value) { fok = true; data[r].cdr = ''; }
          else if(REG.test(document.LESS.elements[fname].value))
          {
            fvalue = parseInt(document.LESS.elements[fname].value);
            if(isNaN(fvalue)) { data[r].cdr=''; } else { fok = true; data[r].cdr = fvalue; }
          }
          if(fok) { $('form[name=LESS] input[name='+fname+']').css('background-color',''); } else { $('form[name=LESS] input[name='+fname+']').css('background-color', 'red'); }
        }
        CDRless = CDRless + (isNaN(fvalue) ? 0 : fvalue);
        fname='mska'+r; fvalue=0;
        if(document.LESS.elements[fname])
        {
          fok=false;
          if(''==document.LESS.elements[fname].value) { fok = true; data[r].mska = ''; }
          else if(REG.test(document.LESS.elements[fname].value))
          {
            fvalue = parseInt(document.LESS.elements[fname].value);
            if(isNaN(fvalue) || !(16 <= fvalue && fvalue <= 32)) { data[r].mska=''; fvalue = 0; } else { fok = true; data[r].mska = fvalue; }
          }
          if(fok) { $('form[name=LESS] input[name='+fname+']').css('background-color',''); } else { $('form[name=LESS] input[name='+fname+']').css('background-color', 'red'); }
        }
        IP4less = IP4less + (0 == fvalue ? 0 : Math.pow(2,32-fvalue));
		HitAPI(GLID);
      }
      $("#less").jqGrid("setGridParam", { data: data });
      //DisplayPricing();
    }
//    function DisplayPricing()
//    {
//      $("#pricing").text("check pricing...");
//      var CDR = CDRsum + CDRmore + CDRless; var IP4 = IP4sum + IP4more - IP4less;
//      if(0 < IP4)
//      {
//        if(0 == CDR) { $("#pricing").text("premium pricing"); }
//        else
//        {
//          if(0 == CDR) { $("#pricing").text("premium pricing + submit an ICB"); }
//          else if(CDR < 1) { $("#pricing").text("premium pricing"); }
//          else if(1 <= CDR && CDR <= 100) { if(IP4 <= 256) { $("#pricing").text("standard pricing"); } else if(IP4 <= 8192) { $("#pricing").text("premium pricing"); } else { $("#pricing").text("submit an ICB"); } }
//          else if(101 <= CDR && CDR <= 999) { if(IP4 <= 512) { $("#pricing").text("standard pricing"); } else if(IP4 <= 8192) { $("#pricing").text("premium pricing"); } else { $("#pricing").text("submit an ICB"); } }
//          else if(1000 <= CDR && CDR <= 9999) { if(IP4 <= 1024) { $("#pricing").text("standard pricing"); } else if(IP4 <= 8192) { $("#pricing").text("premium pricing"); } else { $("#pricing").text("submit an ICB"); } }
//          else if(10000 <= CDR) { if(IP4 <= 2048) { $("#pricing").text("standard pricing"); } else if(IP4 <= 8192) { $("#pricing").text("premium pricing"); } else { $("#pricing").text("submit an ICB"); } }
//          else { $("#pricing").text("submit an ICB"); }
//        }
//        $("#pricing").prepend("CDR "+CDRsum+" Mbps with "+(IP4)+" chargeable IPs (/"+(Math.round(100*(32-(Math.log(IP4)/Math.log(2))))/100)+" IPv4 allocation) &rarr; ").append(" MRC " + MRC.toFixed(2));
//      }
//      else { $("#pricing").text("no chargeable IP allocation"); }
//    }
	function HitAPI(glid)
    {
		//$.get("https://ipaddr.sys.cogentco.com/api/getIPInfo/"+glid, {subIP: IP4less, addIP: IP4more, addCDR: CDRmore, subCDR: CDRless }, function(data)
		$.get("/api/getIPInfo/"+glid, {subIP: IP4less, addIP: IP4more, addCDR: CDRmore, subCDR: CDRless }, function(data)
            {
              $("#load_clist25").hide();
              if(data.ok)
              {
                $("#clist25").jqGrid("setGridParam", { data: data.data, userData: { pcode: 'Total IPv4', block: data.footer.ccount } }).trigger("reloadGrid");
                $("div#plist25 td#plist25_right").text(data.ipv4cnt+" IPv4 allocation"+(1 < data.ipv4cnt ? "s" : ""));
                IP4sum = (typeof data.ipsum !== 'undefined') ? data.ipsum : 0;
                MRC = (typeof data.mrc !== 'undefined') ? parseFloat(data.mrc) : 0;
				CDRsum = (typeof data.totalCDR !== 'undefined') ? parseInt(data.totalCDR) : 0;
				
				var level = data.level;
				var icb = data.icb;
				//var text = "CDR " + CDRsum + " Mbps with " + IP4sum + " chargeable IPs (/"+(Math.round(100*(32-(Math.log(IP4sum)/Math.log(2))))/100)+" IPv4 allocation) &rarr; ";
				$("#CDR").text("CDR: " + CDRsum + " Mbps");
				if (IP4sum == 0) {
					$("#IPSum").text("No Chargable IPs");
					$("#MRC").text("");
					$("#Pricing").text("");
					$("#OutstandingMRC").text("");
				} else {
					$("#IPSum").text(IP4sum + " Chargeable IPs (/"+(Math.round(100*(32-(Math.log(IP4sum)/Math.log(2))))/100)+" IPv4 allocation)");
					$("#MRC").text(MRC + " MRC");
					var pricing = '';
					if (level == 'premium') {
						pricing = " - Premium Pricing ";
					} else {
						pricing = " - Standard Pricing ";
					}
					if (data.icb) {
						pricing += "- ICB Required";
					}
					$("#Pricing").text(pricing);
					var current = data.currentipv4mrccharged;
					if (current > 0) {
						$("#OutstandingMRC").text("Customer currently paying " + current + " in IPv4 MRC");
					}
					if (data.project) {
						$("#ProjectLink").html("<a href='customers?gid=" + glid + "'>Auto Ipv4 Project Link</a>");
					} else {
						$("#ProjectLink").html('');
					}
				}
				
                //DisplayPricing();
              }
              else { $.jGrowl(data.msg.join("<br />"), { theme: 'error', header: 'get IPv4 allocations failed' } ); }
            }, "json");
	}
  </script>
  
 </body>
</html>
