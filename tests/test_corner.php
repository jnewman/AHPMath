<?php

    /**
     * Generate some random matrices
     */
    $c = array(
        rand(1,9),  1/rand(1,9),  rand(1,9),  rand(1,9),
                      rand(1,9),  rand(1,9),  rand(1,9),
                                1/rand(1,9),  rand(1,9),
                                            1/rand(1,9),
    );

    print_r($c);

    $corner = new Corner($c);

    print_r($corner);

    print $corner->toMatrix();
