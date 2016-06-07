<?php


use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Symfony\Component\Console\Input\InputOption;


class SyncProv extends Command
{
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'cron:sync-prov';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Sync the Prov\'s from the ProvTool System.';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		//$date = Carbon::today();
		DB::statement('SET ANSI_NULLS ON; SET ANSI_WARNINGS ON');
		
		DB::statement('truncate table dbo.ProvWorkEvent');
		DB::statement('truncate table dbo.ProvWorkEvent_FEs');

		$results = DB::table('TLG.mjain.OrderHistory as oh')
			->leftJoin('TLG.mjain.TABLE_V_SIEBELORDERS as s','oh.OrderId','=','s.Order_Num')
			->leftJoin('TLG.mjain.BuildingsExtraCols as bec','bec.NodeId','=',DB::raw('s.[Shipping Address BuildingID]'))	
			->leftJoin('TLG.dbo.Buildings as b','b.BuildingID','=','bec.BuildingID')
			->leftJoin('TLG.dbo.CogentMarket as m','b.CogentMarket','=','m.CogentMarket')	
			->select(['b.CogentMarket as Market',
					'oh.OrderId',
					DB::raw('rtrim(oh.ByUser) as ByUser'),
					'oh.OnDate',
					'm.Continent as Region'])
				->distinct()
				->whereRaw("oh.Description like '%Work Order Email Sent out%'")
				->whereRaw("OnDate >= dateadd(m,-6,getdate())")
				->get();
		
		foreach($results as $row) {
			$orderId = $row->OrderId;
			$onDate = $row->OnDate;
			
			$row = (array) $row;
			$id = DB::table('dbo.ProvWorkEvent')->insertGetId($row);
			
			$results2 = DB::table('TLG.mjain.Order_Notes')->select(['Description'])
					->where('OrderId',$orderId)->whereRaw("Description like '<B>Work Order  </B>%'")
					->where('InsertDt',$onDate)->first();
			if ($results2) {
				$desc = $results2->Description;
				$regex = '#<td>To</td><td>(.*?)</td>#';
				if (preg_match($regex, $desc ,$matches)) {
					$to = $matches[1];
					$to = strtolower($to);
					$to = str_replace(' ','',$to);
					$to = str_replace('@cogentco.com','',$to);
					$arr = explode(',', $to);
					foreach ($arr as $FE) {
						if ($FE) {
							if (substr($FE,0,1) != '#') {
								DB::table('dbo.ProvWorkEvent_FEs')->insert(['OrderId' => $orderId, 'FE' => $FE, 'ProvId' => $id]);
							}
						}

					}
				}
			}
		}

		DB::statement('SET ANSI_NULLS OFF; SET ANSI_WARNINGS OFF');
		
		//echo json_encode($results);
	}
}