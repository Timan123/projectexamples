<?php

use Illuminate\Console\Command;
//use Config;

/**
 * Description of GetOrders
 *
 * @author tcassidy
 */
class VCReassign extends Command
{
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'vcReassign';

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
		
		

		$vcs = Config::get('rerun.VCs');	
//		echo var_dump($vcs);
//		return;

		foreach($vcs as $vc) {

			$parent = DB::table('TLG.mjain.OPM_Order_Details')->where('OrderId',$vc)->select(['ParentOrderId'])->first();
			$parent = trim($parent['ParentOrderId']);
			//echo $parent;
			$descr = "$vc - vlan";
			DB::connection('prodsfip')->table('Starfish.AdminSF.ip_block')
					->where('orderid',$vc)->where('version',4)
					->update([
					'descr' => $descr,
					'orderid' => $parent,
					'date' => $now,
					'user' => 'tcassidy'
					]);
			

			//only need to do one audit row, do outside the loop
			$auditRow = ['date' => $now, 'user' => 'tcassidy', 'section' => 'ip_block', 'action' => 'edit', 
				'detail' => "moved vlan ipv4 space for $vc to parent port $parent"];

			DB::connection('prodsfip')->table('Starfish.AdminSF.sf_audit')->insert($auditRow);

		
		
		
		
		}
	
	}
	
}
