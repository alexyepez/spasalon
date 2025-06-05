import path from 'path'
import fs from 'fs'
import { glob } from 'glob'
import { src, dest, watch, series } from 'gulp'
import * as dartSass from 'sass'
import gulpSass from 'gulp-sass'
import terser from 'gulp-terser'
import sharp from 'sharp'

const sass = gulpSass(dartSass)

const paths = {
    scss: 'src/scss/**/*.scss',
    js: 'src/js/**/*.js',
    imagenes: 'src/img/**/*'
}

export function css( done ) {
    src(paths.scss, {sourcemaps: true})
        .pipe( sass({
            outputStyle: 'compressed'
        }).on('error', sass.logError) )
        .pipe( dest('./public/build/css', {sourcemaps: '.'}) );
    done()
}

export function js( done ) {
    src(paths.js)
        .pipe(terser())
        .pipe(dest('./public/build/js'))
    done()
}

export async function imagenes(done) {
    const srcDir = './src/img';
    const buildDir = './public/build/img';
    const images =  await glob('./src/img/**/*'); // Asegúrate que capture .png, .jpg, etc.

    images.forEach(file => {
        const relativePath = path.relative(srcDir, path.dirname(file));
        const outputSubDir = path.join(buildDir, relativePath);
        procesarImagenes(file, outputSubDir);
    });
    done();
}

function procesarImagenes(file, outputSubDir) {
    if (!fs.existsSync(outputSubDir)) {
        fs.mkdirSync(outputSubDir, { recursive: true })
    }
    const baseName = path.basename(file, path.extname(file))
    const extName = path.extname(file).toLowerCase(); // Convertir a minúsculas para la comparación

    const outputFile = path.join(outputSubDir, `${baseName}${extName}`); // Ruta de salida con extensión original
    const outputFileWebp = path.join(outputSubDir, `${baseName}.webp`);
    const outputFileAvif = path.join(outputSubDir, `${baseName}.avif`); // Considera si quieres AVIF para PNG también

    const options = { quality: 80 }; // Opciones generales de calidad

    if (extName === '.svg') {
        // Si es SVG, solo copia el archivo
        fs.copyFileSync(file, outputFile);
    } else if (extName === '.png') {
        // Si es PNG, procesa con sharp.png() para mantener la transparencia
        // y también crea versiones WebP/AVIF si lo deseas
        sharp(file)
            .png() // Usa .png() para optimizar PNG. Puedes pasarle opciones específicas para PNG si es necesario.
            // Por ejemplo: .png({ quality: 80, compressionLevel: 6 })
            .toFile(outputFile);

        // Opcional: Crear también WebP y AVIF desde el PNG original
        sharp(file).webp(options).toFile(outputFileWebp);
        sharp(file).avif().toFile(outputFileAvif); // Sharp por defecto maneja bien la transparencia para AVIF desde PNG

    } else if (extName === '.jpg' || extName === '.jpeg') {
        // Para JPG/JPEG, procesa con sharp.jpeg()
        sharp(file).jpeg(options).toFile(outputFile);
        sharp(file).webp(options).toFile(outputFileWebp);
        sharp(file).avif().toFile(outputFileAvif);
    } else {
        // Para otros formatos no manejados explícitamente, podrías copiarlos o ignorarlos
        console.log(`Formato de imagen no procesado explícitamente: ${extName}. Copiando archivo.`);
        fs.copyFileSync(file, outputFile);
    }
}

export function dev() {
    watch( paths.scss, css );
    watch( paths.js, js );
    watch(paths.imagenes, imagenes); // Modificado para usar la variable paths.imagenes
}

export default series( js, css, imagenes, dev )