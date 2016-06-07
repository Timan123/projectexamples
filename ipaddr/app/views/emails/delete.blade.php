
<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Cogent Communications &#45; Followup Email</title>
        <style>
		@page { size 8.5in 11in; margin: 2cm }
A {
    COLOR: #0000FF; FONT-FAMILY: calibri
}
A:link {
    COLOR: #0000FF; FONT-FAMILY: calibri
}
A:visited {
    COLOR: #0000FF; FONT-FAMILY: calibri
}
A:active {
    COLOR: #0000FF; FONT-FAMILY: calibri
}
A:hover {
    COLOR: #FF0000; FONT-FAMILY: calibri
}
BODY {
    FONT-SIZE: 10px; COLOR: #000000; FONT-FAMILY: verdana, helvetica, tahoma
}
BR {
    FONT-FAMILY: verdana, helvetica, tahoma
}
DIV {
    FONT-SIZE: 10px; FONT-FAMILY: verdana, helvetica, tahoma
}
HEAD {
    FONT-FAMILY: verdana, helvetica, tahoma
}
NOBR {
    FONT-FAMILY: verdana, helvetica, tahoma
}
P {
    FONT-SIZE: 10px; FONT-FAMILY: verdana, helvetica, tahoma
}
SPAN {
    FONT-SIZE: 10px
}
SPAN {
    FONT-FAMILY: verdana, helvetica, tahoma
}
TD {
    FONT-SIZE: 10px; FONT-FAMILY: verdana, helvetica, tahoma
}


.general	{ font:8pt tahoma; }
.text		{ border:solid 1px #000000;font:8pt tahoma; }
.text2		{ border:solid 1px #000000;font:8pt tahoma; color:white; font-weight:bold;}
.submit		{ border:solid 1px #000000;font:8pt tahoma;width:75px; }
.table		{ border:solid 1px #cccccc;font:8pt tahoma; }
.column		{ color:#ffffcc;background:#3366cc;font:bold 7pt tahoma; }
.header		{ font:bold 8pt tahoma;color:#003399; }
.warning	{ font:bold 8pt tahoma;color:#dd0000; }
.message	{ font:bold 8pt tahoma;color:#3366cc; }
.info		{ font:italic 8pt tahoma;color:#000000; }
.texty          { BACKGROUND: #eee; FONT: 7pt tahoma}
.texty1         { BACKGROUND: #dae9f5; FONT: 7pt tahoma}
.heading        { BACKGROUND: #DDE1E1; FONT: bold 7pt tahoma} 
.input          { BORDER-RIGHT: #000 1px solid; BORDER-TOP: #000 1px solid; FONT: 7pt tahoma; BORDER-LEFT: #000 1px solid;  BORDER-BOTTOM: #000 1px solid }

@media print
{
    body.provRedReport{size: landscape; }
    table{ page-break-inside: auto}
}

.email {
    font-family: Calibri;
    font-style : normal;
    font-size : 120%;    
}

	</style>
    </head>
    <body>
        {{$header}}
            <table style="width:100%;margin-right: auto;margin-left: auto;" >
                <tbody>
                    <tr>
                        <td>&nbsp;</td>
                        <td width="50%">
                            <table style='width:700;margin-right: auto;margin-left: auto;padding:2%;-webkit-border-radius: 25px; -moz-border-radius: 25px;border-radius: 25px; border: 6px solid #6699FF;background: #FFFFFF'>                      
                                <tbody>
                                    <tr height="20%">
                                        <td colspan="2"><img src="http://ecogent.cogentco.com/resources/images/logo.png" alt="Logo" width='48%'></td>                                      
                                        <td width="40%">
                                            <table style='width:260;margin-left: auto;padding-right:2%;padding-left: 2%;-webkit-border-radius: 25px; -moz-border-radius: 25px;border-radius: 25px; background: #F2F2F2;'>                                        
                                                <tbody>
                                                    <tr>
                                                        <td class="email" align="right" style='padding-top:2%;padding-bottom:2%'>
                                                            <Strong>eCogent</Strong>: <a href="https://ecogent.cogentco.com/eCogentRegistration/regStartSetup.do">Log in or register your online account.</a>                                                     
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>                                            
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="email"  colspan="3" >
											To {{$customer}},<br><br>
											
											Dear Valued Cogent Customer, <br><br>
											
We notified you earlier about new billing for IPv4 addresses. We decided not to bill you for your two /16 IPv4 allocations. The new order {{$newOrder}} has been deleted from our system and you won't owe any additional monthly recurring fee.
											<br><br>
											Sincerely,<br><br>
											Cogent Communications - Billing

                                        </td>
                                    </tr>                                    
                                </tbody>
                            </table>
                        </td>
                        <td>&nbsp;</td>
                    </tr>                     
                </tbody>        
            </table>
    </body>
</html>
