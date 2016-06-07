<?php



use Illuminate\Console\Command;

use Illuminate\Filesystem\Filesystem;

class WriteEDocs extends Command
{
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'writeEDocs';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'write eDocs';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	
	public function fire() {
		$infos = DB::table('BillingSystem.dbo.IPv4Billing')->where('EmailSent','>','2016-05-21')->get();
		foreach($infos as $info) {
			$emailData = EmailData::getEmailData($info['id']);
			$orderId = $emailData['newOrder'];
			$path = '.\eDocsWork\CSO_' . $orderId; 
			if(!is_dir($path)) {
				mkdir($path);
			}
			$fs = new Filesystem();
			$viewName = $emailData['viewName'];
			$fs->put($path . '\LegacyIPv4emailcommunication.htm', View::make('emails.' . $viewName, $emailData));
			echo $info['CustomerName'] . "\n";
		}
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	public function fireOld()
	{
		$now = (new Carbon())->toDateTimeString();
//		$infos = DB::table('BillingSystem.dbo.IPv4Billing')->whereNotNull('CreatedOrder')->where('JumboNoBill',0)->get();
		//$infos = DB::table('BillingSystem.dbo.IPv4Billing')->where('id',9638)->get();
//		foreach($infos as $info) {
			$id = 76;
			$info = DB::table('BillingSystem.dbo.IPv4Billing')->where('id',$id)->first(); //$id hardcoded for now
			$newOrder = $info['CreatedOrder'];
			if (!$newOrder) {
				//have to have a new order, cut out of the function if you don't
				return;
			}

			$orders = DB::table('BillingSystem.dbo.IPv4BillingToOldOrder')->select('OrderId')->where('GlobalLogoID', $info['GlobalLogoID'])->orderBy('OrderId')->get();
			$addresses = DB::table('BillingSystem.dbo.IPv4BillingToAddresses')->select('Address')->where('GlobalLogoID', $info['GlobalLogoID'])->get();

			//relevant
			$recips = $info['Recips'];
			$recipsArr = explode(',',$recips);
			//echo var_dump($recipsArr);
			//relevant
			$bccArr = [$info['AM'] . '@cogentco.com', 'vteissier@cogentco.com', 'tcassidy@cogentco.com'];
			//echo var_dump($bccArr);
			
			
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
			$viewName = 'noRider';
			if ($billDt->gt($goLive)) {
				$future = true;
				$viewName = 'future';
			} else if ($info['GotRider']) {
				$viewName = 'rider';
			}
			$pathToFile = 'Product Rider DIA V1-9.pdf';
			$logger = new \Swift_Plugins_Loggers_EchoLogger();
            \Mail::getSwiftMailer()->registerPlugin(new \Swift_Plugins_LoggerPlugin($logger));

			\Mail::send(array('emails.' . $viewName, 'textEmails.' . $viewName), 
				[	'orderString' => $string,
					'newOrder' => $newOrder,
					'mrc' => $mrc,
					'terms' => $terms,
					'future' => $future,
					'billDt' => $billDt->format('F j, Y'),
					'notifyBy' => $notifyBy->format('F j, Y'),
					'multOrders' => $multOrders,
					'ipCount' => $ipCount,
					'curr' => $curr,
					'addresses' => $addresses, 
					'customer' => $info['CustomerName'],
					'header' => '' //email only
				], function($message) use ($bccArr, $recipsArr, $fromAddr, $fromName, $pathToFile, $viewName)
			{
				
				$message
					->to(['tcassidy07@gmail.com', 'Timan123@yahoo.com'])
					->bcc(['tcassidy@cogentco.com','tlow@cogentco.com'])
					->attach($pathToFile, ['mime' => 'application/pdf'])
					//->to($recipsArr) //relevant	
					//->bcc($bccArr) //relevant	
					//->bcc(['tcassidy07@gmail.com','Timan123@yahoo.com'])
					->from($fromAddr, $fromName)
					//->subject( 'Cogent Communications – Important Contract Notice' )
					->subject('email host, x2 bcc')	
					;
			});
			ddd( $logger->dump() );
			//DB::table('BillingSystem.dbo.IPv4Billing')->where('id',$info['id'])->update(['EmailSent' => $now]);
			$this->info('Email sent');
		}
//	}
}