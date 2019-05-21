<?php
function wordsof($s) {
    $a = [];foreach(explode(" ",$s)as $w) $a[$w]++;
    return $a;
}

function compare($s1,$s2) {

    $w1 = wordsof($s1);if(!$w1) return 0;
    $w2 = wordsof($s2);if(!$w2) return 0;

    $totalLength = strlen(join("",$w1).join("",$w2)) || 1;

    $chDiff = 0;
    foreach($w1 as $word=>$x) if(!$w2[$word]) $chDiff+=strlen($word);
    foreach($w2 as $word=>$x) if(!$w1[$word]) $chDiff+=strlen($word);

    return $chDiff/$totalLength;

}

function escapeElasticReservedChars($query) {
    return preg_replace(
        '/[\\+\\-\\=\\&\\|\\!\\(\\)\\{\\}\\[\\]\\^\\\"\\~\\*\\<\\>\\?\\:\\\\\\/]/',
        addslashes('\\$0'),
        $query
    );
}



?>
