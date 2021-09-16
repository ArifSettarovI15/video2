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



$function = new \Twig\TwigFunction('passengers_to_array', function ($passengers){
    $passengers = explode(',', $passengers);
    $data = array();
    foreach ($passengers as $passenger) {
        if(preg_match('/Взрослые: (.*)/', $passenger, $match)){
            $data['adult'] = $match[1];
        }


        if(preg_match('/Дети до 7 лет: (.*)/', $passenger, $match)){
            $data['kidsTo7'] = $match[1];
        }

        if(preg_match('/Дети от 3 до 5 лет: (.*)/', $passenger, $match)){
            $data['kids3to7'] = $match[1];
        }

        if(preg_match('/Дети до 1 годика: (.*)/', $passenger, $match)){
            $data['babies'] = $match[1];
        }

    }

    return $data;
});
$this->twig->addFunction($function);


$this->twig->addExtension(new \Twig\Extension\DebugExtension());
