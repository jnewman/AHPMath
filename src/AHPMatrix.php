<?php

/**
 * @author Joshua Newman
 */
class AHPMatrix extends Matrix{

	/**
	 * @param integer $power the power to be used when weighing the matrix
	 * @return array a collection of weighted elements
	 */
	public function weigh($power=100) {
		$powered = $this->pow($power);
		$total = $powered->sum();
		$rowSums = $powered->sum(1);
		$limit = $this->rowCount;
		$weights = array();
		while($limit--){
		    $weights[$limit] = $rowSums[$limit]/$total;
		}
		sort($weights);
		return $weights;
	}

}