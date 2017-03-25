<?php
define('SITE_ROOT', dirname(__FILE__));
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASSWD', '');
define('DB_NAME', 'aibot');

require_once dirname(__FILE__) . '/aibot.php';

$AIBot = AIBot::init();

$AIBot->run();

$session = $AIBot->getSession();
if (empty($session)) {
	$session = uniqid();
}
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,minimum-scale=1.0" />
	<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
</head>
<body>
<div id="chat"></div>
<form action="" method="post">
	<input type="text" name="message" value="" autocomplete="off" autocapitalize="off" autocorrect="off" />
	<input type="hidden" name="session" value="<?php echo $session; ?>" />
	<input type="submit" value="Send" />
</form>
<script>
jQuery(document).ready(function($) {
	$('form').on('submit', function() {
		var message = $('input[name="message"]').val();
		var session = $('input[name="session"]').val();
		if (session.length > 0 && message.length > 0) {
			$.ajax({
				url : '<?php echo basename(__FILE__);?>',
				type:'post',
				data : { session : session, message : message },
				beforeSend : function() {
					$('#chat').append('<p><strong>You:</strong>'+message+'</p>');
				},
				success : function(res) {
					$('#chat').append('<p><strong>'+res.botname+':</strong>'+res.message+' <small>'+res.time+'</small></p>');
					$('input[name="message"]').val('');
				},
				dataType:'json',
				async : true
			});
		}
		return false;
	});
});
</script>
</body>
</html>