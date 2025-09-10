<?php
echo View::getContent();
echo Tag::hiddenField("permiso_menu");
echo Tag::stylesheetLink('Cajas/principal');
?>

<div class="card-body mt-0 pb-1 bt-1">
    <div class="mb-4">
        <h4 for='afiliaciones'>Movimientos</h4>
        <p>Solicitudes de afiliación <br/>
											<span class="text-muted" data-type="profile">
                                            Explorar solicitudes aquí <i class="fas fa-hand-pointer"></i>
                                        	</span>
										</p>
        <div class="d-flex align-content-around flex-wrap mt-4 mb-2" id="show_afiliaciones">
            <? foreach ($servicios as $ai => $register) {
                if ($ai == 'afiliacion') {
                    foreach ($register as $aj => $row) { ?>
                        <div class="p-4 box" style="cursor:pointer" data-toggle='action' data-href='<?= $row['url'] ?>'>
                            <div class="card card-stats" style="min-width: 260px; min-height: 250px">
                                <div class="card-header card-header-warning card-header-icon">
                                    <div class="card-icon">
                                        <span class="fas"><?= $row['icon'] ?></span>
                                    </div>
                                    <div class="card-category">
										<?= $row['name'] ?><br/>
									</div>
                                    <img src='<?= $row['imagen'] ?>' class="img img-principal p-2" />
                                </div>
                                <div class="card-footer border-0">
                                    <div class="stats">
										<? if(is_array($row['cantidad'])):?>
											<div class="text-muted text-right">
												<span class="h5 text-info"><?= $row['cantidad']['pendientes'] ?></span> # Pendientes<br/>
												<span class="h5 text-info"><?= $row['cantidad']['aprobados'] ?></span> # Aprobados<br/>
												<span class="h5 text-info"><?= $row['cantidad']['rechazados'] ?></span> # Rechazados<br/>
												<span class="h5 text-info"><?= $row['cantidad']['devueltos'] ?></span> # Devueltos<br/>
												<span class="h5 text-info"><?= $row['cantidad']['temporales'] ?></span> # Temporales<br/>
											</div>
										<? endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <? } ?>

                <? } ?>
            <? } ?>
        </div>
    </div>

    <div class="mt-3 mb-4">
        <div class="p-2">
            <h4 for='productos'>Productos y Servicios</h4>
            <p>Productos y servicios adicionales de la CAJA de Compensación del Caquetá</p>
        </div>
        <div class="d-flex justify-content-start mt-4 mb-2" id="show_productos">
            <? foreach ($servicios as $ai => $register) {
                if ($ai == 'productos') {
                    foreach ($register as $aj => $row) { ?>
                        <div class="p-2 box" style="cursor:pointer" data-toggle='action' data-href='<?= $row['url'] ?>'>
                            <div class="card card-stats" style="min-width: 250px; min-height: 150px">
                                <div class="card-header card-header-warning card-header-icon">
                                    <div class="card-icon">
                                        <span class="fas"><?= $row['icon'] ?></span>
                                    </div>
                                    <p class="card-category"><?= $row['name'] ?></p>
                                    <img src='<?= $row['imagen'] ?>' class="img img-principal" />
                                </div>
                            </div>
                        </div>
                    <? } ?>

                <? } ?>
            <? } ?>
        </div>
    </div>
</div>

<?= Tag::javascriptInclude('Cajas/inicio/build.inicio'); ?>