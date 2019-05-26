<?php


namespace MainBundle\Twig;

use JMS\DiExtraBundle\Annotation as DI;
use MainBundle\Service\PermissionService;

/**
 * Class TemplateExtension
 * @package MainBundle\Twig
 * @DI\Service("service.main.template")
 * @DI\Tag("twig.extension")
 */
class TemplateExtension extends \Twig_Extension
{

    /**
     * @var PermissionService
     * @DI\Inject("service.permission")
     */
    public $permissionService;

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('no_contents', [$this, 'noContents'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('float_button', [$this, 'floatButton'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('float_button_modal', [$this, 'floatModalButton'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('filter_button', [$this, 'filterButton'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('modal_action_button', [$this, 'modalActionButton'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('date_range_picker_element', [$this, 'dateRangePicker'], ['is_safe' => ['html']]),
        ];
    }

    public function noContents($message, $class = 'info')
    {
        $html = "<div class='alert alert-{$class}'>";
        $html .= "<p>{$message}</p>";
        $html .= "</div>";
        return $html;
    }

    public function floatButton($link, $label, $iconClass = 'icon-plus3', $permission = '', $attr = '')
    {
        $button = "<a href=\"%s\" class=\"btn btn-link btn-float has-text text-size-small\" %s>";
        $button .= "<i class=\"%s text-blue-300\"></i><span>%s</span></a>";
        $button = sprintf($button, $link, $attr, $iconClass, $label);
        if( $permission and ! $this->permissionService->hasPermission($permission))
        {
            return '';
        }

        return $button;
    }


    public function filterButton()
    {
        return "<a href=\"#\" class=\"btn btn-link btn-float has-text text-size-small\" id='showHideFilterButton'  title='filter' data-toggle='collapse' data-target='#ysDataFilter' >"
            . "<i class=\"icon-filter3 text-blue-400\"></i><span>Filter</span></a>";

    }

    public function floatModalButton($target, $label, $iconClass = 'icon-plus3', $permission = '', $attr = '')
    {
        $attr .= ' data-toggle="modal" data-target="%s"  data-backdrop="true"';

        return $this->floatButton('#', $label, $iconClass, $permission, sprintf($attr, $target));

    }


    public function modalActionButton($target, $label, $iconClass = 'icon-plus3', $extra = '', $permission = '')
    {
        $button = '<a data-toggle="modal" data-target="%s" %s >';
        $button .= '<i class="%s"></i> %s</a>';

        if( $permission and ! $this->permissionService->hasPermission($permission))
        {
            return '';
        }

        return sprintf($button, $target, $extra, $iconClass, $label );
    }

    public function dateRangePicker($label = '', $fromDate = '', $toDate = '')
    {
        $html = '<div class="form-group">';
        $html .= ($label) ? '<label>'.$label.'</label>' : '';
        $html .= '<div class="input-group">
                        <span class="input-group-addon"><i class="icon-calendar22"></i></span>
                        <input type="text" class="form-control date-range-picker" value="">
                        <input type="hidden" name="from_date" class="from_date" value="'.$fromDate.'">
                        <input type="hidden" name="to_date" class="to_date" value="'.$toDate.'">
                    </div>
                </div>';

        return $html;
    }

    public function getName()
    {
        return 'template_extension';
    }

}
