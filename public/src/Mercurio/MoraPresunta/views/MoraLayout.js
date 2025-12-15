import { Layout } from '@/Common/Layout';

export default class MoraLayout extends Layout {
    constructor(options = {}) {
        super({
            ...options,
            template: '#tmp_layout',
            className: 'tab-content',
            tagRegions: {
                periodos: '#periodo-list',
                table: '#data-table',
            },
        });
    }
}
