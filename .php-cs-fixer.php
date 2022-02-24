<?php
/**
 * Created by cynic-img.
 * User: ogenes
 * Date: 2021/7/4
 */

$config = new PhpCsFixer\Config();
return $config->setRules([
    '@PSR2' => true
])->setFinder(PhpCsFixer\Finder::create()->in(__DIR__));
