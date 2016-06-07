<?php

use Illuminate\Console\Command;

/**
 * Description of GetOrders
 *
 * @author tcassidy
 */
class LegalWork extends Command
{
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'legalWork';

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
			$careerWeekday = 0;
			$careerWeekend = 0;
			$careerWeekendTrunc = 0;
			$careerWeekend5Min = 0;
			while ($start <= $end) {
				$weekend = $start->isWeekend();

				$callCount = DB::connection('scald')->table('callrecordsca16')->where('username',$username)->where(DB::raw('cast(CallStart as date)'),$start)->count();
				$callMin = 	new Carbon(
						DB::connection('scald')->table('callrecordsca16')->where('username',$username)->where(DB::raw('cast(CallStart as date)'),$start)->min('CallStart')
				);
				$callMax = new Carbon(
						DB::connection('scald')->table('callrecordsca16')->where('username',$username)->where(DB::raw('cast(CallStart as date)'),$start)->max('CallStart')
				);


				$emailCount = DB::connection('scald')->table('emailrecordsca16')->where('username',$username)->where(DB::raw('cast(LocalDateTime as date)'),$start)->count();
				$emailMin =  new Carbon(
						DB::connection('scald')->table('emailrecordsca16')->where('username',$username)->where(DB::raw('cast(LocalDateTime as date)'),$start)->min('LocalDateTime')
				);
				$emailMax =  new Carbon(
						DB::connection('scald')->table('emailrecordsca16')->where('username',$username)->where(DB::raw('cast(LocalDateTime as date)'),$start)->max('LocalDateTime')
				);


				$compCount = DB::connection('scald')->table('CompRecordsCA16')->where('username',$username)->where(DB::raw('cast(LocalTime as date)'),$start)->count();
				$compMin =  new Carbon(
						DB::connection('scald')->table('CompRecordsCA16')->where('username',$username)->where(DB::raw('cast(LocalTime as date)'),$start)->min('LocalTime')
						);
				$compMax =  new Carbon(
						DB::connection('scald')->table('CompRecordsCA16')->where('username',$username)->where(DB::raw('cast(LocalTime as date)'),$start)->max('LocalTime')
				);


				$sfCount = DB::connection('scald')->table('SFRecordsCA16')->where('username',$username)->where(DB::raw('cast(created as date)'),$start)->count();
				$sfMin =  new Carbon(
						DB::connection('scald')->table('SFRecordsCA16')->where('username',$username)->where(DB::raw('cast(created as date)'),$start)->min('created')
						);
				$sfMax =  new Carbon(
						DB::connection('scald')->table('SFRecordsCA16')->where('username',$username)->where(DB::raw('cast(created as date)'),$start)->max('created')
						);

	//			$lookUp = [
	//						$callMin => 'call',
	//						$callMax => 'call',
	//						$emailMin => 'email',
	//						$emailMax => 'email',
	//						$compMin => 'comp',
	//						$compMax => 'comp',
	//						$sfMin => 'sf',
	//						$sfMax => 'sf'
	//				
	//				
	//			];
				$dayCount = $callCount + $emailCount + $compCount + $sfCount;
				$nonEmailDayCount = $callCount + $compCount + $sfCount;
				$minCause = '';
				$maxCause = '';
				$diff = 0;
				if ($dayCount == 0) {
					$min = '00:00:00';
					$max = '00:00:00';
				} else {
					$min = min( [$callMin, $emailMin, $compMin, $sfMin] );
					$max = min( [$callMax, $emailMax, $compMax, $sfMax] );


					if ($min == $callMin) {	$minCause = 'call';	}
					if ($min == $emailMin) {	$minCause = 'email';	}
					if ($min == $compMin) {	$minCause = 'comp';	}
					if ($min == $sfMin) {	$minCause = 'sf';	}
					if ($max == $callMax) {	$maxCause = 'call';	}
					if ($max == $emailMax) {	$maxCause = 'email';	}
					if ($max == $compMax) {	$maxCause = 'comp';	}
					if ($max == $sfMax) {	$maxCause = 'sf';	}
					$diff = $max->diffInHours($min);
					$min = $min->toTimeString();
					$max = $max->toTimeString();
				}

	//			$lookedUpMin = $lookUp[$min->toDateTimeString()];
	//			$lookedUpMax = $lookUp[$max->toDateTimeString()];
	//			echo $lookedUpMin;

				$weekDayOver = 0;
				$weekEndOver = 0;
				$weekEndOverTrunc = 0;
				$weekEndOver5Min = 0;
				if (!$weekend) {
					$weekDayOver = $diff - 8;
					if ($weekDayOver < 0) {
						$weekDayOver = 0;
					}
				} else {
					if ($diff > 0 && $dayCount < 10) {
						$weekEndOverTrunc = 1;
					} else {
						$weekEndOverTrunc = $diff;
					}
					$weekEndOver = $diff;
					$weekEndOver5Min = intval(($dayCount * 5) / 60);
				}
				$insert = [
					'Username' => $username,
					'Day' => $start->toDateString(),
					'Min' => $min,
					'MinType' => $minCause,
					'Max' => $max, 
					'MaxType' => $maxCause, 
					'IsWeekEnd' => $weekend,
					'HoursDiff' => $diff,
					'WeekDayOver' => $weekDayOver,
					'WeekEndOver' => $weekEndOver, 
					'WeekEndOverTrunc' => $weekEndOverTrunc, 
					'WeekEndOver5Min' => $weekEndOver5Min,
					'ActCount' => $dayCount,
					'NonEmailActCount' => $nonEmailDayCount
				];
				$careerWeekday += $weekDayOver;
				$careerWeekend += $weekEndOver;
				$careerWeekendTrunc += $weekEndOverTrunc;
				$careerWeekend5Min += $weekEndOver5Min;
				//echo "min: " . $min->toDateString() . " max: " . $max->toDateString() . "\n";
				DB::connection('scald')->table('CA16')->insert($insert);

				$start->addDay();
			}
			
			DB::connection('scald')->table('CA16_Sum')->insert([
				'Username' => $username,
				'Start' => $emp['start'],
				'End' => $emp['end'],
				'CareerWeekdayOver' => $careerWeekday,
				'CareerWeekendOver' => $careerWeekend,
				'CareerWeekendOverTrunc' => $careerWeekendTrunc,
				'CareerWeekendOver5Min' => $careerWeekend5Min,
				
			]);
		}	
	}
	
	
}

