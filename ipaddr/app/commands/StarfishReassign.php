<?php

use Illuminate\Console\Command;
//use Config;

/**
 * Description of GetOrders
 *
 * @author tcassidy
 */
class StarfishReassign extends Command
{
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'starfishReassign';

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
				//->whereRaw("isnull(SFReassign,0) = 0")
				//->whereNotNull('CreatedOrder')
				->where('id',7872)
				////->where('Investigate',1)
				//->where('id',4130)
				//->whereIn('Batch',[8,9])
				->select(['CreatedOrder','GlobalLogoID','id'])
				->get();

		foreach ($customers as $customer) {
			$newOrder = $customer['CreatedOrder'];
			$gid = $customer['GlobalLogoID'];
			//only need to reassign port orders that have some ip space
			$orderIds = DB::table('BillingSystem.dbo.ipv4billingtooldorder')
				->select(['OrderId'])->where('IPCount','>',0)->where('GlobalLogoID',$gid)->get();
			
			foreach($orderIds as $orderId) {
				$portOrder = $orderId['OrderId'];
				$dataForHist = DB::connection('prodsfip')->table('Starfish.AdminSF.ip_block')->where('OrderId',$portOrder)->where('version',4)
					->select([	'assigned',
								'assignedby',
								DB::raw("'$now' as 'freed'"),
								DB::raw("'tcassidy' as 'freedby'"),
								'netaddr',
								'netend',
								'netmask',
								'status',
								'custid',
								'orderid'
							])->get();
				if ($dataForHist) {
					DB::connection('prodsfip')->table('Starfish.AdminSF.ip_history')->insert($dataForHist);
				}
				
				DB::connection('prodsfip')->table('Starfish.AdminSF.ip_block')
					->where('orderid',$portOrder)->where('version',4)
					->update([
						'port_order' => $portOrder,
						'orderid' => $newOrder,
						'date' => $now,
						'user' => 'tcassidy'
					]);
			}
			//only need to do one audit row, do outside the loop
			$auditRow = ['date' => $now, 'user' => 'tcassidy', 'section' => 'ip_block', 'action' => 'edit', 
				'detail' => "updated by MAC and moved port and lag ipv4 space for globallogoid=$gid to new ipv4alloc order $newOrder"];

			DB::connection('prodsfip')->table('Starfish.AdminSF.sf_audit')->insert($auditRow);
			echo "finished $newOrder\n";
			DB::table('BillingSystem.dbo.IPv4Billing')->where('id',$customer['id'])->update(['SFReassign' => 1]);
		}
		
		
	}
	
	
}
