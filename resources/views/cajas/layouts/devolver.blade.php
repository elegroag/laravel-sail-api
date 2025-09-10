<div style="max-width: 720px; margin: 0 auto; font-family: Arial, Helvetica, sans-serif; background-color: #EEEEEE; padding: 0; border: 1px solid #e1e1e1;">
    <!-- Header with Image -->
    <div style="background-color: #FFFFFF; padding: 55px 0; text-align: center; background-image: url('<?= $rutaImg ?>'); background-repeat: no-repeat; background-size: cover; background-position: center;">
        <!-- Empty div for the background image -->
    </div>

    <!-- Main Content -->
    <div style="background-color: #FFFFFF; padding: 15px 20px 25px; border: 1px solid #e1e1e1; border-top: none;">
        <h3 style="color: #000000; margin: 0 0 15px 0; font-size: 18px; line-height: 1.4;">
            <b>Pendiente Afiliación.<br>Caja De Compensación Familiar del Caquetá, COMFACA</b>
        </h3>

        <p style="text-align: left; font-size: 14px; color: #000000; margin: 0 0 15px 0; line-height: 1.5;">
            <?= $titulo ?>
        </p>

        <div style="font-size: 14px; color: #000000; line-height: 1.5;">
            <p style="text-align: justify; margin: 0 0 15px 0;">
                <?= $msj ?>
            </p>
            <p style="text-align: left; margin: 0 0 10px 0;">
                Ruta de afiliación:<br>
                <a href="<?= $url_activa ?>" style="color: #10acda; text-decoration: none;" target="_blank">
                    Plataforma comfaca en línea aquí &#x1f4ea;
                </a>
            </p>
        </div>
    </div>

    <!-- Divider -->
    <div style="height: 1px; background-color: #e1e1e1;"></div>

    <!-- Contact Information -->
    <div style="background-color: #f5f5f5; padding: 21px; border: 1px solid #e1e1e1; border-top: 1px solid #eeeeee; font-size: 14px; color: #787878; line-height: 1.6; font-style: italic;">
        <?= $mercurio02['razsoc'] ?><br>
        Direccion: <?= $mercurio02['direccion'] ?><br>
        Telefono: <?= $mercurio02['telefono'] ?><br>
        <br>
        Website:
        <a href="http://<?= $mercurio02['pagweb'] ?>" style="color: #478eae; text-decoration: none;" target="_blank">
            <?= $mercurio02['pagweb'] ?>
        </a>
    </div>

    <!-- Footer -->
    <div style="background-color: #373737; border: 1px solid #e1e1e1; border-top: none; padding: 3px 20px; height: 50px; display: flex; align-items: center;">
        <div style="font-size: 11px; color: #8e8e8e;">
            Comfaca En Línea - COMFACA.COM
        </div>
    </div>
</div>