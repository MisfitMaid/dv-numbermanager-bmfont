<?php

use Intervention\Image\Image;

function addGlyphsToNode(DOMElement $elem, array $glyphs, int $height, Image $debug): void {
    ksort($glyphs);

    $width = [];
    $x = [];
    $y = [];
    foreach ($glyphs as $v) {
        $width[] = $v['width'];
        $x[] = $v['x'];
        $y[] = $v['y'];
        $debug->circle(3, $v['x'], $v['y'], function ($draw) {
            $draw->background('#0000ff');
        });
    }

    $numfon = $elem->ownerDocument->createElement("NumberFont");
    $numfon->setAttribute("Height", $height);
    $numfon->setAttribute("CharX", implode(" ", $x));
    $numfon->setAttribute("CharY", implode(" ", $y));
    $numfon->setAttribute("CharWidth", implode(" ", $width));
    $elem->append($numfon);
}

function rotateGlyphArrayCW(array $in, int $tileSize): array {
    $arr = [];
    foreach ($in as $k => $v) {
        $x = [];
        $x['width'] = $v['width'];

        // rotate point
        $x['x'] = (0 - $v['y'] - $tileSize) + $tileSize;
        $x['y'] = ($v['x'] - $tileSize) + $tileSize;

        $arr[$k] = $x;
    }
    return $arr;
}