<?php ini_set('display_errors',1); error_reporting(E_ALL); ?>
<?php
    include './AHPMath.php';
?>

<pre>

<?php
    if(isset($_GET['t'])) {
        include sprintf('./tests/test_%s.php',
                    str_replace(
                        '/',
                        '',
                        strtolower($_GET['t'])
                    )
                );
    } else {
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

        $u = AHPMath::user($data);
        $u->criteria(3);

        printf("Data followed by Calculated weights (offsets maintained)\n");
        print_r($u);

    }


