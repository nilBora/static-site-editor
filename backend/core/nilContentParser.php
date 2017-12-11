<?php

class nilContentParser
{
    private $_request;

    public function __construct($request)
    {
        $this->_request = $request;

        if ($this->_hasSaveInRequest()) {
            $this->_onSaveContent();
            exit;
        }
    }

    private function _onSaveContent()
    {
        $urlPost = $this->_request['url'];
        $contentPost = $this->_request['content'];

        $contentPost = json_decode($contentPost, true);

        $contentHtml = file_get_contents(PATH_GENERATE_TEMPLATE.$urlPost);
        $dom = new DOMDocument;
        $dom->loadHTML($contentHtml);

        $xpath = new DomXPath($dom);

        foreach ($contentPost as $classname => $contentClass) {
            $div = $xpath->query('//*[@class="'.$classname.'"]')->item(0);
            $div->nodeValue = $contentClass;
        }

        $content = $dom->saveHTML();

        //$classname="my-class";

        // $attributes = $dom->getElementsByTagName('p');
        file_put_contents(PATH_GENERATE_TEMPLATE.$urlPost, $content);
    }

    private function _hasSaveInRequest()
    {
        return array_key_exists('save', $this->_request);
    }

    public function displayPage()
    {
        if ($this->_hasUrlParamInRequest()) {
            $file = $this->_request['url'];

            if ($this->isAdmin()) {
                $this->_doPrepareAdminHtml($file);
            }

            if ($this->_hasStaticPageInTemplate($file)) {
                $content = $this->_fetchGenerateContent($file);
            } else {
                $content = $this->_fetchOriginalContent($file);
            }

            if ($this->isAdmin()) {
                $js = '<script type="text/javascript" src="/backend/js/app.js" ></script>';
                $content = $content.$js;
            }
            echo $content;
        }
    }

    private function _fetchOriginalContent($file)
    {
        $pathHtmlNew = PATH_STATIC_PAGE . '/' . $file;
        $htmlNew = file_get_contents($pathHtmlNew);
        return $htmlNew;
    }

    private function _fetchGenerateContent($file)
    {
        $pathHtmlNew = PATH_GENERATE_TEMPLATE.$file;
        $htmlNew = file_get_contents($pathHtmlNew);
        return $htmlNew;
    }

    public function isAdmin()
    {
        return array_key_exists('nilAuthUser', $_SESSION) &&
               $_SESSION['nilAuthUser'] == 1;
    }

    private function _doPrepareAdminHtml($file)
    {
		$path = PATH_STATIC_PAGE.$file;
		if (file_exists(PATH_GENERATE_TEMPLATE.$file)) {
			$path = PATH_GENERATE_TEMPLATE.$file;
		}
        $html = file_get_contents($path);
        $dom = new DOMDocument;
        libxml_use_internal_errors(true);
        $dom->loadHTML($html);

        $tags = $this->_getAllowedTags();

        $attributes = $dom->getElementsByTagName('p');

        $className = 'nil-edit-content-';
        $i = 0;
        foreach ($attributes as $node) {
            $i++;
            $myClass = $className.$i;
            if ($node->hasAttribute('class')) {
                $class = (string)$node->getAttribute('class');
                $myClass = $class.' '.$myClass;
            }

            $node->setAttribute('class', $myClass);
            $node->setAttribute('data-nilcontent', $myClass);
        }

        $bodyAttr = $dom->getElementsByTagName('body');

        foreach ($bodyAttr as $nod) {
            $nod->setAttribute('data-nilUrl', $file);
        }

        $content = $dom->saveHTML();

        file_put_contents(PATH_GENERATE_TEMPLATE.$file, $content);
    }

    private function _getAllowedTags()
    {
        return array('p','li');
    }

    private function _hasUrlParamInRequest()
    {
        return array_key_exists('url', $this->_request);
    }

    private function _hasStaticPageInTemplate($file)
    {
        return file_exists(PATH_GENERATE_TEMPLATE.$file);
    }
}