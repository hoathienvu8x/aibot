<?php
function tsql($message) {
	$words = explode(' ', $message);
	if (count($words) == 1) {
		return "select * from aiml where pattern like '%$message%'";
	} else {
		$totalWords = count($words);
		$count_words = $totalWords - 1;
		$first_word = $words[0];
		$last_word = $words[$count_words];
		$likes = array(
			"pattern like '$first_word % $last_word'",
			"pattern like '$first_word %'"
		);
		for($i = 0; $i < $totalWords; $i++) {
			$twoUp = $i + 2;
			$oneUp = $i + 1;
			$oneDown = $i - 1;
			
			if (isset($words[$twoUp])) {
				$middleWord = $words[$oneUp];
				$likes[] = "(pattern like '% $middleWord %')";
			}
			if ($oneDown >= 0) {
				$likePatternOneArr = $words;
				$likePatternOneArr[$i] = '%';
				$likePatternOne = implode(' ', $likePatternOneArr);
				$likes[] = "(pattern like '".trim(strstr($likePatternOne, '%', true))." %')";
			}
			if ($oneUp < $totalWords) {
				$likePatternOneArr = $words;
				$likePatternOneArr[$i] = '%';
				$likePatternOne = implode(' ', $likePatternOneArr);
				$likes[] = "(pattern like '" . trim(strstr($likePatternOne, '%', false)) . "')";
			}
		}
		return "select * from aiml where ".implode(' or ', $likes);
	}
}
echo tsql("hello");
echo '<hr />';
echo tsql("hello foo");
echo '<hr />';
echo tsql("vfm la gi ?");
echo '<hr />';
echo tsql("fob là gì ?");
exit;
function b($m) {
	return '[bot '.$m[1].']';
}
function s($m) {
	return '[star '.$m[1].']';
}
function se($m) {
	return '[set '.$m[1].']';
}
function hh($m) {
	return '<template>'.htmlspecialchars($m[1]).'</template>';
}
$str = file_get_contents('AI.aiml');
$str = str_replace('<br/>',"\n", $str);
$str = str_replace('<srai>',"[srai]", $str);
$str = str_replace('<think>',"[think]", $str);
$str = str_replace('</srai>',"[/srai]", $str);
$str = str_replace('</set>',"[/set]", $str);
$str = str_replace('</think>',"[/think]", $str);
$str = str_replace('<li>',"[li]", $str);
$str = str_replace('</li>',"[/li]", $str);
$str = preg_replace_callback('/<bot (.*?)\/>/',"b", $str);
$str = preg_replace_callback('/<star (.*?)\/>/',"s", $str);
$str = preg_replace_callback('/<set (.*?)>/',"se", $str);

$str = preg_replace_callback('/(?s)<template>(.*?)<\/template>/','hh', $str);


$xml = simplexml_load_string($str);

print_r($xml);