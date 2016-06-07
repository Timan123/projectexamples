<?php

use Illuminate\Console\Command;


/**
 * Description of GetOrders
 *
 * @author tcassidy
 */
class SetUpOrdersRegus extends Command
{
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'setUpOrdersRegus';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'IP Allocation';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{ 
		
					
		$now = (new Carbon())->toDateTimeString();

		
		$ordersToSetUp = DB::table('BillingSystem.dbo.IPv4BillingRegus')
				->whereNotNull('CreatedOrder')
				->whereRaw("isnull(ImportStep,'') = 'LinkedFromSF'")
				->whereRaw("isnull(SetupStep,'') != 'Done'")
				//->whereRaw("efflastbilldt != efflastbilldtbackup")
				->get();
//		echo count($ordersToSetUp);
//		return;

		foreach($ordersToSetUp as $orderToSetup) {
			$orderId = $orderToSetup['CreatedOrder'];
			$basis = $orderToSetup['OrderId'];
			$mrc = $orderToSetup['MRC'];
			$id = $orderToSetup['id'];
			$ipCount = $orderToSetup['IPCount'];
			$lineInsert = $orderToSetup['LineInsert'];
			$ccdbInsert = $orderToSetup['CCDBInsert'];
			$billDt = '2016-07-01';
			
			
			$data = DB::connection('prodtlg')->table('TLG.mjain.TABLE_V_SIEBELORDERS')->where('ORDER_NUM',$basis)->get();
			//echo var_dump($data);
			$arr = $data[0];
			unset($arr['ORDER_NUM']);
			unset($arr['Comments']);
			unset($arr['Order_Num']);
			unset($arr['Order Number']);
			unset($arr['Order Date']);
			unset($arr['Price List']);
			unset($arr['Sales Stage']);
			unset($arr['CDR']);		
			unset($arr['Created']);
			unset($arr['Created By']);
			unset($arr['Sales Rep_ID']);
			unset($arr['Sales Rep Name']);
			unset($arr['As Of']);
			unset($arr['F2Close']);
			unset($arr['OrderType']);

			//echo var_dump($arr);
			DB::table('BillingSystem.dbo.IPv4BillingRegus')->where('id',$id)->update(['SetupStep' => 'Table_V']);
			DB::connection('prodtlg')->table('TLG.mjain.TABLE_V_SIEBELORDERS')->where('Order_num',$orderId)->update($arr);


			
			//the lookup id for the legacy allocation type
			$allocId = 18;
			

			DB::table('BillingSystem.dbo.IPv4BillingRegus')->where('id',$id)->update(['SetupStep' => 'OPM']);
			$opm = DB::connection('prodtlg')->table('TLG.mjain.OPM_Order_Details')->where('OrderId',$basis)
					->select([	'OrderClass', 
								'Currency', 
								'CogentEntity', 
								'BillCurrencyId', 
								'TradeRegister', 
								'VATNumber', 
								'SecondaryCompanyName', 
								'RegistrationNo',
								'consolidatedBillingRequested',
								'TaxOffice'
							])
					->get();
			$opmArr = $opm[0];
			
			$opmSet = [	'MRR' => $mrc,
						'IPAllocationId' => $allocId,
						'IPv4LegacyCount' => $ipCount,
						'Layer2Type' => 10,
						'BurstBillRate' => 10,
						'VcType' => 4,
						'L2PortTypeId' => 1,
						'Resell' => 0,
						'ICB' => 0,
						'CCDBSync' => 1,
						'BpPerMeg' => 0.00,
						'TTD' => 0,
						'BurstBillingType' => 0,
						'ICBBilling' => 0,
						'BurstBillRate' => 10,
						'NonStdCont' => 0,
						'Expedite' => 0,
						'SalesMngrValidDt' => $now,
						'SalesMngrValidBy' => 'tcassidy',
						'Term' => 1,
						'SalesMngrValidStatus' => 1,
						'RequestedInstallDt' => $billDt,
						'AutoRenew' => 1,
						'SDGoodOrderDt' => $billDt,
						'LastModfDt' => $now,
						'LastModfBy' => 'tcassidy'
					];

			$opmArr = array_merge($opmArr, $opmSet);
			
			if ($opmArr['BillCurrencyId'] > 0 && !is_null($opmArr['BillCurrencyId'])) {
				$billCur = DB::table('dbo.OPM_BillCurrency')->where('BillCurrencyID', $opmArr['BillCurrencyId'])->select('CurrencyCode')->get();
				$billCur = $billCur[0]['CurrencyCode'];
				$cur = $opmArr['Currency'];
				$table = '';
				$insertDate = '';
				$rate = null;
				if ($cur == 2) {
					$convData = DB::table('dbo.EuroToOtherRate')->where('CurrencyCode',$billCur)->select('RateToEuroDollar','InsertDate')->get();
					$rate = $convData[0]['RateToEuroDollar'];
					$insertDate = $convData[0]['InsertDate'];
				} else if ($cur == 3) {
					if ($billCur == 'USD') {
						$rate = 1.00;
						$insertDate = $now;
					} else {
						$convData = DB::table('dbo.USDToOtherRate')->where('CurrencyCode',$billCur)->select('RateToUSDollar','InsertDate')->get();
						$rate = $convData[0]['RateToUSDollar'];
						$insertDate = $convData[0]['InsertDate'];
					}
				} else if ($cur == 7) {
					if ($billCur == 'GBP') {
						$rate = 1.00;
						$insertDate = $now;
					} else {
						$convData = DB::table('dbo.GBPtoOtherRate')->where('CurrencyCode',$billCur)->select('RateToGBPDollar','InsertDate')->get();
						$rate = $convData[0]['RateToGBPDollar'];
						$insertDate = $convData[0]['InsertDate'];
					}
				}
				if ($rate) {
					$opmArr['ConversionToEuro'] = $rate;
					$opmArr['BillCurrencyDate'] = $insertDate;
					$opmArr['EuroRateSource'] = 'www.oanda.com';
				}
			}



			DB::connection('prodtlg')->table('TLG.mjain.OPM_Order_Details')->where('OrderId',$orderId)->update($opmArr);


			if (!$lineInsert) {
				DB::table('BillingSystem.dbo.IPv4BillingRegus')->where('id',$id)->update(['SetupStep' => 'OPMLine']);
				$lineArr = ['OrderId' => $orderId,
							'LineItemID' => 2008,
							'ListedPrice' => $mrc,
							'DiscountPrice'  => $mrc,
							'CreatedBy' => 'tcassidy',
							'CreatedDt' => $now,
							'LastModfBy' => 'tcassidy',
							'LastModfDt' => $now,
							'IsActive' => 1
							];
				DB::connection('prodtlg')->table('TLG.dbo.OPMOrderLineItems')->insert($lineArr);
				DB::table('BillingSystem.dbo.IPv4BillingRegus')->where('id',$id)->update(['LineInsert' => 1]);
			}

			DB::table('BillingSystem.dbo.IPv4BillingRegus')->where('id',$id)->update(['SetupStep' => 'OrderSch']);
			$sch = ['SLA'=> $billDt,
					'AssignedDt'=> $billDt,
					'IPCompDt'=> $billDt,
					'CustCutOverDt'=> $billDt,
					'CustomerAccptDt'=> $billDt,
					'CAPSentDt'=> $billDt,
					'StartBillingDt'=> $billDt,
					'LastUpdatedt'=> $now,
					'LastUpdatedBy'=> 'tcassidy'];
			DB::connection('prodtlg')->table('TLG.mjain.OrderScheduleDates')->where('OrderId',$orderId)->update($sch);

			DB::table('BillingSystem.dbo.IPv4BillingRegus')->where('id',$id)->update(['SetupStep' => 'Prov']);
			
			//there is no need to initialize the prov data with a row from the table, it should be totally fresh data
			$provSet = ['StatusCode' => 'Completed',
					'IpEngStatus' => 'Completed',
					'Keyword' => 'Auto',
					'CreditApprvDt' => $now,
					'CreditApprvBy' => 'tcassidy',
					'CreditNotes' => 'Auto',
					'OrderCompletedDt' => $billDt,
					'SalesValidStatus' => 1,
					'SalesValidUpdtDt' => $now,
					'SalesValidBy' => 'tcassidy',
					'SalesOpsNotes' => "basis order: $basis",
					'OrderCreatedDt' => $now,
					'LastUpdtDt' => $now,
					'CreditStatus' => 'Approved Credit',
					'LastUpdtUser' => 'tcassidy'
					];
			DB::connection('prodtlg')->table('TLG.mjain.Order_Prov_Details')->where('OrderId',$orderId)->update($provSet);
			//echo var_dump($prov);

			//CCDB SetupStep
			if (!$ccdbInsert) {
				DB::table('BillingSystem.dbo.IPv4BillingRegus')->where('id',$id)->update(['SetupStep' => 'CCDB']);
				$ccdbs = DB::connection('prodtlg')->table('TLG.dbo.CCDBCCToOrder')
						->where('OrderId',$basis)
						->select(['CCID', 'ContactType','DataCenterAccess', 'Status','IsActive'])
						->get();
				$ccdbArr = array_map(function($ccdb) use ($orderId, $now, $basis) { 
					$ccdb['OrderId'] = $orderId;
					$ccdb['LastUpDt'] = $now;
					$ccdb['LastUpDtUser'] = "auto selected from $basis";
					return $ccdb;
				}, $ccdbs);
				DB::connection('prodtlg')->table('TLG.dbo.CCDBCCToOrder')->insert($ccdbArr);
				DB::table('BillingSystem.dbo.IPv4BillingRegus')->where('id',$id)->update(['CCDBInsert' => 1]);
			}

			DB::table('BillingSystem.dbo.IPv4BillingRegus')->where('id',$id)->update(['SetupStep' => 'Done']);
			echo "finished $orderId\n";
		}
	
	}
	

	
}
