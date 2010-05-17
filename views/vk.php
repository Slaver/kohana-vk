<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<title>Vk_Auth</title>
</head>
<body>

<?php if ($user === FALSE) { ?>

<script src="http://vkontakte.ru/js/common.js"></script>
<div id="vk_api_transport"></div>
<script type="text/javascript">
window.vkAsyncInit = function() {
	VK.init({
		apiId: <?php echo $config['VK_API_ID']; ?>,
		nameTransportPath: '/xd_receiver.htm'
	});
	VK.UI.button('vk_login');
};

(function() {
	var el = document.createElement("script");
	el.type = "text/javascript";
	el.charset = "windows-1251";
	el.src = "http://vkontakte.ru/js/api/openapi.js";
	el.async = true;
	document.getElementById("vk_api_transport").appendChild(el);
}());

function doLogin() {
	VK.Auth.login(afterLog);
}

function doLogout() {
	VK.Auth.logout(afterLog);
}

function afterLog(response) {
	//baseURL
	window.location = '/vk';
}
</script>

	<div id="vk_login" onclick="doLogin()"></div>

<?php } else { ?>

	<div id="openapi_block">
		<img src="<?php echo $user['photo']; ?>" id="openapi_userphoto" />
		<div id="openapi_profile">
			<p>Привет, <a href="http://vk.com/id<?php echo $user['uid']; ?>"><?php echo $user['first_name']; ?> <?php echo $user['last_name']; ?></a>!</p>
			<p><a href="/vk/logout">Выход</a></p>
		</div>
	</div>

<?php } ?>

</body>
</html>