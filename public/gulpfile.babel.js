const gulp = require('gulp');
const { exec } = require('node:child_process');
const { promisify } = require('node:util');

const CamelCase = (str) => {
    return str.replace(/\b\w/g, (l) => l.toUpperCase());
};

const appModules = {
    mercurio: [
        'login',
        'principal',
        'mercurio31',
        'mercurio32',
        'mercurio30',
        'mercurio34',
        'mercurio41',
        'mercurio36',
        'mercurio472',
        'dashboard',
        'mercurio471',
        'mercurio38',
        'certificados',
        'servicioDomestico',
        'usuario',
        'notificaciones',
        'consultasEmpresa',
        'consultasTrabajador',
        'coberturaServicios',
        'firma',
        'consultaNucleo',
        'moraPresunta',
        'trabajadoresEmpresa',
        'aportesEmpresa',
        'nominasEmpresa',
        'generadorCertificado',
    ],
    cajas: [
        'glob',
        'login',
        'dashboard',
        'basicas',
        'datos_caja',
        'firmas',
        'tipo_acceso',
        'documentos',
        'permisos',
        'motivo_rechazo',
        'tipo_opciones',
        'oficinas',
        'galeria',
        'consulta',
        'empresas',
        'inicio',
        'trabajadores',
        'conyuges',
        'beneficiarios',
        'certificados',
        'independientes',
        'pensionados',
        'facultativos',
        'madres_comunitarias',
        'servicio_domesticos',
        'datos_empresa',
        'datos_trabajador',
        'usuario',
        'docu_req_empresas',
        'mercurio13',
        'notificaciones',
        'auditoria',
        'mercurio18',
        'mercurio50',
        'mercurio51',
        'mercurio52',
        'mercurio53',
        'mercurio55',
        'mercurio56',
        'mercurio57',
        'mercurio58',
        'mercurio59',
        'mercurio65',
        'mercurio67',
        'mercurio72',
        'mercurio73',
        'mercurio74',
        'reasigna',
        'indicadores',
    ],
};

const app = process.env.APP || 'mercurio';
console.log('APP==', app);

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
    throw new Error(`[gulp] APP=${app} no está configurada en gulpfile.babel.js`);
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

exports.default = gulp.parallel(...modules);

// npx gulp watch:login
// npx gulp login
// npx gulp
