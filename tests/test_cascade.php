<?php

    $data = array(
            'id' => 1,

            'factorsComparisons' => array(1/rand(1,9), rand(1,9), 1/rand(1,9),
                                       rand(1,9), 1/rand(1,9), rand(1,9)),

            'criteriaComparisons' => array(

                    array(1/rand(1,9), rand(1,9), 1/rand(1,9), rand(1,9),
                          1/rand(1,9), rand(1,9)),

                    array(1/rand(1,9), rand(1,9), 1/rand(1,9), rand(1,9),
                          1/rand(1,9), rand(1,9)),

                    array(1/rand(1,9), rand(1,9), 1/rand(1,9), rand(1,9),
                          1/rand(1,9), rand(1,9)),

                    array(1/rand(1,9), rand(1,9), 1/rand(1,9), rand(1,9),
                          1/rand(1,9), rand(1,9)),

            ),
    );

    print_r($data);

    $c = new Cascade($data);

    print "\nWeights for criteria under factor 2, without broad scope\n";
    print_r($c->criteria(2, false));

    print "\nWeight for factor 2\n";
    print $c->factor(2);

    print "\n\nWeights for criteria under factor 2, with broad scope\n";
    print_r($c->criteria(2));