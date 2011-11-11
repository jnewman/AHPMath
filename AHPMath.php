<?php
/**
 * Copyright (c) 2011, Monterey Institute of International Studies
 * All rights reserved.
 *
 * @license BSD type http://www.opensource.org/licenses/bsd-license.php
 * @author Joshua Newman
 *
 * Including the file will grant access to all objects available within the
 * AHPMath mini-library. To simplify access, everything is hidden behind a static
 * facade and called like so:
 *
 *     AHPMath::matrix(
 *         array(
 *             array(1,2),
 *             array(2,1)
 *         )
 *     );
 *
 * This package was built to perform somewhat simple matrix math on a PHP only
 * server.
 *
 */
    include "./src/Matrix.php";
    include "./src/AHPMatrix.php";
    include "./src/Corner.php";
    include "./src/Cascade.php";

class AHPMath{

    /**
     *
     * @param array $data
     * @return Cascade a custom object wih a series of AHP specific methods
     * focused around the user.
     */
    public static function user(array $data) {
        $c = new Cascade($data);
        return $c;
    }

    /**
     *
     * @param array $data a 2D array
     * @return AHPMatrix a custom object type defined in ./src/matrix.php
     */
    public static function matrix(array $data) {

    	return new AHPMatrix($data);

    }

    /**
     *
     * @param array $corner
     * @return AHPMatrix
     */
    public static function inflate(array $corner) {
        $c = new Corner($corner);
        return $c->toMatrix();
    }


}