const gulp = require('gulp');
const concat = require('gulp-concat');
const sass = require('gulp-sass');
const sassGlob = require('gulp-sass-glob');
const plumber = require('gulp-plumber');
const uglify = require('gulp-uglify');

const filesAndDirectories = {
    bootstrapLightbox: {
        libraries: [
            'node_modules/photoswipe/dist/photoswipe.js',
            'node_modules/photoswipe/dist/photoswipe-ui-default.js',
        ],
        source: [
            'JavaScript/PhotoSwipe.js'
        ],
        destination: {
            path: '../Public/JavaScript/',
            fileName: 'BootstrapLightbox.min.js'
        },
    },

    justifiedGallery: {
        libraries: [
            'node_modules/justifiedGallery/dist/js/jquery.justifiedGallery.js',
            'node_modules/photoswipe/dist/photoswipe.js',
            'node_modules/photoswipe/dist/photoswipe-ui-default.js',
        ],
        source: [
            'JavaScript/JustifiedGallery/JustifiedGallery.js',
            'JavaScript/PhotoSwipe.js'
        ],
        destination: {
            path: '../Public/JavaScript/',
            fileName: 'JustifiedGallery.min.js'
        },

    },

    styles: {
        source: [
            'Styles/**/*.scss'
        ],
        destination: '../Public/Styles',
    },

};

const javascript = (subTaskName) => {
    return gulp
        .src([].concat(filesAndDirectories[subTaskName].libraries, filesAndDirectories[subTaskName].source))
        .pipe(plumber())
        .pipe(uglify())
        .pipe(concat(filesAndDirectories[subTaskName].destination.fileName))
        .pipe(gulp.dest(filesAndDirectories[subTaskName].destination.path))
};

const bootstrapLightbox = () => {
    return javascript('bootstrapLightbox')
};

const justifiedGallery = () => {
    return javascript('justifiedGallery')
};

const styles = () => {
    return gulp
        .src(filesAndDirectories.styles.source)
        .pipe(plumber())
        .pipe(sassGlob())
        .pipe(sass())
        .pipe(gulp.dest(filesAndDirectories.styles.destination));
};

const registerWatchers = () => {
    gulp.watch(filesAndDirectories.bootstrapLightbox.source, bootstrapLightbox);
    gulp.watch(filesAndDirectories.justifiedGallery.source, justifiedGallery);

    gulp.watch(filesAndDirectories.styles.source, sass);
};

const allJavascript = gulp.parallel(bootstrapLightbox, justifiedGallery);

const buildAll = gulp.parallel(allJavascript, styles);

exports.bootstrapLightbox = bootstrapLightbox;
exports.justifiedGallery = justifiedGallery;
exports.allJavascript = allJavascript;
exports.styles = styles;
exports.watch = gulp.series(buildAll, registerWatchers);
exports.default = buildAll;

