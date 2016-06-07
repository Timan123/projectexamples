<?php

use Illuminate\Console\Command;
//use Config;

/**
 * Description of GetOrders
 *
 * @author tcassidy
 */
class NonProjBlockReassign extends Command
{
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'nonProjBlockReassign';

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
		
		


		$allocOrder = '1-300016210';
		$customer = 'G11DI39208';
		//only need to reassign port orders that have some ip space
		$portOrders =  ['1-189719412'];

		foreach($portOrders as $portOrder) {

//			$dataForHist = DB::connection('prodsfip')->table('Starfish.AdminSF.ip_block')->where('OrderId',$portOrder)->where('version',4)
//				->select([	'assigned',
//							'assignedby',
//							DB::raw("'$now' as 'freed'"),
//							DB::raw("'tcassidy' as 'freedby'"),
//							'netaddr',
//							'netend',
//							'netmask',
//							'status',
//							'custid',
//							'orderid'
//						])->get();
//			if ($dataForHist) {
//				DB::connection('prodsfip')->table('Starfish.AdminSF.ip_history')->insert($dataForHist);
//			}
			//$descr = "$portOrder - port order";
			DB::connection('prodsfip')->table('Starfish.AdminSF.ip_block')
				->where('orderid',$portOrder)->where('version',4)
				->update([
					'port_order' => $portOrder,
					'orderid' => $allocOrder,
					'date' => $now,
					'user' => 'tcassidy'
				]);
		}
		//only need to do one audit row, do outside the loop
		$auditRow = ['date' => $now, 'user' => 'tcassidy', 'section' => 'ip_block', 'action' => 'edit', 
			'detail' => "moved port and lag ipv4 space for ports on globallogoid=$customer to ipv4alloc order $allocOrder"];

		DB::connection('prodsfip')->table('Starfish.AdminSF.sf_audit')->insert($auditRow);
		echo "finished $allocOrder\n";
		
		
		
		
	}
	
	
}
