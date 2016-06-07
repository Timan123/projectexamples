<?php

class EmailData
{
	
	
	public static function getEmailData($id) {
		$info = DB::table('BillingSystem.dbo.IPv4Billing')->where('id',$id)->first();
		$newOrder = $info['CreatedOrder'];
		if (!$newOrder) {
			$newOrder = '1-XXXXXXXXX';
		}
		
		$orders = DB::table('BillingSystem.dbo.IPv4BillingToOldOrder')->select('OrderId')->where('GlobalLogoID', $info['GlobalLogoID'])->orderBy('OrderId')->get();
		$addresses = DB::table('BillingSystem.dbo.IPv4BillingToAddresses')->select('Address')->where('GlobalLogoID', $info['GlobalLogoID'])->get();
		$fromAddr = 'billing@cogentco.com';
		$fromName = 'Cogent Billing';
		$terms = 'renewals@cogentco.com';
		if ($info['Region'] == 'EU') {
			$terms = 'termseu@cogentco.com';
			$fromAddr = 'billingeu@cogentco.com';
			$fromName = 'Cogent Billing Europe';
		}
		$string = implode(', ', array_map(function ($entry) {
			return $entry['OrderId'];
		}, $orders));
		//start currency as quoted then do a billed lookup
		$mrc = $info['MRC'];
		$curr = $info['QuotedCurrency'];
		$opm = DB::table('TLG.mjain.OPM_Order_Details')->where('OrderId',$newOrder)->select('ConversionToEuro','BillCurrencyId')->first();
		if ($opm['BillCurrencyId']) { //not null and not zero
			$billCur = DB::table('dbo.OPM_BillCurrency')->where('BillCurrencyID', $opm['BillCurrencyId'])->select('CurrencyCode')->first();
			$curr = $billCur['CurrencyCode'];
			$mrc *= $opm['ConversionToEuro'];
		}
		$multOrders = count($orders) == 1 ? false : true;
		$mrc = number_format($mrc, 2);
		$ipCount = number_format($info['IPCount'],0);
		$billDt = new Carbon($info['EffLastBillDt']);
		$notifyBy = clone $billDt;
		$notifyBy->subMonth();
		$future = false;
		$goLive = new Carbon('2016-07-01');
		$correction = false;
		if ($info['CorrectionEmail']) {
			$correction = true;
		}
		$viewName = 'noRider';
		if ($billDt->gt($goLive)) {
			$future = true;
			$viewName = 'future';
		} else if ($info['GotRider']) {
			$viewName = 'rider';
		}
		$recips = $info['Recips'];
		$recipsArr = explode(',',$recips);
		
		
		$header = "From: $fromAddr<br>";
		$header .= "To: " . $info['Recips'] . "<br>BCC: ";
		$bccArr = [];
		if ($info['PCMRep']) {
			$header .= $info['PCMRep'] . '@cogentco.com';
			$bccArr = [ $info['PCMRep'] . '@cogentco.com' ];
		}
		else if ($info['AM']) {
			$header .= $info['AM'] . '@cogentco.com';
			$bccArr = [ $info['AM'] . '@cogentco.com' ];
		} 
		
		$header = chop($header,',');
		$header .= "<br>";
		return [	'orderString' => $string,
					'newOrder' => $newOrder,
					'recipsArr' => $recipsArr,
					'bccArr' => $bccArr,
					'correction' => $correction,
					'fromAddr' => $fromAddr,
					'fromName' => $fromName,
					'viewName' => $viewName,
					'header' => $header,
					'mrc' => $mrc,
					'terms' => $terms,
					'future' => $future,
					'billDt' => $billDt->format('F j, Y'),
					'notifyBy' => $notifyBy->format('F j, Y'),
					'multOrders' => $multOrders,
					'ipCount' => $ipCount,
					'curr' => $curr,
					'addresses' => $addresses, 
					'customer' => $info['CustomerName']
			];
	}
	
	
}