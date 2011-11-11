<?php

/**
 *
 * This class based on code posted by "Exception e" here: http://goo.gl/b23oR
 *
 * The objective is to reimplement a critical subset of the NumPy library
 * (numpy.scipy.org) with PHP > 5.2, so that AHP calculations can be performed
 * on a PHP only server.
 *
 * The original had some serious mistakes (e.g., use of && instead of || while
 * validating) and was missing a fair number of important methods (e.g., pow),
 * but seemed a reasonable place to start, so this was begun from it.
 *
 */
class Matrix{

    /**
     * @var array
     */
    protected $matrix;

    /**
     * @var integer
     */
    protected $rowCount = 0;

    /**
     * @var integer
     */
    protected $colCount = 0;

    /**
     * Constructs a new Matrix from a given two-dimensional array
     * @param array $matrix a two-dimensional array.
     *        $matrix must be indexed without gaps, starting from 0
     */
    public function __construct(&$matrix) {
        $this->matrix =& $matrix;
        $this->rowCount = count($matrix);
        $this->colCount = count($matrix[0]);
    }

    /**
     * @param int $index key of the row to be retrieved.
     *        Possible values ranges from 0 to numberOfRows - 1
     * @return array the row of the matrix at position $index
     */
    public function getRow($index) {
        if (!array_key_exists($index, $this->matrix)) {
            throw new Exception('Invalid row index');
        }
        return $this->matrix[$index];
    }

    /**
     * if $index is an invalid key of the row, an exception will be thrown
     * @param int $index key of the row that has to be retrieved.
     *        Possible values ranges from 0 to numberOfRows - 1
     * @return array the column of the matrix at position $index
     */
    public function getCol($index) {
        $result = array();

        if ($index > $this->colCount - 1 || $index < 0) {
            throw new Exception('Invalid column index');
        }
        foreach ($this->matrix as $row) {
            $result[] = $row[$index];
        }
        return $result;
    }

    /**
     * @param mixed $rhs
     * if $rhs is numeric, each value of this matrix is multiplied by $rhs
     * (scalar multiplication).
     * if $rhs is a Matrix, an ordinary matrix product will be returned
     * @return Matrix
     */
    public function multiply($rhs) {
        if (is_numeric($rhs)) {
            return $this->multiplyByNumber($rhs);
        } elseif ($rhs instanceof Matrix) {
            return $this->multiplyByMatrix($rhs);
        } else{
            throw new Exception('Operand must be a number or another Matrix');
        }
    }

    /**
     *
     * @param integer optional $axis (defaults to 2)
     * @return mixed (array || float)
     *     if $axis is 0 returns array colSums
     *     if $axis is 1 returns array rowSums
     *     Otherwise returns float matrix sum
     */
    public function sum($axis=2) {
        if(!is_numeric($axis) || $axis > 2) {
            throw new Exception('@param $axis must be an integer < 3.');
        }
        if($axis === 0) {
            return $this->sumCols();
        } elseif ($axis === 1) { // e.g., sumRows
            return $this->sumRows();
        } else {
            return $this->sumMatrix();
        }
    }

    /**
     * Adds this and $rhs matrix.
     * @param Matrix $rhs
     * @return Matrix
     */
    public function add(Matrix $rhs) {
        if ($rhs->rowCount !== $this->rowCount ||
            $rhs->colCount !== $this->colCount) {
            throw new Exception('Matrices must have same dimensions');
        }

        for ($i = 0; $i < $this->rowCount; $i++) {
            $result[$i] = array();
            for ($j = 0; $j < $this->colCount; $j++) {
                $result[$i][$j] = $this->matrix[$i][$j] + $rhs->matrix[$i][$j];
            }
        }
        return new Matrix($result);
    }

    /**
     * Subtracts $rhs from this.
     * @param Matrix $rhs
     * @return Matrix
     */
    public function subtract(Matrix $rhs) {
        if ($rhs->rowCount !== $this->rowCount ||
            $rhs->colCount !== $this->colCount) {
            throw new Exception('Matrices must have same dimensions');
        }

        for ($i = 0; $i < $this->rowCount; $i++) {
            $result[$i] = array();
            for ($j = 0; $j < $this->colCount; $j++) {
                $result[$i][$j] = $this->matrix[$i][$j] - $rhs->matrix[$i][$j];
            }
        }
        return new Matrix($result);
    }

    /**
     * Getter for the number of rows
     * @return integer
     */
    public function countRows() {
        return $this->rowCount;
    }

    /**
     * Getter for the number of columns
     * @return integer
     */
    public function countCols() {
        return $this->colCount;
    }

    /**
     * @param integer $exponent
     * @return Matrix $this raised to the $exponent power
     */
    public function pow($exponent) {
        if (!is_int($exponent)) {
            throw new Exception('@param $exponent must be an integer.');
        }
        $self = new Matrix($this->matrix);
        while(--$exponent)
            $self = $this->multiplyByMatrix($self);
        return $self;
    }

    /**
     * HTML representation of the Matrix
     * @return string tabulated version of matrix
     */
    public function __toString() {
        $copy = $this->matrix;
        $str = '<table border=1>';
        foreach($copy as $row){
            $str .= '<tr><td>'.implode('</td><td>', $row).'</td></tr>';
        }
        return $str.'</table>';
    }

    /**
     * @return array
     */
    protected function sumRows() {
        $totals = array();
        $limit = $this->rowCount;
        for($i=0; $i<$limit; $i++){
            $totals[] = array_sum($this->getRow($i));
        }
        return $totals;
    }

    /**
     * @return array
     */
    protected function sumCols() {
        $totals = array();
        $limit = $this->colCount;
        for($i=0; $i<$limit; $i++){
            $totals[] = array_sum($this->getCol($i));
        }
        return $totals;
    }

    /**
     * @return integer
     */
    protected function sumMatrix() {
        $total = 0;
        foreach($this->matrix as $arr){
            $total += array_sum($arr);
        }
        return $total;
    }

    /**
     * @param integer $number
     * @return Matrix
     */
    private function multiplyByNumber($number) {
        for ($i = 0; $i < $this->rowCount; $i++) {
            $result[$i] = array();
            for ($j = 0; $j < $this->colCount; $j++) {
                $result[$i][$j] = $this->matrix[$i][$j] * $number;
            }
        }
        return new Matrix($result);
    }

    /**
     * @param Matrix $rhs
     * @return Matrix
     */
    private function multiplyByMatrix(Matrix $rhs) {
        if ($this->colCount !== $rhs->rowCount) {
            throw new Exception('Matrices must have same dimensions');
        }
        $res = array();
        $selfCopy = $this->matrix;
        $i = 0;
        foreach($selfCopy as $row) {
            $result[$i] = array();
            for ($j = 0; $j < $rhs->colCount; $j++) {
                $result[$i][$j] = 0;
                for ($k = 0; $k < $this->colCount; $k++) {
                    $result[$i][$j] += $row[$k] * $rhs->matrix[$k][$j];
                }
            }
            $i++;
        }
        return new Matrix($result);
    }

}