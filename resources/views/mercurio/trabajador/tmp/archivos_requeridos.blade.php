<? foreach ($param->load_archivos as $ai => $archivo) { 
    if($archivo->diponible)
    { ?>
        <tr>
            <td colspan='2'>
                <a href="#" class='btn btn-sm btn-outline-primary' type='button' onclick="verArchivo('<?=$param->path?>','<?=$archivo->diponible?>')">
                    <span class='btn-inner--icon'><i class='fas fa-file-download'></i></span>
                    <span class='btn-inner--text'><?=$archivo->detalle?></span>
                </a>
                <?=($archivo->corrige)? '<span class="text-warning">Archivo por devolución</span>':''?>
            </td>
            <? if($param->puede_borrar){ ?>
			<td>
				<button class='btn btn-icon btn-danger btn-sm btn-outline-danger' type='button' onclick="borrarArchivo('<?=$archivo->id?>','<?=$archivo->coddoc?>')"> 
                    <span class='btn-inner--icon'><i class='fas fa-save'></i> Borrar</span> 
                </button>
			</td>
            <?}else{ ?>
                <td></td>
            <? }?>
        </tr>
    <?}else{?>
        <tr>
            <td style='width:30%; border-right:0px'><?=$archivo->detalle?> <?=$archivo->obliga?></td>
            <td style="border-left:0px; border-right:0px">
                <div class='custom-file'>
                    <input type='file' class='custom-file-input' onchange="showNameFile(this.files, <?=$archivo->coddoc?>)" id='archivo_<?=$archivo->coddoc?>' name='archivo_<?=$archivo->coddoc?>' accept='application/pdf, image/*'>
                    <label class='custom-file-label toogle-show-name' for='customFileLang' data-code='<?=$archivo->coddoc?>'>Selecciona y carga aquí...</label>
                </div>
            </td>
            <td style="border-left:0px">
                <button class='btn btn-icon btn-primary btn-sm btn-outline-primary' type='button' onclick="guardarArchivo('<?=$archivo->id?>','<?=$archivo->coddoc?>')"> 
                    <span class='btn-inner--icon'><i class='fas fa-save'></i> Guardar</span> 
                </button>
            </td>
        </tr>
    <?} ?>
<? } ?>

	