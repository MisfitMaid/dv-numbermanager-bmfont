<?php

require_once 'vendor/autoload.php';
require_once 'funcs.php';

$imgr = new \Intervention\Image\ImageManager(['driver' => 'imagick']);

$fnt = \qp(file_get_contents($argv[1]));
$font = $fnt->find("font");

$nmDesc = new DOMDocument();
$numcon = $nmDesc->createElement("NumberConfig");
$numconFonts = $nmDesc->createElement("Fonts");

$lineHeight = $font->find("common")->attr("lineHeight");

$tileName = $font->find("pages>page")->attr("file");
$tileWidth = $font->find("common")->attr("scaleW");
$tileHeight = $font->find("common")->attr("scaleH");

$tile = $imgr->make($tileName);
$canvas = $imgr->canvas($tileWidth*2, $tileHeight*2);

$canvas->insert($tile, 'top-left');
$tile->rotate(-90);
$canvas->insert($tile, 'top-right');
$tile->rotate(-90);
$canvas->insert($tile, 'bottom-right');
$tile->rotate(-90);
$canvas->insert($tile, 'bottom-left');

// build font atlas info
$glyphs = [];
foreach($font->find("chars>char") as $char) {
    $x = [];
    $x['width'] = $char->attr("width");
    $x['x'] = $char->attr("x");
    $x['y'] = $char->attr("y") + $char->attr("height");
    $glyphs[chr($char->attr("id"))] = $x;
}



addGlyphsToNode($numconFonts, $glyphs, $lineHeight, $canvas);
$glyphs = rotateGlyphArrayCW($glyphs, $tileHeight);
addGlyphsToNode($numconFonts, $glyphs, $lineHeight, $canvas);
$glyphs = rotateGlyphArrayCW($glyphs, $tileHeight);
addGlyphsToNode($numconFonts, $glyphs, $lineHeight, $canvas);

$canvas->save("out.png");

$numcon->append($numconFonts);
$nmDesc->append($numcon);
echo $nmDesc->saveXML();