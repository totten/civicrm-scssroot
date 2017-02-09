<?php
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'BuildScss.php';

$s = new BuildScss();
$s->update();
echo json_encode(array(
  'buildPath' => dirname($s->getBuildPath()),
));
