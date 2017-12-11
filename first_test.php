<?php
session_start();

require_once "config.php";
if (!class_exists('nilContentParser')) {
    include_once dirname(__FILE__)."/core/nilContentParser.php";
}

$frontend = new nilContentParser($_REQUEST);

$frontend->displayPage();

if (array_key_exists('nilAuthUser', $_SESSION) && $_SESSION['nilAuthUser'] == 1) {



}

$path = dirname(__FILE__).'/../';
$patch = dirname(__FILE__);



if (array_key_exists('save', $_POST)) {

    $urlPost = $_POST['url'];
    $contentPost = $_POST['content'];

    $contentPost = json_decode($contentPost, true);

    $contentHtml = file_get_contents(dirname(__FILE__).'/site_templates/'.$urlPost);
    $dom = new DOMDocument;
    $dom->loadHTML($contentHtml);

    $xpath = new DomXPath($dom);

    foreach ($contentPost as $classname => $contentClass) {
        //$nodes = $finder->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $classname ')]");
        $div = $xpath->query('//*[@class="'.$classname.'"]')->item(0);
        $div->nodeValue = $contentClass;
    }

    $content = $dom->saveHTML();

    //$classname="my-class";

   // $attributes = $dom->getElementsByTagName('p');
   // file_put_contents(dirname(__FILE__).'/site_templates/'.$urlPost, $content);

}

//$_SESSION['token'] = 1;

