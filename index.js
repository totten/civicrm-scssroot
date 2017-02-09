module.exports = function(options) {
  var cvSync = require('civicrm-cv')({mode: 'sync'});
  var cvPromise = require('civicrm-cv')({mode: 'promise'});
  var updateScript = __dirname + "/bin/civicrm-scssroot-update.php";
  var cleanScript = __dirname + "/bin/civicrm-scssroot-clean.php";
  var buildPath = null;

  /**
   * Assimilate any interesting metadata returned by external script.
   */
  function assimilate(result) {
    if (result.buildPath) buildPath = result.buildPath;
  }

  return {

    clean: function() { return cvPromise('scr ' + cleanScript).then(assimilate); },
    cleanSync: function() { assimilate(cvSync('scr ' + cleanScript)); },
    update: function() { return cvPromise('scr ' + updateScript).then(assimilate); },
    updateSync: function() { assimilate(cvSync('scr ' + updateScript)); },

    getPath: function() {
      if (buildPath === null) {
        buildPath = cvSync("ev 'return CRM_Core_Config::singleton()->templateCompileDir;'");
      }
      return buildPath;
    },

    /**
     * @param filter
     *   Null, or an array of extension keys, or a function
     *   Ex: ['org.civicrm.bootstrap', 'org.civicrm.bootstrapcivicrm']
     * @return array
     *   List of file globs.
     *   Ex: ['/var/www/vendor/civicrm/bootstrap/**' + '/*.scss']
     *   (Note: example split slightly to fit in JS comments)
     */
    getWatchList: function(filter) {
      var extDirs = cvSync('ext:list -L --columns=key,path');
      var paths = [];
      if (!filter) {
        filter = function(){return true;};
      }
      if (Array.isArray(filter)) {
        var extKeys = filter;
        filter = function(ext) {
          return extKeys.indexOf(ext.key) >= 0;
        };
      }

      for (var i = 0; i < extDirs.length; i++) {
        if (filter(extDirs[i])) {
          paths.push(extDirs[i].path + '/**/*.scss');
        }
      }
      return paths;
    }
  };
};
