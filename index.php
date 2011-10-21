<?php

require_once __DIR__ . '/vendor/Silex/silex.phar';

$app = new Silex\Application();

$app->register(new Silex\Extension\TwigExtension(), array(
    'twig.path' => __DIR__ . '/web/src',
    'twig.class_path' => __DIR__ . '/vendor',
    'twig.options' => array('cache' => __DIR__ . '/cache'),
));

$app->get('/', function() use ($app) {

          // layout pages list
          return $app['twig']->render('index.html.twig');
        });

$app->get('/{url_slug}', function($url_slug) use ($app) {
          $output = $app['twig']->render("$url_slug.html.twig");

          // Tidy
          $config = array(
              'indent' => true,
              'output-xhtml' => true,
              'doctype' => 'strict',
              'numeric-entities' => 'yes',
              'vertical-space' => true,
              'new-blocklevel-tags' => 'header, footer',
              'new-inline-tags' => 'video, audio, canvas, ruby, rt, rp',
              'tidy-mark' => false,
              'wrap' => 300);
          
          $tidy = new tidy;
          $tidy->parseString($output, $config, 'utf8');
          
          $tidy->cleanRepair();
                    
          $output_folder = 'web';
          $html_file = "web/$url_slug.html";
          $handle = fopen($html_file, 'w') or die("can't open file");
          fwrite($handle, $tidy);

          fclose($handle);

          return $app['twig']->render($url_slug . '.html.twig');
        });

$app['debug'] = true;
$app->run();