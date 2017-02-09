# civicrm-scssroot

Aggregate all SCSS from CiviCRM extensions under a common root folder.  This
enables cross-extension SCSS references (which are robust against the
variations in directory structure seen by different deployments).

 * To reference an other extension, use "SCSSROOT", as in `@import "SCSSROOT/org.example.other/foo.scss";`
 * To reference a collection files in an extensions' folder, use "ALL", as in `@import "SCSSROOT/org.example.other/scss/ALL";`

# Usage

```javascript

var civicrmScssRoot = require('civicrm-scssroot')();

// Include the files as part of gulp-sass task
gulp.task('sass-civicrm', function () {
  civicrmScssRoot.updateSync();
  gulp.src('scss/myfile.scss')
    .pipe(sass({
      includePaths: civicrmScssRoot.getPath(),
      precision: 10
    })
    .pipe(gulp.dest('css/'));
});

// Watch all extensions for SCSS updates
gulp.task('watch', function () {
  gulp.watch(civicrmScssRoot.getWatchList(), ['sass']);
});

// Watch specific extensions for SCSS udpates
gulp.task('watch', function () {
  gulp.watch(civicrmScssRoot.getWatchList(['org.civicrm.bootstrap', 'org.civicrm.bootstrapcivicrm']), ['sass']);
});
```
