<script>
    document.addEventListener("DOMContentLoaded", function(event) {
        let mail = window.atob(window.location.pathname.split('/')[3]);

        console.log(mail);
        document.querySelector('#mail').innerText = mail;
    });
</script>

<style>
    #mail {
        color: blue;
    }
</style>

<div class="container">
    <div class="row">
        <div class= col-lg-8 offset-lg-2 mt-3 text-end"  style="text-align:left;">
            <div class="row vcenter">
                <div class="col-xs-12 col-sm-12 col-md-6 col-md-push-3">
                    <h1>Correo enviado</h1>

                    <center>
                        <img src="<?= __DIR__  . '../assets/images/mail.png' ?>" style="height: 100px;" />
                    </center>

                    <p />
                    <p />
                    Si la dirección de correo <span id='mail'>correo</span> es válida,
                    un correo con el enlace de recuperación de contraseña fue enviado a dicha dirección<br />
                    <p />

                    Revíselo cuanto antes, tiene vencimiento.
                </div>
            </div>
        </div>
    </div>
</div>