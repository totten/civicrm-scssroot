# civicrm-scssroot

Aggregate all SCSS files from CiviCRM extensions under a common root folder
named `SCSSROOT`.  This enables cross-extension SCSS references (which are
robust against the variations in directory structure seen by different
deployments).

 * To reference another extension, use "SCSSROOT", as in `@import "SCSSROOT/org.example.other/foo.scss";`
 * To reference all files in an extensions' folder, use "ALL", as in `@import "SCSSROOT/org.example.other/scss/ALL";`

# Installation

```bash
npm install --save totten/civicrm-scssroot
```

# Usage (Basic)

```javascript
var civicrmScssRoot = require('civicrm-scssroot')();
civicrmScssRoot.clean().then(...);                  // Delete SCSSROOT. Return a promise.
civicrmScssRoot.cleanSync();                        // Delete SCSSROOT immediately.
civicrmScssRoot.update().then(...);                 // Update SCSSROOT. Return a promise.
civicrmScssRoot.updateSync();                       // Update SCSSROOT immediately.
civicrmScssRoot.getPath();                          // Find the parent of SCSSROOT.
civicrmScssRoot.getWatchList();                     // List of paths to monitor.
civicrmScssRoot.getWatchList(['org.example.foo']);  // List of paths to monitor.
```

# Usage (Gulp Examples)

```javascript
var civicrmScssRoot = require('civicrm-scssroot')();

// Generate SCSSROOT and use it with gulp-sass
gulp.task('sass', function() {
  civicrmScssRoot.updateSync();
  gulp.src('scss/myfile.scss')
    .pipe(sass({
      includePaths: civicrmScssRoot.getPath()
    })
    .pipe(gulp.dest('css/'));
});

// Watch all extensions for SCSS updates
gulp.task('watch', function() {
  gulp.watch(civicrmScssRoot.getWatchList(), ['sass']);
});

// Watch specific extensions for SCSS udpates
gulp.task('watch', function() {
  gulp.watch(civicrmScssRoot.getWatchList(['org.civicrm.bootstrap', 'org.civicrm.bootstrapcivicrm']), ['sass']);
});

// Clear the SCSSROOT
gulp.task('clean', function() {
  civicrmScssRoot.cleanSync();
});
```
