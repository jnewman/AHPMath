<?php

    /**
     * Generate some random matrices
     */
    $a = array(
        array(1, ($two = rand(1, 9)), ($three = rand(1, 9)), ),
        array(1 / $two, 1, ($four = rand(1, 9)), ),
        array(1 / $three, 1/$four, 1, ),
    );
    $b = array(
        array(1, ($two = rand(1, 9)), ($three = rand(1, 9)), ),
        array(1 / $two, 1, ($four = rand(1, 9)), ),
        array(1 / $three, 1/$four, 1, ),            
    );
    
    
    $alpha = AHPMath::matrix($a);
    print 'a = '.$alpha;
    
    print '<hr /> a * 6 = ';
    print $alpha->multiply(6);
    
    print '<hr /> a ** 10 = ';
    print $alpha->pow(10);
    
    print '<hr /> a->sum = ';
    print $alpha->sum();
    
    print '<hr /> a->sum(rows) = ';
    print_r($alpha->sum(1));
    
    print '<hr /> a->sum(cols) = ';
    print_r($alpha->sum(0));
    
    $beta = AHPMath::matrix($b);
    print '<hr /> b = '.$beta;
    
    print '<hr /> a + b = ';
    print $alpha->add($beta);
    
    print '<hr /> a->weigh() = ';
    print_r($alpha->weigh());