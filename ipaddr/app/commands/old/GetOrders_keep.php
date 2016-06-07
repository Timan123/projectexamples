<?php

use Illuminate\Console\Command;


/**
 * Description of GetOrders
 *
 * @author tcassidy
 */
class GetOrders extends Command
{
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'getOrders';

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
	
		//first get every customer that already has been ipv4 billed
//		$results = DB::table('dbo.v_sdscorecard')->where('ProductCode','like','%_L3_ON_IPV4ALLOC_FLAT')->select('GlobalLogoID')->distinct()->get();
//		$billedCustomers = [];
//		foreach($results as $result) {
//			array_push($billedCustomers,$result->GlobalLogoID);
//		}
		
		
		$results2 = DB::table('dbo.v_sdscorecard')
				->where('OrderCreatedDt','>=','9/1/15')
				->whereNotIn('GlobalLogoID', $billedCustomers)
				->select('GlobalLogoID')->distinct()->get();
		
		//do for each customer in the loop
		foreach($results2 as $customer) {
			$customer = $customer->GlobalLogoID;
		
			//get all orders that are port or lag, completed, not cancelled, 
			$results3 = DB::table('dbo.v_sdscorecard')
				->whereIn('InterfaceTypeInt',[13, 14, 33, 40, 42, 45])
				->where('GlobalLogoID', $customer)
				->where('ProvOrderStatus','Completed')
				->where('OrderCreatedDt','>=','9/1/15')
				->whereNull('OrderCancelDt')
				->select(['OrderId'])
				->get();
			$orderIds = [];
			foreach($results3 as $result) {
				array_push($orderIds,$result->OrderId);
			}

			$ips = DB::table('Starfish.AdminSF.ip_block')
					->where('Version',4)
					->whereIn('OrderId', $orderIds)
					->where('Netmask','>=',115) //exclude the super jumbo blocks
					->select('count')->get();

			//use array_reduce because we can get multiple IPs per order, add them this way
			$totalIps = array_reduce($ips, function($carry, $item) {
				$carry += $item->count;
				return $carry;
			});
			
			if ($totalIps > 8) {
				echo $customer . "," . $totalIps . "\n";
			}	
		}
		
		
	}
	
}
