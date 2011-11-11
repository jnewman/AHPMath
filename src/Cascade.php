<?php

class Cascade{

    /**
     * @var array
     */
    protected $data = array();

    /**
     * @var array
     */
    protected $factorCache = array();

    /**
     * @var array
     */
    protected $criteriaInnerScopeCache = array();

    /**
     * @var array
     */
    protected $criteriaOuterScopeCache = array();

    /**
     * @TODO really spend some time defining the data stream.
     *
     * @param stdClass $userData should be a collection of arrays containing
     * the minimum amount of data needed to calculate the weights of every
     * criterion.
     *
     * @desc Main purpose is to  serve as middle-ware between AHPMath and the
     * lower-level classes.
     *
     * For example, a factors comparison table like this:
     *
     *       +-----------------+
     *       |     |  3  |  6  |
     *       +-----------------+
     *       |     |     | 1/8 |
     *       +-----------------+
     *       |     |     |     |
     *       +-----------------+
     *
     * might look like this:
     *
     *     Array (
     *
     *         [id] => 1
     *
     *         [factorsComparisons] => Array
     *             (
     *
     *                 [0] => 3
     *                 [1] => 6
     *                 [2] => 0.125
     *
     *             )
     *
     *         [criteriaComparisons] => Array
     *             (
     *
     *                 [0] => Array
     *                         (
     *                             [0] => 3
     *                             [1] => 5
     *                             [2] => 0.2
     *                             [3] => 0.75
     *                         )
     *
     *                 [1] => Array
     *                         (
     *                             [0] => 3
     *                             [1] => 5
     *                             [2] => 0.2
     *                             [3] => 0.75
     *                         )
     *
     *                 [2] => Array
     *                         (
     *                             [0] => 3
     *                             [1] => 5
     *                             [2] => 0.2
     *                             [3] => 0.75
     *                         )
     *
     *             )
     *
     *     )
     *
     * Please note, that criteria_comparisons should have 1, and only 1, Array
     * for every factor.
     *
     */
    public function __construct(array $userData) {
        $this->data = $userData;
    }

    public function __toString() {
        return (string)var_export($this->data);
    }

    /**
     *
     * @param integer $ids
     * @return mixed (float || array[float])
     */
    public function factor($id=0) {
        if(empty($this->factorCache)) {
            $f = new Corner($this->data['factorsComparisons']);
            $this->factorCache = $f->toMatrix()->weigh();
        }
        if($id > 0) {
            // I rely on the offset, so just convert the id to an offset
            return $this->factorCache[$id - 1];
        } else {
            return $this->factorCache;
        }
    }

    /**
     *
     * @desc I leave up to the client to figure out which criterion they are
     * dealing with, but the order is maintained throughout the operations.
     *
     * @param integer $factor_id
     * @return array[array] || array[float]
     */
    public function criteria($factor_id=0, $compounded=true) {
        if($compounded) {
            $w = $this->getCriteriaOuterWeights();
        } else {
            $w = $this->getCriteriaInnerWeights();
        }

        if ($factor_id > 0) {
            return $w[($factor_id - 1)];
        } else {
            return $w;
        }
    }

    /**
     *
     * @return array[array]
     */
    protected function getCriteriaOuterWeights() {
        if(empty($this->criteriaOuterScopeCache)) {
            foreach ($this->getCriteriaInnerWeights() as $oKey => $children) {
                foreach ($children as $iKey => $child) {
                    $this->criteriaOuterScopeCache[$oKey][$iKey] = $child *
                        $this->factor($oKey + 1);
                }
            }
        }
        return $this->criteriaOuterScopeCache;
    }

    /**
     *
     * @return array[array]
     */
    protected function getCriteriaInnerWeights() {
        if(empty($this->criteriaInnerScopeCache)) {
            foreach ($this->data['criteriaComparisons'] as $children) {
                $c = new Corner($children);
                $this->criteriaInnerScopeCache[] = $c->toMatrix()->weigh();
            }
        }
        return $this->criteriaInnerScopeCache;
    }
}

