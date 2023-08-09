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

$indexes = ['', 'font indexes:'];

$indexes[] = "0: upright, LTR";
addGlyphsToNode($numconFonts, $glyphs, $lineHeight, false, false, 'none');
$indexes[] = "1: upright, RTL";
addGlyphsToNode($numconFonts, $glyphs, $lineHeight, false, true, 'none');

$glyphs = rotateGlyphArrayCW($glyphs, $tileHeight);
$indexes[] = "2: clockwise, LTR";
addGlyphsToNode($numconFonts, $glyphs, $lineHeight, true, false, 'awy');
$indexes[] = "3: clockwise, RTL";
addGlyphsToNode($numconFonts, $glyphs, $lineHeight, true, true, 'awy');

$glyphs = rotateGlyphArrayCW($glyphs, $tileHeight);
$indexes[] = "4: upside-down, LTR";
addGlyphsToNode($numconFonts, $glyphs, $lineHeight, false, false, 'swx,ahy');
$indexes[] = "5: upside-down, RTL";
addGlyphsToNode($numconFonts, $glyphs, $lineHeight, false, true, 'swx,ahy');

$glyphs = rotateGlyphArrayCW($glyphs, $tileHeight);
$indexes[] = "6: counterclockwise, LTR";
addGlyphsToNode($numconFonts, $glyphs, $lineHeight, true, false, 'shx');
$indexes[] = "7: counterclockwise, RTL";
addGlyphsToNode($numconFonts, $glyphs, $lineHeight, true, true, 'shx');

$indexes[] = '';
$canvas->save("num.png");

$numcon->append($numconFonts);

$numconAP = $nmDesc->createElement("AttachPoints");

$com = $nmDesc->createComment(implode(PHP_EOL, $indexes));
$numconAP->append($com);

$ap = $nmDesc->createElement("NumAttachPoint");
$ap->setAttribute("FontIdx", "XXX");
$ap->setAttribute("X", "XXX");
$ap->setAttribute("Y", "XXX");
$numconAP->append($ap);

$numcon->append($numconAP);

$numcon->setAttribute("TargetTexture", "XXX");
$numcon->setAttribute("BlendMode", "Normal");
$numcon->setAttribute("MinNumber", "0");
$numcon->setAttribute("MaxNumber", "0");
$numcon->setAttribute("Offset", "0");
$numcon->setAttribute("ForceRandom", "false");

$nmDesc->append($numcon);
$nmDesc->formatOutput = true;
file_put_contents("numbering.xml", $nmDesc->saveXML());