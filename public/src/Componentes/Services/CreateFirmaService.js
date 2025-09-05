'use strict';

import { $App } from '@/App';

class CreateFirmaService {
    constructor() {
        // Variables para el dibujo
        this.stage = null;
        this.layer = null;
        this.isDrawing = false;
        this.lines = [];
        this.lastLine = null;
    }

    init() {
        this.stage = new Konva.Stage({
            container: 'konvaContainer',
            width: 300,
            height: 180,
        });

        // Capa para dibujar
        this.layer = new Konva.Layer();
        this.stage.add(this.layer);

        // Evento de dibujo al inicio
        this.stage.on('mousedown touchstart', (e) => {
            this.isDrawing = true;
            const pos = this.stage.getPointerPosition();
            this.lastLine = new Konva.Line({
                stroke: 'black',
                strokeWidth: 2,
                points: [pos.x, pos.y],
            });

            this.layer.add(this.lastLine);
            this.lines.push(this.lastLine);
        });

        // Evento de dibujo al finalizar
        this.stage.on('mouseup touchend', (e) => {
            this.isDrawing = false;
        });

        // Evento de dibujo continuo
        this.stage.on('mousemove touchmove', (e) => {
            if (!this.isDrawing) {
                return;
            }
            const pos = this.stage.getPointerPosition();
            let newPoints = this.lastLine.points().concat([pos.x, pos.y]);
            this.lastLine.points(newPoints);
            this.layer.batchDraw();
        });
    }

    undoEvent() {
        if (_.isNull(this.stage)) return false;
        // Botón para deshacer
        if (this.lines.length > 0) {
            const lastDrawnLine = this.lines.pop();
            lastDrawnLine.destroy();
            this.layer.batchDraw();
        }
    }

    clearEvent() {
        if (_.isNull(this.stage)) return false;
        this.layer.destroyChildren();
        this.layer.draw();
        this.lines = [];
    }

    // Botón para procesar la imagen en formato PNG sin fondo
    processEvent() {
        // Convertir el lienzo en una imagen PNG
        if (_.isNull(this.stage)) return false;
        this.stage.toDataURL({
            callback: (dataUrl) => {
                // Abrir la imagen en una nueva ventana para guardarla
                const windowContent = '<img src="' + dataUrl + '">';
                const printWindow = window.open('', '_blank');
                printWindow.document.open();
                printWindow.document.write(windowContent);
            },
        });
    }

    saveEvent(transfer) {
        const { callback } = transfer;
        this.stage.toDataURL({
            callback: (dataUrl) => {
                const data = new FormData();
                data.append('imagen', dataUrl);
                $App.trigger('upload', {
                    url: $App.kumbiaURL('firmas/guardar'),
                    silen: false,
                    data: data,
                    callback: (response) => {
                        if (response) {
                            return callback(response);
                        }
                        return callback(false);
                    },
                });
            },
        });
    }
}

export { CreateFirmaService };
