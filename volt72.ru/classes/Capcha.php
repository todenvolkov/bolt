<?php
$width = 100;        // Ширина изображения
$height = 30;        // Высота изображения
$sign = 5;            // Длинна кода
$img_code = '';

session_start();

// Символы, которые будут использованы в защитном коде
$letters = array('A','B','C','D','E','F','G','H','J','K','M','N',
                 'P','Q','R','S','T','U','V','W','X','Y','Z',
                 '0','1','2','3','4','5','6','7','8','9');
// Компоненты, используемые при создании для RGB-цвета
$digital_data = array(44,66,88,111,133,155,177,199);

$img = imagecreatetruecolor($width, $height);
$fon = imagecolorallocate($img, 255, 255, 255);    // Белый фон изображения
imagefill($img, 0, 0, $fon);

$letter_Width = intval((0.9*$width)/$sign);    // Ширина, отводимая под один символ
/*
for($j=0; $j<$width; $j++) {                // Заливка фона случайными точками
   for($i=0; $i<($height*$width)/600; $i++) {
     // Генерируем случайный цвет
     $color = imagecolorallocatealpha($img,
              $digital_data[rand(0,count($digital_data)-1)],
              $digital_data[rand(0,count($digital_data)-1)],
              $digital_data[rand(0,count($digital_data)-1)],
              rand(10,30));
     // Выводим случайную точку
     imagesetpixel($img, rand(0,$width), rand(0,$height), $color);
   }
}
*/
for($i=0; $i<$sign; $i++) {                        // Накладываем защитный код

   $color = imagecolorallocatealpha($img,
            $digital_data[rand(0,count($digital_data)-1)],
            $digital_data[rand(0,count($digital_data)-1)],
            $digital_data[rand(0,count($digital_data)-1)],
            rand(10,30));

   $letter = $letters[rand(0,sizeof($letters)-1)]; // Генерируем случайный символ

   // Координаты вывода символа
   if(empty($x)) { $x = intval($letter_Width*0.2); }
   else {
     if(rand(0,1))  $x = $x + $letter_Width + rand(0, intval($letter_Width*0.1));
     else $x = $x + $letter_Width - rand(0, intval($letter_Width*0.1));
   }
   $y = rand( intval($height*0.7), intval($height*0.8) );

   $size = rand(intval(0.4*$height), intval(0.5*$height));
   $angle = rand(0, 50) - 25;                    // Задаем угол поворота символа
   $img_code .= $letter;
   // Выводим сгенерированный символ на изображение
   imagettftext($img, $size, $angle, $x, $y, $color, "ARIALBD.TTF", $letter);
}
$_SESSION[img_code] = $img_code;

header ("Content-type: image/jpeg");
imagejpeg($img);
?>