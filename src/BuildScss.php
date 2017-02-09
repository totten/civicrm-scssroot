<?php

class BuildScss {

  private $buildDir;

  private $extDirs;

  public function __construct() {
    $this->buildDir = CRM_Utils_File::addTrailingSlash(CRM_Core_Config::singleton()->templateCompileDir) . 'SCSSROOT';
    $this->extDirs = array();
    $container = CRM_Extension_System::singleton()->getFullContainer();
    foreach ($container->getKeys() as $key) {
      $this->extDirs[] = array(
        'key' => $key,
        'path' => $container->getPath($key),
      );
    }
  }

  public function getBuildDir() {
    return $this->buildDir;
  }

  /**
   * Prepare file-map between the source and target folders.
   *
   * @return array
   *   A list of files to synchronize.
   *   Ex: ["/var/www/source.scss" => "/var/www/target.scss"].
   */
  public function buildFileMap() {
    $allFiles = array();
    foreach ($this->extDirs as $ext) {
      $extPath = CRM_Utils_File::addTrailingSlash($ext['path']);
      foreach (CRM_Utils_File::findFiles($extPath, '*.scss') as $file) {
        if (preg_match(';[/\\\\]node_modules[/\\\\];', $file)) {
          continue;
        }

        $relPath = CRM_Utils_File::relativize($file, $extPath);
        $allFiles[$file] = $this->buildDir . DIRECTORY_SEPARATOR . $ext['key'] . DIRECTORY_SEPARATOR . $relPath;
      }
    }
    return $allFiles;
  }

  /**
   * Clean out the build dir
   */
  public function clean() {
    CRM_Utils_File::cleanDir($this->getBuildDir(), TRUE, FALSE);
  }

  /**
   * Update files
   */
  public function update() {
    $fileMap = $this->buildFileMap();

    foreach (CRM_Utils_File::findFiles($this->getBuildDir(), '*.scss') as $tgtFile) {
      if (!in_array($tgtFile, $fileMap)) {
        $this->debug("Remove orphan: $tgtFile");
        unlink($tgtFile);
      }
    }

    foreach ($fileMap as $from => $to) {
      if (!file_exists($to) || filemtime($from) > filemtime($to)) {
        $this->debug("Copy $from to $to");
        $parent = dirname($to);
        if (!is_dir($parent)) {
          mkdir($parent, 0777, TRUE);
        }
        copy($from, $to);
      }
    }

    foreach (_find_dirs($this->getBuildDir()) as $dir) {
      $allFile = "$dir/_ALL.scss";
      $this->debug("Generate $allFile");
      $files = CRM_Utils_File::findFiles($dir, '*.scss');
      $files = preg_grep(';_ALL.scss$;', $files, PREG_GREP_INVERT);
      sort($files);
      $buf = '';
      foreach ($files as $file) {
        $file = CRM_Utils_File::relativize($file, CRM_Utils_File::addTrailingSlash(dirname($this->getBuildDir())));
        $buf .= sprintf("@import \"%s\";\n", $file);
      }
      file_put_contents($allFile, $buf);
    }
  }

  protected function debug($message) {
     // echo "[$message]\n";
  }

}

function _find_dirs($dir) {
  $todos = array($dir);
  $result = array();
  while (!empty($todos)) {
    $subdir = array_shift($todos);
    foreach ((array) glob("$subdir/*") as $match) {
      if (is_dir($match)) {
        $result[] = $match;
      }
    }
    if ($dh = opendir($subdir)) {
      while (FALSE !== ($entry = readdir($dh))) {
        $path = $subdir . DIRECTORY_SEPARATOR . $entry;
        if ($entry{0} == '.') {
        }
        elseif (is_dir($path)) {
          $todos[] = $path;
        }
      }
      closedir($dh);
    }
  }

  $result = array_unique($result);
  sort($result);
  return $result;
}
