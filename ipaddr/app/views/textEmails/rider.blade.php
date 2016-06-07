Cogent Communications - Important Contract Notice

eCogent: https://ecogent.cogentco.com/eCogentRegistration/regStartSetup.do


To {{$customer}},
											
Dear Valued Cogent Customer, 

{{ $correction ? 'This email is a correction to a prior email with updated information.' : '' }}

In August 2015, Cogent changed the way we allocate IPv4 address space to our customers.

Due to the increasing scarcity of public IPv4 addresses, Cogent faced a need to adopt a more sustainable management of this scarce resource.

As a result, beginning in August 2015, Cogent customers using (or requesting) more than 8 IPv4 addresses (/29) per port incur a monthly recurring fee with respect to such additional IPv4 address space.

For your "Legacy" IPv4 address allocations (i.e., allocated to you by Cogent before August 2015), we deferred implementation of these fees until July 1, 2016.

In accordance with Section 2.2 of the CSA, we are hereby notifying you that, beginning on July 1, 2016, Cogent will begin charging the fees set forth in the Product Rider for your legacy IPv4 address allocations.

In addition to notifying you about the change in charges, we wanted to give you adequate time to evaluate your IPv4 usage and, if necessary, to take appropriate actions, whether that includes giving back IPv4 addresses you feel you do not need or reconfiguring your network. 

For your convenience, we have included a list of your current Legacy IPv4 address allocations below (which were provided with your port order number{{ $multOrders ? 's' : '' }} {{$orderString}}).
											
IPv4 Allocation
@foreach($addresses as $address)
{{$address['Address']}}
@endforeach
											
These IPv4 address allocations will now be included under a single new order number: {{$newOrder}}. 

Discounting your one free /29 per port, your allocations come to a total count of {{$ipCount}} IPv4 addresses.

Beginning on July 1, 2016, we will be charging you a monthly recurring fee of {{$mrc}} {{$curr}} for this IPv4 address space. 

Should you wish to return any IPv4 addresses to avoid or reduce these fees, please let us know before July 1, 2016 by sending an email to {{$terms}}. 

You can also notify us any time thereafter - as IP address allocations are on a monthly term, billing for an IPv4 address allocation order that you terminate will stop 30 days after the end of the month of its notification date.
											
Should you have any question regarding this notification, please let us know by replying to this email. 
											
You will receive an automatic response with a tracking number and we will be in touch with you shortly to address your inquiries.
											
Sincerely,
Cogent Communications - Billing