<?php

use Intervention\Image\Image;

function addGlyphsToNode(DOMElement $elem, array $glyphs, int $height, bool $vert = false, bool $reverse = false, string $pointOffset = "none", Image $debug = null): void {
    ksort($glyphs);

    $width = [];
    $x = [];
    $y = [];
    foreach ($glyphs as $v) {
        $width[] = $v['width'];
        switch ($pointOffset) {
            case "awy":
                $x[] = $v['x'];
                $y[] = $v['y'] + $v['width'];
                break;
            case "swx,ahy":
                $x[] = $v['x'] - $v['width'];
                $y[] = $v['y'] + $height;
                break;
            case "shx":
                $x[] = $v['x'] - $height;
                $y[] = $v['y'];
                break;
            case "none":
            default:
                $x[] = $v['x'];
                $y[] = $v['y'];
                break;
        }
        if (!is_null($debug)) {
            $debug->circle(3, end($x), end($y), function ($draw) {
                $draw->background('#0000ff');
            });
        }
    }

    $numfon = $elem->ownerDocument->createElement("NumberFont");
    $numfon->setAttribute("ReverseDigits", $reverse ? "true" : "false");
    $numfon->setAttribute("Orientation", $vert ? "Vertical" : "Horizontal");
    $numfon->setAttribute("Height", $height);
    $numfon->setAttribute("CharX", implode(", ", $x));
    $numfon->setAttribute("CharY", implode(", ", $y));
    $numfon->setAttribute("CharWidth", implode(", ", $width));
    $elem->append($numfon);
}

function rotateGlyphArrayCW(array $in, int $tileSize): array {
    $arr = [];
    foreach ($in as $k => $v) {
        $x = [];
        $x['width'] = $v['width'];

        // rotate point
        $x['x'] = (0 - ($v['y'] - $tileSize)) + $tileSize;
        $x['y'] = ($v['x'] - $tileSize) + $tileSize;

        $arr[$k] = $x;
    }
    return $arr;
}