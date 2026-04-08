import gulp from 'gulp';
import { exec } from 'node:child_process';
import { dirname } from 'node:path';
import { fileURLToPath } from 'node:url';
import { promisify } from 'node:util';

const __dirname = dirname(fileURLToPath(import.meta.url));

const CamelCase = (str) => {
    return str.replace(/\b\w/g, (l) => l.toUpperCase());
};

const appModules = {
    mercurio: [
        'Login',
        'Principal',
        'Mercurio31',
        'Mercurio32',
        'Mercurio30',
        'Mercurio34',
        'Mercurio41',
        'Mercurio36',
        'Mercurio472',
        'DashBoard',
        'Mercurio471',
        'Mercurio38',
        'Certificados',
        'ServicioDomestico',
        'Usuario',
        'Notificaciones',
        'ConsultasEmpresa',
        'ConsultasTrabajador',
        'CoberturaServicios',
        'Firma',
        'ConsultaNucleo',
        'MoraPresunta',
        'TrabajadoresEmpresa',
        'AportesEmpresa',
        'NominasEmpresa',
        'GeneradorCertificado',
    ],
    cajas: [
        'Glob',
        'Login',
        'DashBoard',
        'Basicas',
        'DatosCaja',
        'Firmas',
        'TipoAcceso',
        'Documentos',
        'Permisos',
        'MotivoRechazo',
        'TipoOpciones',
        'Oficinas',
        'Galeria',
        'Consulta',
        'Empresas',
        'Inicio',
        'Trabajadores',
        'Conyuges',
        'Beneficiarios',
        'Certificados',
        'Independientes',
        'Pensionados',
        'Facultativos',
        'MadresComunitarias',
        'ServicioDomesticos',
        'DatosEmpresa',
        'DatosTrabajador',
        'Usuario',
        'DocureqEmpresas',
        'Mercurio13',
        'Notificaciones',
        'Auditoria',
        'Mercurio18',
        'Mercurio50',
        'Mercurio51',
        'Mercurio52',
        'Mercurio53',
        'Mercurio55',
        'Mercurio56',
        'Mercurio57',
        'Mercurio58',
        'Mercurio59',
        'Mercurio65',
        'Mercurio67',
        'Mercurio72',
        'Mercurio73',
        'Mercurio74',
        'Reasigna',
        'Indicadores',
    ],
};

const app = process.env.APP || 'mercurio';

const modules = appModules[app] || [];

const execAsync = promisify(exec);

async function viteBuild(moduleName) {
    try {
        await execAsync('npx vite build', {
            cwd: __dirname,
            env: {
                ...process.env,
                APP: app,
                MODULE: moduleName,
            },
        });
    } catch (err) {
        console.error(`[gulp] vite build failed for APP=${app} MODULE=${moduleName}`);
        throw err;
    }
}

if (!modules.length) {
    throw new Error(`[gulp] APP=${app} no está configurada en gulpfile.js`);
}

for (const moduleName of modules) {
    gulp.task(moduleName, () => viteBuild(moduleName));

    gulp.task('watch:' + moduleName, async () => {
        gulp.watch(['./src/**/*.js']).on('change', gulp.series(moduleName));
    });

    gulp.task('tasks:' + moduleName, gulp.series(moduleName, 'watch:' + moduleName));
}

gulp.task('build:all', gulp.parallel(...modules));
gulp.task('watch:all', async () => {
    gulp.watch(['./src/**/*.js'], gulp.series(...modules));
});

export default gulp.parallel(...modules);

/* 
APP=cajas npx gulp Login
APP=cajas npx gulp watch:Login
APP=mercurio npx gulp Login

APP=cajas npx gulp
APP=mercurio npx gulp
*/
