<?php

use Illuminate\Console\Command;
//use Config;

/**
 * Description of GetOrders
 *
 * @author tcassidy
 */
class AllocWork extends Command
{
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'allocWork';

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
	

		$blocks = DB::connection('dev')->table('dbo.IPv4BlockPull')->where('PortRow',1)->whereRaw('len(descr) > 2')->get();
		foreach ($blocks as $block) {
			$descr = $block['descr'];
			$replaced = preg_replace('/[^\d-]*/','',$descr);
			$replaced = ltrim($replaced,'4');
			$replaced = ltrim($replaced,'34');
			if (strlen($replaced) > 8) {
				//echo $block['id'] . "\t". $replaced . "\n";
				$orderType = DB::table('dbo.v_sdscorecard')->where('OrderId',$replaced)->select('InterfaceTypeInt')->first();
				$alloc = 0;
				if ($orderType['InterfaceTypeInt'] == 47) {
					
					$alloc = 1;
				}
				$blocks = DB::connection('dev')->table('dbo.IPv4BillingToOldOrder')->where('OrderId',$block['orderid'])
					->update(['OtherOrder' => $replaced, 'OtherOrderIsAlloc' => $alloc, 'RawDescr' => $descr]);
			}
			
		}
		
		
	}
	
	
}

