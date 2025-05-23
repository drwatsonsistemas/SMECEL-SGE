<!DOCTYPE html>
<html>
<head>
	<title>ChatGPT com PHP</title>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
	<script>
		function sendMessage() {
			var userInput = $('#userInput').val();
			$.ajax({
				type: "GET",
				url: "chatbot.php",
				data: { userInput: userInput },
				success: function(response) {
					$('#chatOutput').append('<p><strong>Você:</strong> ' + userInput + '</p><p><strong>ChatGPT:</strong> ' + response + '</p>');
				}
			});
			$('#userInput').val('');
			return false;
		}
	</script>
</head>
<body>
	<div id="chatOutput"></div>
	<form onsubmit="return sendMessage();">
		<input type="text" id="userInput" name="userInput" placeholder="Digite sua mensagem aqui...">
		<input type="submit" value="Enviar">
	</form>
</body>
</html>
