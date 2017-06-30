<?php
require_once dirname(__FILE__) . '/database.php';
define('IS_MB_ENABLED', true);
class AIBot {
	private static $instance = null;
	private static $db = null;
	private $session = '';
	private $botname = 'aiml';
	private $username = 'guest';
	private $default = 'I do not understand that command, but I will learn from it';
	private $input = '';
	private function __construct() {
		self::$db = Database::getInstance();
	}
	public static function init() {
		if (self::$instance == null) {
			self::$instance = new AIBot();
		}
		return self::$instance;
	}
	public function run() {
		if (isset($_POST['session']) && !empty($_POST['session'])) {
			$this->session = trim($_POST['session']);
			if (isset($_POST['message']) && !empty($_POST['message'])) {
				$message = $this->cleanMessage($_POST['message']);
				$this->input = $this->cleanMessage($_POST['message'], false);
				$this->storeConvetion($message);
				$answer = $this->getAnswer($message);
				$this->storeConvetion($answer);
				$response = array(
					'session' => $this->session,
					'botname' => $this->botname,
					'username' => $this->username,
					'message' => $answer,
					'time' => date('Y-m-d H:i:s')
				);
				$this->response($response);
			}
		}
	}
	public function getSession() {
		return $this->session;
	}
	private function _strtolower($text) {
		return (IS_MB_ENABLED) ? mb_strtolower($text) : strtolower($text);
	}
	private function _strtoupper($text) {
		return (IS_MB_ENABLED) ? mb_strtoupper($text) : strtoupper($text);
	}
	private function match_pattern($pattern, $input) {
		if (empty($input)) {
			return false;
		}
		$pattern_regex = $this->pattern_regex($this->_strtolower($pattern));
		return preg_match($pattern_regex, _strtolower($input)) === 1;
	}
	private function pattern_regex($item) {
		$item = trim($item);
		$item = str_replace("*", ")(.*)(", $item);
		$item = str_replace("_", ")(.*)(", $item);
		$item = str_replace("+", "\+", $item);
		$item = "(" . str_replace(" ", "\s", $item) . ")";
		$item = str_replace("()", '', $item);
		$matchme = "/^" . $item . "$/ui";
		return $matchme;
	}
	private function aiml_like_query($message) {
		$words = explode(' ', $message);
		if (count($words) == 1) {
			return "select * from aiml where pattern like '%$message%'";
		}
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
	private function getResultPatterns($message) {
		$result = self::$db->once_fetch_array("select * from aiml where pattern like '$message'");
		if ($result) {
			return $result;
		}
		$sql = $this->aiml_like_query($message);
		$query = self::$db->query($sql);
		while ($row = self::$db->fetch_array($query)) {
			if ($this->match_pattern($row['pattern'], $message)) {
				return $row;
			}
		}
		return null;
	}
	// Private
	private function getAnswer($message) {
		$result = $this->getResultPatterns($message);
		
		if ($result) {
			return $this->processMessage($result);
		}
		return $this->default;
	}
	private function response($resonse) {
		header('Content-Type:text/plain;charset=utf-8');
		if (is_string($resonse)) {
			echo $resonse;
		} else {
			echo json_encode($resonse, JSON_UNESCAPED_UNICODE);
		}
		exit;
	}
	private function storeConvetion($message) {
	self::$db->query("insert into chats (session_id, botname, guestname, message, sendtime) values ('{$this->session}','{$this->botname}','{$this->username}','$message',now())");
	}
	private function processMessage($row) {
		$result = self::$db->once_fetch_array("select template from ramdoms where aiml = {$row['id']} order by rand() limit 1");
		if ($result) {
			return $result['template'];
		}
		return $row['template'];
	}
	private function addUnknown($message) {
		// $message = mb_strtoupper($message);
		$message = mb_convert_case ($message, MB_CASE_UPPER, "UTF-8");
		self::$db->query("insert into aiml (category, pattern, template) values (1, '$message', '{$this->default}')");
	}
	private function cleanMessage($message, $clean = true) {
		$message = strip_tags($message);
		$message = trim($message);
		$message = preg_replace('/<.*?>/',' ',$message);
		$message = preg_replace('/ {2,}/',' ',$message);
		$message = trim($message);
		return $clean ? $this->cleanInput($message) : $message;
	}
	private function cleanInput($tmp) {
		//remove puncutation except full stops
		$tmp = preg_replace('/\.+/', '.', $tmp);
		$tmp = preg_replace('/\,+/', '', $tmp);
		$tmp = preg_replace('/\!+/', '', $tmp);
		$tmp = preg_replace('/\?+/', '', $tmp);
		$tmp = str_replace("'", " ", $tmp);
		$tmp = str_replace("\"", " ", $tmp);
		$tmp = preg_replace('/\s\s+/', ' ', $tmp);
		//replace more than 2 in a row occurances of the same char with two occurances of that char
		$tmp = preg_replace('/aa+/', 'oaa', $tmp);
		$tmp = preg_replace('/bb+/', 'bb', $tmp);
		$tmp = preg_replace('/cc+/', 'cc', $tmp);
		$tmp = preg_replace('/dd+/', 'dd', $tmp);
		$tmp = preg_replace('/ee+/', 'ee', $tmp);
		$tmp = preg_replace('/ff+/', 'ff', $tmp);
		$tmp = preg_replace('/gg+/', 'gg', $tmp);
		$tmp = preg_replace('/hh+/', 'hh', $tmp);
		$tmp = preg_replace('/ii+/', 'ii', $tmp);
		$tmp = preg_replace('/jj+/', 'jj', $tmp);
		$tmp = preg_replace('/kk+/', 'kk', $tmp);
		$tmp = preg_replace('/ll+/', 'll', $tmp);
		$tmp = preg_replace('/mm+/', 'mm', $tmp);
		$tmp = preg_replace('/nn+/', 'nn', $tmp);
		$tmp = preg_replace('/oo+/', 'oo', $tmp);
		$tmp = preg_replace('/pp+/', 'pp', $tmp);
		$tmp = preg_replace('/qq+/', 'qq', $tmp);
		$tmp = preg_replace('/rr+/', 'rr', $tmp);
		$tmp = preg_replace('/ss+/', 'ss', $tmp);
		$tmp = preg_replace('/tt+/', 'tt', $tmp);
		$tmp = preg_replace('/uu+/', 'uu', $tmp);
		$tmp = preg_replace('/vv+/', 'vv', $tmp);
		$tmp = preg_replace('/ww+/', 'ww', $tmp);
		$tmp = preg_replace('/xx+/', 'xx', $tmp);
		$tmp = preg_replace('/yy+/', 'yy', $tmp);
		$tmp = preg_replace('/zz+/', 'zz', $tmp);
		return trim($tmp);
	}
}