<?php
namespace Regus;

use Illuminate\Console\Command;
use DB;

/**
 * Description of GetOrders
 *
 * @author tcassidy
 */
class DoOrderBasisRegus extends Command
{
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'doOrderBasisRegus';

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
	
		
		$orders = DB::table('BillingSystem.dbo.IPv4BillingRegus')->select(['id','OrderId'])
				->get();
//		echo var_dump($orders);
//		return;
		foreach($orders as $order) {
			$orderInfo = DB::table('dbo.v_sdscorecard')
					->where('OrderId',$order['OrderId'])
					->first();
			if ($order) {
				
				$region = $orderInfo['Region'];
				$productCode = $region . '_L3_ON_IPV4ALLOC_FLAT';
				DB::table('BillingSystem.dbo.IPv4BillingRegus')->where('id',$order['id'])->
					update(['Region'=> $region, 'QuotedCurrency' => $orderInfo['Currency'] ]);
			}

		}
		
		
		
	}
	
	
}
