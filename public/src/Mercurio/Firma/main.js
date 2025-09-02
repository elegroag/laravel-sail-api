import { $App } from '@/App';

(function ($) {
	var fileUploadCount = 0;
	var prepareFiles = [];

	$.fn.fileUpload = function () {
		return this.each(function () {
			const fileUploadDiv = $(this);
			const fileUploadId = `fileUpload-${++fileUploadCount}`;

			// Creates HTML content for the file upload area.
			const fileDivContent = `
                <label for="${fileUploadId}" class="file-upload">
                    <div>
                        <b class="material-icons-outlined">Validar Documento</b>
                        <p>Arrastra y suelta archivo aquí</p>
                        <span>O</span>
                        <div>Click buscar archivos</div>
                    </div>
                    <input type="file" id="${fileUploadId}" name=[] multiple hidden />
                </label>
            `;

			fileUploadDiv.html(fileDivContent).addClass('file-container');

			let table = null;
			let tableBody = null;
			// Creates a table containing file information.
			function createTable() {
				table = $(`
                    <table>
                        <thead>
                            <tr>
                                <th></th>
                                <th style="width: 30%;">Nombre archivo</th>
                                <th style="width: 20%;">Tamaño</th>
                                <th>Tipo</th>
                                <th>Opciones</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                `);

				tableBody = table.find('tbody');
				fileUploadDiv.append(table);
			}

			// Adds the information of uploaded files to table.
			function handleFiles(files) {
				if (!table) createTable();
				tableBody.empty();

				if (files.length > 0) {
					$.each(files, function (index, file) {
						if (typeof index == 'number') {
							prepareFiles[index] = file;
							let fileName = file.name;
							let fileSize = (file.size / 1024).toFixed(2) + ' KB';
							let fileType = file.type;
							tableBody.append(`
                            <tr>
                                <td>${index + 1}</td>
                                <td>${fileName}</td>
                                <td>${fileSize}</td>
                                <td>${fileType}</td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-primary validaBtn" data-cid='${index}'><i class="material-icons-outlined">Validar</i></button>
                                    <button type="button" class="btn btn-sm btn-danger deleteBtn"><i class="material-icons-outlined">Borrar</i></button>
                                </td>
                            </tr>
                        `);
						}
					});

					tableBody.find('.deleteBtn').click(function () {
						$(this).closest('tr').remove();
						if (tableBody.find('tr').length === 0) {
							tableBody.append(
								'<tr><td colspan="6" class="no-file">No files selected!</td></tr>',
							);
						}
					});

					tableBody.find('.validaBtn').click(function () {
						const cid = $(this).attr('data-cid');
						const formData = new FormData();
						formData.append('file', prepareFiles[cid]);

						$App.trigger('upload', {
							url: $App.url('validaFirma'),
							data: formData,
							callback: (response) => {
								if (response.success == true) {
									if (response.isValid == true) {
										$App.trigger('alert:success', { message: response.msj });
									} else {
										$App.trigger('alert:warning', { message: response.msj });
									}
								} else {
									$App.trigger('alert:error', { message: response.msj });
								}
							},
						});
					});
				}
			}

			// Events triggered after dragging files.
			fileUploadDiv.on({
				dragover: function (e) {
					e.preventDefault();
					fileUploadDiv.toggleClass('dragover', e.type === 'dragover');
				},
				drop: function (e) {
					e.preventDefault();
					fileUploadDiv.removeClass('dragover');
					handleFiles(e.originalEvent.dataTransfer.files);
				},
			});

			// Event triggered when file is selected.
			fileUploadDiv.find(`#${fileUploadId}`).change(function () {
				handleFiles(this.files);
			});
		});
	};
})(jQuery);

$(() => {
	$App.initialize();

	$('#fileUpload').fileUpload();
});
