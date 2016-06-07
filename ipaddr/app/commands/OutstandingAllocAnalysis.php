<?php

use Illuminate\Console\Command;


/**
 * Description of GetOrdersFirstLast
 *
 * @author tcassidy
 */
class OutstandingAllocAnalysis extends Command
{
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'outAllocAnal';

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
		$customers = DB::table('BillingSystem.dbo.IPv4Billing')->where('ExistingAllocOrder',1)->whereNull('CreatedOrder')->get();
		echo "Id,Gid,CalcMRC,OutstMRC,MyMRC\n";
		$base = 'https://ipaddr-dev.sys.cogentco.com/api/getIPInfo/';
		foreach($customers as $customer) {
			$id = $customer['id'];
			$gid = $customer['GlobalLogoID'];

			$json = file_get_contents($base . $gid);
			$arr = json_decode($json,true);
			$mrc = $arr['mrc'];
			$outstandingMRC = $arr['currentipv4mrccharged'];
			$myMrc = $customer['MRC'];
			if ($mrc > 50) {
				echo "$id,$gid,$mrc,$outstandingMRC,$myMrc\n";
			}
//			$myOrder = $customer['CreatedOrder'];
//			
//			$mrc = OutstandingAllocAnalysis::getIPInfo($gid);
//			
//			$outstandingMRC = DB::table('TLG.dbo.v_sdscorecard')
//					->where('interfacetypeint',47)
//					->where('ProvOrderStatus','Completed')
//					->whereNull('OrderCancelDt')
//					->where('GlobalLogoID',$gid)
//					->where('OrderId','!=',$myOrder)->sum('MRR');
//			$numOrders = DB::table('TLG.dbo.v_sdscorecard')
//					->where('interfacetypeint',47)
//					->where('ProvOrderStatus','Completed')
//					->whereNull('OrderCancelDt')
//					->where('GlobalLogoID',$gid)
//					->where('OrderId','!=',$myOrder)->count();
//			$earliest = DB::table('TLG.dbo.v_sdscorecard')
//					->where('interfacetypeint',47)
//					->where('ProvOrderStatus','Completed')
//					->whereNull('OrderCancelDt')
//					->where('GlobalLogoID',$gid)
//					->where('OrderId','!=',$myOrder)->min('OrderCreatedDt');
//			
//			DB::table('BillingSystem.dbo.IPv4Billing')->where('id',$id)->
//					update(['OutstandingMRC' => $outstandingMRC,
//							'NumOutstandingAllocOrders' => $numOrders,
//							'APIMRC' => $mrc,
//							'EarliestAllocOrder' => $earliest]);
//			
//			
			

		}
		
	}
	
	
	
}
