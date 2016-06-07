<?php

use Illuminate\Console\Command;
//use Config;

/**
 * Description of GetOrders
 *
 * @author tcassidy
 */
class FillInPortOrder extends Command
{
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'fillInPortOrder';

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
//		$string = 'dsjfk343sdflkd 1-9X9IF kljd32423';
//		$replaced = preg_replace("/.*([12]-[A-Za-z0-9]{5,9}).*/", "$1", $string);
//		echo $replaced;
//		return;
		$now = (new Carbon())->toDateTimeString();
		
		$blocks = DB::connection('prodsfip')->table('Starfish.adminsf.ip_block')
				->where('version',4)
				->where('port_order','')
				->where('descr','!=','')
				->where('descr','not like','%ICK%')
				->where('descr','not like','%import%')
				->where('descr','not like','%BGP%')
				->where('descr','not like','%vlan%')
				->where('orderid','!=','IPEng-Infrastr')
				->where('orderid','!=','')
				->get();
		$array = [];
		//echo var_dump($blocks);
		foreach ($blocks as $block) {
			$descr = $block['descr'];
			//$replaced = preg_replace('/[^\d-]*/','',$descr);
			$replaced = preg_replace("/.*([12]-[A-Za-z0-9]{5,9}).*/", "$1", $descr);
			$replaced = ltrim($replaced,'-');
			$replaced = ltrim($replaced,'0');
			$replaced = rtrim($replaced,'-');
			$replaced = trim($replaced);
			if (strlen($replaced) > 4 && strlen($replaced) < 12) {
				echo "$replaced\n";
				//DB::connection('dev')->table('Starfish.adminsf.ip_block_sync')->where('id',$block['id'])->update(['port_order' => $replaced]);
				$data = DB::table('dbo.v_sdscorecard')->where('OrderId',$replaced)->whereIn('InterfaceTypeInt',[6, 12, 10, 8, 13, 14, 33, 40, 42, 45])->whereIn('Layer',[3,4])->get();
				if ($data) {
					echo "yesport:\t$replaced\n";
					DB::connection('prodsfip')->table('Starfish.adminsf.ip_block')->where('netaddr',$block['netaddr'])
							->update(['port_order' => $replaced,
										'date' => $now,
										'user' => 'tcassidy'
									]);
				}
//				else {
//					$interface = '';
//					$data = DB::table('dbo.v_sdscorecard')->where('OrderId',$replaced)->select('InterfaceType')->first();
//					if ($data) {
//						$interface = $data['InterfaceType'];
//						if ($interface == 'IPv4') {
//							$theOrder = $block['orderid'];
//							$data = DB::table('dbo.v_sdscorecard')->where('OrderId',$theOrder)->select('InterfaceType')->first();
//							DB::connection('prodsfip')->table('Starfish.adminsf.ip_block')->where('netaddr',$block['netaddr'])
//							->update([	
//										'port_order' => $theOrder,
//										'orderid' => $replaced,
//										'descr' => $theOrder . ' - port order',
//										'date' => $now,
//										'user' => 'tcassidy'
//									]);
//							echo "$replaced\t$theOrder\n";
//							
//						}
//					}
//					//echo "notport:\t$replaced\t$interface\n";
//				}
				
				//DB::connection('dev')->table('Starfish.adminsf.ip_block_sync')->where('id',$block['id'])->update(['port_order' => $replaced]);
				
			}
			
		}
		print_r($array);
		
		
		
	}
	
//	if ($interface == 'BGP4') {
//							$descr = $block['descr'];
//							$descr .= ' - BGP4';
//							DB::connection('prodsfip')->table('Starfish.adminsf.ip_block')->where('netaddr',$block['netaddr'])
//							->update([	'descr' => $descr,
//										'date' => $now,
//										'user' => 'tcassidy'
//									]);
//						}
}

//if ($interface == 'BGPMULTI') {
//							$data = DB::table('mjain.OPM_Order_Details')->where('OrderId',$replaced)->select('ParentOrderId')->first();
//							$parent = trim($data['ParentOrderId']);
//							DB::connection('prodsfip')->table('Starfish.adminsf.ip_block')->where('netaddr',$block['netaddr'])
//							->update([	
//										'port_order' => $parent,
//										//'descr' => $descr,
//										'date' => $now,
//										'user' => 'tcassidy'
//									]);
//							
//						}