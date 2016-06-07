<?php

use Illuminate\Console\Command;


/**
 * Description of GetOrders
 *
 * @author tcassidy
 */
class MACSweep extends Command
{
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'macSweep';

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
		
		
		$customers = DB::table('BillingSystem.dbo.IPv4Billing')
				->whereNotNull('CreatedOrder')
				->select(['CreatedOrder','GlobalLogoID','id'])
				->get();

		foreach ($customers as $customer) {
			$id = $customer['id'];
			$gid = $customer['GlobalLogoID'];
			//only need to reassign port orders that have some ip space
			$orderIds = DB::table('BillingSystem.dbo.ipv4billingtooldorder')
				->select(['OrderId'])->where('IPCount','>',0)->where('GlobalLogoID',$gid)->get();
			
			foreach($orderIds as $orderId) {
				$oldOrder = $orderId['OrderId'];
				$newOrder = DB::table('TLG.dbo.MAC_To_OldOrder')->where('MACOrderId',$oldOrder)->select(['OrderId'])->first();
				if ($newOrder) {
					$newOrder = $newOrder['OrderId'];
					DB::table('BillingSystem.dbo.ipv4billingtooldorder')->where('OrderId',$oldOrder)->update(['OrderId' => $newOrder]);
					DB::table('BillingSystem.dbo.ipv4billingtoaddresses')->where('OrderId',$oldOrder)->update(['OrderId' => $newOrder]);
					DB::table('BillingSystem.dbo.IPv4Billing')->where('id',$id)->update(['Investigate' => 1]);
					echo "$oldOrder\t->\t$newOrder\n";
				}
			}
		}
					

	
	}
	

	
}
