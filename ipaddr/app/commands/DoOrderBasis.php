<?php

use Illuminate\Console\Command;
//use Config;

/**
 * Description of GetOrders
 *
 * @author tcassidy
 */
class DoOrderBasis extends Command
{
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'doOrderBasis';

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
	

		$customers = DB::table('BillingSystem.dbo.IPv4Billing')->select(['GlobalLogoID'])
				//->where('Batch',8)
				->where('id',7872)
				//->whereNull('CreatedOrder')
				//->whereNull('BasisOrder')
				->get();
//		echo var_dump($customers);
//		return;
		foreach($customers as $customer) {
			$customer = $customer['GlobalLogoID'];
			$max = DB::table('dbo.v_sdscorecard')
					->whereIn('InterfaceTypeInt',[6,12,10,8,13, 14, 33, 40, 42, 45])
					->whereIn('Layer',[3,4])
					->where('GlobalLogoID',$customer)
					->where('ProvOrderStatus','Completed')
					->whereNull('OrderCancelDt')
					->max('OrderCreatedDt');
			
			
			$order = DB::table('dbo.v_sdscorecard')
					->where('OrderCreatedDt',$max)
					->whereIn('Layer',[3,4])
					->where('GlobalLogoID',$customer)
					->whereIn('InterfaceTypeInt',[6,12,10,8,13, 14, 33, 40, 42, 45])
					->where('ProvOrderStatus','Completed')
					->whereNull('OrderCancelDt')
					->select(['Region','OrderId','CustomerName','Currency'])
					->first();
			
			
			
			
			if ($order) {
				
				$region = $order['Region'];
				$productCode = $region . '_L3_ON_IPV4ALLOC_FLAT';
				DB::table('BillingSystem.dbo.IPv4BillingRerun')->where('GlobalLogoID',$customer)->
					update(['Region'=> $region, 'BasisOrder' => $order['OrderId'], 'QuotedCurrency' => $order['Currency'], 'ProductCode' => $productCode, 'CustomerName' => $order['CustomerName']]);
			}
			echo "$customer\n";

		}
		
		
		
	}
	
	
}
