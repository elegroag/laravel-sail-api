import { CollectionView } from '../Collections/CollectionView';
import { ModelView } from '@/Common/ModelView';

class DocumentsCollectionView extends CollectionView {
    constructor(options = {}) {
        super(options);
        this.modelView = DocumentsRow;
        this.$el.attr('id', 'addArchivoRequeridos');
    }

    get tagName() {
        return 'ul';
    }

    get className() {
        return 'doc-adjuntos-list list-unstyled mb-0';
    }

    /**
     * @override
     */
    remove() {
        this.stopListening();
        Backbone.View.prototype.remove.call(this);
    }
}

class DocumentsRow extends ModelView {
    constructor(options = {}) {
        super(options);
        this.template = _.template(document.getElementById('tmp_docurow').innerHTML);
    }

    initialize() {
        this.listenTo(this.model, 'change', this.render);
    }

    get tagName() {
        return 'li';
    }

    get className() {
        return 'doc-adjunto-item';
    }
}

export { DocumentsCollectionView };
