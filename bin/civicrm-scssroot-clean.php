<?php

require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'BuildScss.php';

$s = new BuildScss();
$s->clean();
echo json_encode(array(
  'buildPath' => $s->getBuildPath(),
  'basePath' => dirname($s->getBuildPath()),
));
