'use strict';

import { $App } from '@/App';
import { DocumentoModel } from '../Models/DocumentoModel';
import { DocumentsCollectionView } from './DocumentsCollectionView';

class DocumentoCollection extends Backbone.Collection {
    get model() {
        return DocumentoModel;
    }
}

class LoadDocumentsView extends Backbone.View {
    constructor(options) {
        super(options);
        this.documentosCollection = new DocumentoCollection();
        this.documentosView = null;
    }

    get className() {
        return 'card-body';
    }

    initialize() {
        this.template = $('#tmp_documentos').html();
    }

    get events() {
        return {
            'click [toggle-event="borrar"]': 'borrarArchivo',
            'click [toggle-event="download"]': 'descargaArchivo',
            'click [toggle-event="prodoc"]': 'processDocument',
            'click [toggle-event="salvar"]': 'guardarArchivo',
            'click [toggle-event="show"]': 'verArchivo',
            'change input[toggle-event="change"]': 'showNameFile',
        };
    }

    render() {
        const { archivos, path, puede_borrar, disponibles } = this.collection[0];

        const template = _.template(this.template);
        this.$el.html(
            template({
                archivos,
                path,
                puede_borrar,
                disponibles,
                model: this.model.toJSON(),
            }),
        );

        _.each(archivos, (archivo) => {
            archivo.puede_borrar = puede_borrar;
            this.documentosCollection.add(new DocumentoModel(archivo), { merge: true });
        });

        this.documentosView = new DocumentsCollectionView({
            collection: this.documentosCollection,
        });
        this.$el.find('#addArchivoRequeridos').append(this.documentosView.render().el);
        return this;
    }

    borrarArchivo(event) {
        event.preventDefault();
        let target = $(event.currentTarget);
        const coddoc = target.attr('data-coddoc');
        const id = target.attr('data-id');

        $App.trigger('confirma', {
            message: 'Esta seguro de borrar el Archivo?',
            callback: (status) => {
                if (status) {
                    this.trigger('file:trash', {
                        data: {
                            id: id,
                            coddoc: coddoc,
                        },
                        callback: (response) => {
                            if (response) {
                                this.trigger('file:reload');
                            }
                        },
                    });
                }
            },
        });
    }

    descargaArchivo(event) {
        event.preventDefault();
        const target = this.$el.find(event.currentTarget);
        $App.trigger('down', {
            url: target.attr('data-url'),
            filename: target.attr('data-file'),
        });
    }

    processDocument(event) {
        event.preventDefault();
        let target = this.$el.find(event.currentTarget);
        const id = parseInt(target.attr('data-cid'));
        const url = $App.kumbiaURL(target.attr('data-url') + '/' + id);

        this.trigger('file:firma', (response) => {
            if (response) {
                this.trigger('file:prodoc', {
                    data: {
                        id: id,
                    },
                    url: url,
                    callback: (response) => {
                        if (response) {
                            $App.trigger('down', {
                                url: response.url,
                                filename: response.name,
                            });
                            this.trigger('file:reload');
                        }
                    },
                });
            }
        });
    }

    guardarArchivo(e) {
        e.preventDefault();
        let _target = this.$el.find(e.currentTarget);
        const coddoc = _target.attr('data-coddoc');
        const id = parseInt(_target.attr('data-id'));
        const target = document.querySelector('#archivo_' + coddoc);

        this.trigger('file:save', {
            target,
            id,
            coddoc,
            callback: (response) => {
                $App.trigger('alert:success', { message: response['msj'] });
                this.trigger('file:reload');
            },
        });
    }

    showNameFile(event) {
        let target = $(event.currentTarget);
        let coddoc = target.attr('data-coddoc');
        let files = document.getElementById(target.attr('id')).files;
        if (files.length == 0) return;
        let archivo = files[0];
        this.$el.find('.toogle-show-name[data-code="' + coddoc + '"]').html(archivo.name);
    }

    verArchivo(event) {
        event.preventDefault();
        const target = $(event.currentTarget);
        const filename = target.attr('data-href');

        $.ajax({
            url: $App.url('documentos/ver-pdf'),
            method: 'POST',
            data: { filename: filename },
            xhrFields: {
                responseType: 'blob',
            },
            beforeSend: (xhr) => {
                const csrf = document.querySelector("[name='csrf-token']").getAttribute('content');
                xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                xhr.setRequestHeader('X-CSRF-TOKEN', csrf);
                xhr.setRequestHeader('Authorization', 'Bearer ' + csrf);
            },
            success: (data) => {
                const url = URL.createObjectURL(data);
                window.open(url, filename, 'width=900,height=750,toolbar=no,location=no,status=no,menubar=no,scrollbars=yes');
            },
            error: () => {
                $App.trigger('alert:error', { message: 'No se pudo cargar el documento' });
            },
        });
    }

    remove() {
        if (this.documentosView) this.documentosView.remove();
        this.stopListening();
        Backbone.View.prototype.remove.call(this);
    }
}

export { LoadDocumentsView };
