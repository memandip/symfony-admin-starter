<?php


namespace MainBundle\Twig;

use JMS\DiExtraBundle\Annotation as DI;

/**
 * Class TemplateExtension
 * @package MainBundle\Twig
 * @DI\Service("service.main.template")
 * @DI\Tag("twig.extension")
 */
class TemplateExtension extends \Twig_Extension
{

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('no_contents', [$this, 'noContents'], ['is_safe' => ['html']])
        ];
    }

    public function noContents($message, $class = 'info')
    {
        $html = "<div class='alert alert-{$class}'>";
        $html .= "<p>{$message}</p>";
        $html .= "</div>";
        return $html;
    }

}
