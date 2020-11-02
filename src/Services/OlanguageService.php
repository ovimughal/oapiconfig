<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Oapiconfig\Services;

use Interop\Container\ContainerInterface;

/**
 * Description of OlanguageService
 *
 * @author ovimughal
 */
class OlanguageService
{

    private $language = null;
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    private function extractLanguage()
    {
        $request = $this->container->get('Request');
        $languageHeader = $request->getHeader('X-Language');

        if ($languageHeader) {
            list($language) = sscanf($languageHeader->toString(), 'X-Language: %s');
            $this->setLanguage($language);
        }
    }

    private function setLanguage($language)
    {
        $this->language = $language;
    }

    public function getLanguage()
    {
        $this->extractLanguage();
        return $this->language;
    }

}
