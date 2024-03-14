<!-- 	
	Rememberme => change password 

	Acá envio pass y token recibido en url
-->

<?php
global $config;
?>

<script>
	let token = null;

	document.addEventListener("DOMContentLoaded", function(event) {
		const params = new URLSearchParams(window.location.search);

		if (!params.has('token')) {
			document.getElementById('sendBtn').disabled = true;
			addNotice('La url está incorrecta', 'danger', 'error_box', true);
		} else {
			token = params.get('token');
		}
	});
</script>

<div class="container">
	<div class="row">
		<div class="col-lg-8 offset-lg-2 mt-3 text-end">
			<div class="input-group mb-3">
				<span class="input-group-text"><i class="fas fa-key"></i></span><input class="form-control" type="password" id="password" placeholder="Password" required="required"></input><span class="input-group-text" onclick="password_show_hide_pc();">
					<i class="fas fa-eye" id="show_eye"></i>
					<i class="fas fa-eye-slash d-none" id="hide_eye"></i>
				</span>
			</div>

			<div class="input-group mb-3"><span class="input-group-text"><i class="fas fa-key"></i></span><input class="form-control" type="password" id="passwordconfirmation" placeholder="Password confirmación" required="required" name="passwordconfirmation"></input></div>

			<div class="form-group">
				<button type="submit" class="btn btn-primary btn-lg btn-block login-btn w-100" id="sendBtn" onClick="change_pass()">Enviar</button>
			</div>

			<div id="error_box"></div>
		</div>
	</div>
</div>

<script>
	function password_show_hide_pc() {
		password_show_hide();
		password_show_hide('passwordconfirmation')
	}

	function change_pass() {
		let obj = {};

		if (jQuery('#password').val() != jQuery('#passwordconfirmation').val()) {
			addNotice('Contraseñas no coinciden', 'warning', 'error_box', true);
			return;
		} else {
			hideNotice('error_box');
		}

		obj['password'] = jQuery('#password').val();

		const url = base_url + '/wp-json/auth/v1/change_pass_by_link/' + token;

		const data = Object.keys(obj)
			.map((key) => `${key}=${encodeURIComponent( obj[key])}`)
			.join('&');

		axios
			.post(url, data, {
				headers: {
					Accept: "application/json",
					"Content-Type": "application/x-www-form-urlencoded;charset=UTF-8",
					Authorization: 'Bearer ' + token
				},
			})
			.then(({
				data
			}) => {
				console.log(data);
				// ....

				if (typeof password_changed_redirection != 'undefined' && password_changed_redirection !== null && password_changed_redirection != '') {
					window.location = password_changed_redirection;
				} else {
					addNotice('Cambio exitoso.', 'success', 'error_box', true);
				}

			})
			.catch(function(error) {
				if (error.response) {
					// The request was made and the server responded with a status code
					// that falls out of the range of 2xx
					//console.log(error.response.data);  ///  <--- mensaje de error
					// console.log(error.response.status);
					// console.log(error.response.headers);

					addNotice(error.response.data.message, 'warning', 'error_box', true);
				} else if (error.request) {
					// The request was made but no response was received
					// `error.request` is an instance of XMLHttpRequest in the browser and an instance of
					// http.ClientRequest in node.js

					console.log(error.request);
					addNotice('El servidor no responde. Intente maś tarde.', 'danger', 'error_box', true);
				} else {
					// Something happened in setting up the request that triggered an Error

					console.log('Error', error.message);
					addNotice('Algo salió mal.', 'danger', 'error_box', true);
				}

				//console.log(error.config);
			});

		return false;
	}
</script>