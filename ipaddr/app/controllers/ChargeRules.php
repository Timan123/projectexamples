<?php

class ChargeRules
{
	
	public static function getMRC($ipCount, $custIpCount, $CDR) {
		$mrc = 0;
		$level = 'standard';
		$icb = '';
		if ($ipCount == 0) {
			
		}
		else if ($CDR == 0) {
			if ($custIpCount <= 256) {
				$mrc = 75; $level = 'premium';
				$icb = 'yes';
			} else {
				$mrc = .30 * $ipCount;
				$level = 'premium';
			}
		}
		else if ($custIpCount <= 256) {
			$mrc = 50;
		}
		else if ($custIpCount <= 512) { //23
			if ($CDR > 100) {
				$mrc = .20 * $ipCount; //standard
			}
			else {
				$level = 'premium';
				$mrc = .30 * $ipCount; //premium
			}
		}
		else if ($custIpCount <= 1024) { //22
			if ($CDR >= 1000) {
				$mrc = .20 * $ipCount; //standard
			}
			else {
				$level = 'premium';
				$mrc = .30 * $ipCount; //premium
			}
		}
		else if ($custIpCount <= 2048) { //21
			if ($CDR >= 10000) {
				$mrc = .20 * $ipCount; //standard
			}
			else {
				$mrc = .30 * $ipCount;
				$level = 'premium';
			}
		}
		else { //if over /21, use premium pricing and ignore CDR
			$mrc = .30 * $ipCount;
			$level = 'premium';
			
		}
		if ($custIpCount > 8192) {
			$icb = 'yes';
		}
		return ['mrc' => $mrc, 'level' => $level, 'icb' => $icb];
	}
	
	
}