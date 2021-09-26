<?php
$function = new \Twig\TwigFunction('unique_string', function () {
    return GenerateName(16,2);
});
$this->twig->addFunction($function);


$function = new \Twig\TwigFunction('format_money', function ($value) {
    return number_format($value, 0, '', ' ');
});
$this->twig->addFunction($function);

$function = new \Twig\TwigFunction('number_to_string', function ($value) {
    return num2str($value);
});


$function = new \Twig\TwigFunction('prupal', function ($number, $titles, $show_number=0){
    if( is_string( $titles ) )
        $titles = preg_split( '/, */', $titles );
    if( empty( $titles[2] ) )
        $titles[2] = $titles[1];

    $cases = [ 2, 0, 1, 1, 1, 2 ];

    $intnum = abs( (int) strip_tags( $number ));

    $title_index = ( $intnum % 100 > 4 && $intnum % 100 < 20 )
        ? 2
        : $cases[ min( $intnum % 10, 5 ) ];

    return ( $show_number ? "$number " : '' ) . $titles[ $title_index ];
});

$this->twig->addFunction($function);

$function = new \Twig\TwigFilter('date_diff', function ($date){

    $now = time();
    $your_date = strtotime($date);
    $date_diff = $your_date - $now;

    return floor($date_diff / (60 * 60 * 24));
});
$this->twig->addFilter($function);


$this->twig->addExtension(new \Twig\Extension\DebugExtension());
