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