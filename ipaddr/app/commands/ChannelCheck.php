<?php

use Illuminate\Console\Command;
//use Config;

/**
 * Description of GetOrders
 *
 * @author tcassidy
 */
class ChannelCheck extends Command
{
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'channelCheck';

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
	
		$customers = DB::table('BillingSystem.dbo.IPv4Billing')
				->select(['id','GlobalLogoID'])
				->where('HasChannelOrder',1)->get();
		foreach($customers as $customer) {
		
			$id = $customer['id'];
			$customer = $customer['GlobalLogoID'];
			$pcms = DB::table('TLG.mjain.TABLE_V_SIEBELORDERS as s')
					->leftJoin('TLG.dbo.v_sdscorecard as sd','s.order_num','=','sd.OrderId')
					->leftJoin('Reporting.dbo.RAMInfo as r','sd.RAM','=','r.RAM')
					->where('ProvOrderStatus','Completed')
					->where('sd.GlobalLogoId',$customer)
					->whereNull('OrderCancelDt')
					->where('r.IsActive',1)
					->where('Channel','true')
					->select('sd.RAM')->first();
			if ($pcms) {
				$val = strtolower($pcms['RAM']);
				DB::table('BillingSystem.dbo.IPv4Billing')->where('id',$id)->update(['PCMRep' => $val]);
				//echo $customer . "\t" . var_dump($pcms);
			}
		}
		
		
		
	}
	
	
}

