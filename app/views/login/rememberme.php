<!-- Rememberme -->

<?php
global $config;
?>

<div class="container">
	<div class="row">
		<div class=" col-lg-8 offset-lg-2 mt-3 text-end">
			<div style="text-align:right; margin-bottom:1em;">
				Tiene cuenta? <a href="login">Ingresar</a>
			</div>
			<div class="input-group input-group mb-3"><span class="input-group-text"><i class="fa fa-envelope"></i></span><input class="form-control" type="text" id="email" placeholder="E-mail" required="required"></input></div>
			<div class="form-group mb-3">
				<button type="submit" class="btn btn-primary btn-lg btn-block login-btn w-100" onClick="rememberme()">Recuérdame</button>
			</div>

			<div class="mt-3" style="text-align:left;">
				No tiene cuenta? <a href="<?= $config['url_pages']['register'] ?>">Regístrese</a>
			</div>			

			<div id="error_box"></div>
		</div>
	</div>
</div>

<script>
	function rememberme() {
		var obj = {};

		obj['email'] = jQuery('#email').val();

		const url = base_url + '/wp-json/auth/v1/rememberme';

		const data = Object.keys(obj)
			.map((key) => `${key}=${encodeURIComponent( obj[key])}`)
			.join('&');

		axios
			.post(url, data, {
				headers: {
					Accept: "application/json",
					"Content-Type": "application/x-www-form-urlencoded;charset=UTF-8",
				},
			})
			.then(({
				data
			}) => {
				console.log(data);
				addNotice(data.message, 'success', 'error_box', true);
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