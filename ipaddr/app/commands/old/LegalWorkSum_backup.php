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
			$careerSumHoursOff = 0;
			$careerSumHoursOffPlus8 = 0;
			$time1 = ' 00:00';
			$time2 = ' 7:59';
			$time3 = ' 18:01';
			$time4 = ' 23:59';
			while ($start <= $end) {
				$date = $start->toDateString();
				$dtime1 = $date . $time1;
				$dtime2 = $date . $time2;
				$dtime3 = $date . $time3;
				$dtime4 = $date . $time4;
				
				$weekend = $start->isWeekend();

				$callSum = DB::connection('scald')->table('callrecordsbothca16')
						->where('username',$username)
						->where(DB::raw('cast(CallStart as date)'),$date)
						->sum('duration');
				$callSum = $callSum / 60;
				
				$emailCount = DB::connection('scald')->table('emailrecordsbothca16')
						->where('username',$username)
						->where(DB::raw('cast(LocalDateTime as date)'),$date)->count();
				$emailCount *= 2;
				$compCount = DB::connection('scald')->table('CompRecordsCA16')->where('username',$username)->where(DB::raw('cast(LocalTime as date)'),$date)->count();

				$sfCount = DB::connection('scald')->table('SFRecordsCA16')->where('username',$username)->where(DB::raw('cast(created as date)'),$date)->count();
				$sfCount *= 2;
				
				$totalSum = $callSum + $emailCount + $compCount + $sfCount;
				$totalSumHours = round($totalSum/60, 0);
				
				//echo $weekend  . "\t" . $totalSumHours . "\n";
				$callSumOff = $callSum;
				$emailCountOff = $emailCount;
				$sfCountOff = $sfCount;
				$compCountOff = $compCount;
				if (!$weekend) {
					$callSumOff = DB::connection('scald')->table('callrecordsbothca16')
						->where('username',$username)
						->where(function($query) use ($dtime1, $dtime2, $dtime3, $dtime4) {
							$query->whereBetween('CallStart',[$dtime1,$dtime2])->orWhereBetween('CallStart',[$dtime3,$dtime4]);
						}) 
						->sum('duration');
					$callSumOff = $callSumOff / 60;
					
					$emailCountOff = DB::connection('scald')->table('emailrecordsbothca16')
						->where('username',$username)
						->where(function($query) use ($dtime1, $dtime2, $dtime3, $dtime4) {
							$query->whereBetween('LocalDateTime',[$dtime1,$dtime2])->orWhereBetween('LocalDateTime',[$dtime3,$dtime4]);
						}) 
						->count();
					$emailCountOff *= 2;
					
					$compCountOff = DB::connection('scald')->table('CompRecordsCA16')->where('username',$username)
						->where(function($query) use ($dtime1, $dtime2, $dtime3, $dtime4) {
							$query->whereBetween('LocalTime',[$dtime1,$dtime2])->orWhereBetween('LocalTime',[$dtime3,$dtime4]);
						}) 
						->count();
					
					$sfCountOff = DB::connection('scald')->table('SFRecordsCA16')->where('username',$username)
						->where(function($query) use ($dtime1, $dtime2, $dtime3, $dtime4) {
							$query->whereBetween('created',[$dtime1,$dtime2])->orWhereBetween('created',[$dtime3,$dtime4]);
						}) 
						->count();	
					$sfCountOff *= 2;
				}
				
				$totalSumOff = $callSumOff + $emailCountOff + $compCountOff + $sfCountOff;
				$totalSumHoursOff = round($totalSumOff/60, 0);
				$totalSumHoursOffPlus8 = $totalSumHoursOff;
				if (!$weekend) {
					$totalSumHoursOffPlus8 += 8;
				}
				
				$insert = [
					'Username' => $username,
					'Day' => $start->toDateString(),
					'SumHours' => $totalSumHours,
					'SumHoursOff' => $totalSumHoursOff,
					'SumHoursOffPlus8' => $totalSumHoursOffPlus8,
					'IsWeekEnd' => $weekend,
				];
				DB::connection('scald')->table('CA16Sum')->insert($insert);
				$careerSumHours += $totalSumHours;
				$careerSumHoursOff += $totalSumHoursOff;
				$careerSumHoursOffPlus8 += $totalSumHoursOffPlus8;
				$start->addDay();
			}
			DB::connection('scald')->table('CA16Sum_Summary')->insert([
				'Username' => $username,
				'Start' => $emp['start'],
				'End' => $emp['end'],
				'CareerSumHours' => $careerSumHours,
				'CareerSumHoursOff' => $careerSumHoursOff,
				'CareerSumHoursOffPlus8' => $careerSumHoursOffPlus8
				
			]);
		}
	}
	
	
}

