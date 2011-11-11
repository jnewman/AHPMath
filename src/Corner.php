<?php

class Corner{

    protected $data = array();

    /**
     * @author Joshua Newman
     * @param array $seqence
     * @desc $sequence should be the n*(n-1)/2 portion of a square matrix.
     * For example, this sequence:
     *
     *     $corner = array(
     *         2,  1/3,  4,  5,
     *               6,  9,  2,
     *                 1/3,  5,
     *                     1/2,
     *     );
     *
     *    $c = new Corner($corner);
     *
     * Results in:
     *
     *     array(
     *         array(   1,   2, 1/3,   4,   5,),
     *         array( 1/2,   1,   6,   9,   2,),
     *         array(   3, 1/6,   1, 1/3,   5,),
     *         array( 1/4, 1/9,   3,   1, 1/2,),
     *         array( 1/5, 1/2, 1/5,   2,   1,),
     *     );
     *
     */
    public function __construct(array $sequence) {
        $this->width = self::widthFromResults(count($sequence));
        $this->data = $this->toCorner($sequence);
    }


    /**
     * @return string a reasonable representation of the Corner as an
     * evaluatable string.
     */
    public function __toString() {
        return (string)var_export($this->data);
    }

    /**
     * @return Matrix
     */
    public function toMatrix() {
        $right = array_filter($this->data);
        // Make internal data ready for use as left-side.
        $this->pad()->transpose()->reciprocate()->data;
        foreach ($right as $key => $row) {
            $this->data[$key] = array_merge($this->data[$key], $row);
        }
        unset($row);
        return new AHPMatrix($this->data);
    }

    /**
     * @param array $sequence
     * @return Corner
     */
    protected function toCorner(array $sequence) {
        $corner = array();
        $length = self::resultsFromWidth($this->width);
        $w = $this->width - 1; // Since I haven't padded it yet.

        while ($w--){
            $row = array();
            $range = range(0, $w);
            foreach ($range as $i) {
                $row[] = array_shift($sequence);
            }
            $corner[] = $row;
        }

        return $corner;
    }


    /**
     *
     * @return Corner
     */
    protected function transpose() {
        if (count($this->data[0]) < count($this->data[1])) {
            throw new Exception("
                The first row must be longer than the second row.
            ");
        }
        $limit = count($this->data);
        $parts = array();
        for($i = 0; $i < $limit; $i++) {
            $row = array();
            $j = 0;
            while ($j < $i + 1) {
                $row[] = array_shift($this->data[$j++]);
            }
            $parts[] = $row;
        }

        $this->data = $parts;
        return $this;
    }

    protected function pad() {
        // Instead of real branches, just flip and flag
        $flag = 0;
        if(count($this->data[0]) < count($this->data[1])){
            $this->transpose();
            $flag = 1;
        }

        $this->data[] = array(1);
        $i = 0;
        while ($i < $this->width - 1) {
            array_unshift($this->data[$i++], 1);
        }
        if($flag) $this->transpose();

        return $this;

    }

    protected function reciprocate() {
        foreach ($this->data as &$row) {
            foreach ($row as &$value) {
                $value = 1 / $value;
            }
            unset($value); // Remove the pointer to avoid overwriting.
        }
        unset($row); // Remove the pointer to avoid overwriting.
        return $this;
    }

///////////////////////////////////////////////////////////////////////////////
//// What follows was created by Brent Kesler for an earlier project and later
//// ported to PHP by @author Josh Newman
///////////////////////////////////////////////////////////////////////////////

    /**
     *
     * @param integer $width
     * @return integer number of elements necessary to permute $width elements.
     */
    final private static function resultsFromWidth($width) {
        return $width * ($width - 1) / 2;
    }

    /**
     * @param integer $results
     * @return integer
     * @desc Given:
     *     width-squared = 2r + width --> width = sqrt(2r + width)
     *     width > 0                  --> 2r < 2r + width
     *     results > width            --> 2r + width < 3r
     *     --> 2r < 2r + width < 3r
     *     --> sqrt(2r) < width < sqrt(3r)
     *
     *     width is an integer        -->
     */
    final private static function widthFromResults($results) {
        $min = ceil(sqrt(2 * $results));
        $max = floor(sqrt(3 * $results));
        // Test resultsFromWidth for values between $min and $max
        return self::widthFromResultsHelper($results, $min, $max);
    }

    /**
     *
     * @param integer $results
     * @param integer $min
     * @param integer $max
     */
    final private static function widthFromResultsHelper($results, $min, $max) {
        /*
         * if($min === $max) doesn't work if $results === 2
         */
        if ($min >= $max) {
            return $max;
        } else {
            /*
             * Instead of going through all numbers between min and max, we'll
             * test a possible width near the halfway point and cut down the
             * range.
             */
            $testWidth = $min + $max - $min / 2;
            $testResults = self::resultsFromWidth($testWidth);

            if($testResults === $results) {
                return $testResults;

            } elseif($testResults > $results) {
                // Cut off the top half of the range.
                return self::widthFromResultsHelper($results, $min, $testWidth);

            } else { // i.e., elif($testResults < $results)
                // Cut off the bottom half of the range.
                return self::widthFromResultsHelper($results, $testWidth, $max);
            }
        }
    }
}






