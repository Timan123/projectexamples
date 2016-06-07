<?php

use Illuminate\Console\Command;

/**
 * Description of GetOrders
 *
 * @author tcassidy
 */
class LegalWorkSum extends Command
{
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'legalWorkSum';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Legal Work';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{ 

		

		$legalSet = Config::get('legal.legalSet');
		foreach($legalSet as $emp) {
			$username = $emp['username'];
			
			$start = new Carbon($emp['start']);
			$end = new Carbon($emp['end']);
	//		$username = 'reggers';
	//		$start = new Carbon('2010-09-16');
	//		$end = new Carbon('2010-09-17');
			echo "$username\n";
			$careerSumHours = 0;
			while ($start <= $end) {
				if ($start->isWeekend()) {
					$start->addDay();
					continue;
				}
				$dt = clone $start;
				$dt->hour = 8;
				$endDay = clone $start;
				$endDay->hour = 18;
				$totalMin = 0;
				while ($dt <= $endDay) {
					$next10 = clone $dt;
					$next10->addMinutes(10);
					$callSum = DB::connection('scald')->table('callrecordsbothca16')
						->where('username',$username)->whereBetween('CallStart',[$dt,$next10])
						->sum('duration');
					if ($callSum > 600) {
						$inMin = round($callSum / 60, 0);
						$totalMin += $inMin;
						$by10 = round($callSum / 60, -1);
						$dt->addMinutes($by10);
						continue;
					} else if ($callSum > 0) {
						$totalMin += 10;
						$dt->addMinutes(10);
						continue;
					}
					$emailCount = DB::connection('scald')->table('emailrecordsbothca16')
						->where('username',$username)
						->whereBetween('LocalDateTime',[$dt,$next10])->count();
					if ($emailCount > 0) {
						$totalMin += 10;
						$dt->addMinutes(10);
						continue;
					}
					$sfCount = DB::connection('scald')->table('SFRecordsCA16')->where('username',$username)->whereBetween('created',[$dt,$next10])->count();
					if ($sfCount > 0) {
						$totalMin += 10;
						$dt->addMinutes(10);
						continue;
					}
					$compCount = DB::connection('scald')->table('CompRecordsCA16')->where('username',$username)->whereBetween('LocalTime',[$dt,$next10])->count();
					if ($compCount > 0) {
						$totalMin += 10;
						$dt->addMinutes(10);
						continue;
					}
					$dt->addMinutes(10);
				}
				$totalHours = round($totalMin / 60, 0);
				$insert = [
					'Username' => $username,
					'Day' => $start->toDateString(),
					'SumHours' => $totalHours
				];
				DB::connection('scald')->table('CA16TenBlock')->insert($insert);
				$careerSumHours += $totalHours;
				$start->addDay();
			}
			DB::connection('scald')->table('CA16TenBlock_Summary')->insert([
				'Username' => $username,
				'Start' => $emp['start'],
				'End' => $emp['end'],
				'CareerSumHours' => $careerSumHours
			]);
		}
	}
	
	
}

