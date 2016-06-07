
<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Cogent Communications &#45; Important Contract Notice</title>
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
											
											{{ $correction ? 'This email is a correction to a prior email with updated information.<br><br>' : '' }}
											
											
In August 2015, Cogent changed the way we allocate IPv4 address space to our customers.

Due to the increasing scarcity of public IPv4 addresses, Cogent faced a need to adopt a more sustainable management of this scarce resource.

As a result, beginning in August 2015, Cogent customers using (or requesting) more than 8 IPv4 addresses (/29) per port incur a monthly recurring fee with respect to such additional IPv4 address space.
<br><br>

For your &quot;Legacy&quot; IPv4 address allocations (i.e., allocated to you by Cogent before August 2015), we deferred implementation of these fees until July 1, 2016. 

In accordance with Section 2.2 of the CSA, we are hereby notifying you of (a) the updated Product Rider which reflects this policy and (b) the change in your monthly recurring charges to include the charges set forth in the Product Rider for your legacy IPv4 address allocations. 

As of your next port renewal date that occurs on or after July 1, 2016, the attached Product Rider will apply to your contract and the additional charge for IPv4 address allocations will go in effect.

<br><br>

In addition to notifying you about the change in charges, we wanted to give you adequate time to evaluate your IPv4 usage and, if necessary, to take appropriate actions, whether that includes giving back IPv4 addresses you feel you do not need or reconfiguring your network. 

For your convenience, we have included a list of your current Legacy IPv4 address allocations below (which were provided with your port order number{{ $multOrders ? 's' : '' }} {{$orderString}}).
											<br><br>
											<table style="border: black solid 1px;" width="25%" border="0" cellpadding="2" cellspacing="0">
												<tbody>
													<tr class="heading email">
														<td><b>IPv4 Allocation</b></td>
													</tr>
													@foreach($addresses as $address)
													<tr class="email">
														<td >
															{{$address['Address']}}
														</td>
													</tr>
													@endforeach
												</tbody>
											</table>
											<br>
These IPv4 address allocations will now be included under a <u>single new order number: {{$newOrder}}</u>. 

Discounting your one free /29 per port, your allocations come to a total count of {{$ipCount}} IPv4 addresses.

<u>Beginning on July 1, 2016</u>, we will be charging you a <u>monthly recurring fee of {{$mrc}} {{$curr}}</u> for this IPv4 address space. 

<br><br>

Should you wish to return any IPv4 addresses to avoid or reduce these fees, <u>please let us know before July 1, 2016</u> by sending an email to <a href="mailto:{{$terms}}">{{$terms}}</a>. 

You can also notify us any time thereafter &mdash; as IP address allocations are on a monthly term, billing for an IPv4 address allocation order <u>that you terminate</u> will stop 30 days after the end of the month of its notification date.
											<br><br>
Should you have any question regarding this notification, please let us know by replying to this email. 
											
You will receive an automatic response with a tracking number and we will be in touch with you shortly to address your inquiries.
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
