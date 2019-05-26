<?php

namespace YarshaStudio\MainBundle\Twig\Extension;

use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\HttpFoundation\Request;
use YarshaStudio\CustomerBundle\Entity\Card;
use YarshaStudio\TransactionBundle\Entity\Transaction;


/**
 * Class PremiumStatusExtension
 * @package YarshaStudio\AdminBundle\Twig\Extension
 *
 * @DI\Service("ys.twig.render")
 * @DI\Tag(name="twig.extension")
 */
class RenderExtension extends \Twig_Extension
{

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('not_available', [$this, 'notAvailable']),
            new \Twig_SimpleFunction('no_contents', [$this, 'noContents'], ['is_safe'=>['html']]),
            new \Twig_SimpleFunction('render_modal', [$this, 'renderModal'], ['is_safe'=>['html']]),
            new \Twig_SimpleFunction('render_modal_default_body', [$this, 'modalDefaultContent'], ['is_safe'=>['html']]),
            new \Twig_SimpleFunction('ys_show_filter', [$this, 'showFilterOnRefresh'],['is_safe' => ['html']]),
            new \Twig_SimpleFunction('render_show_filter_button', [$this, 'renderShowFilterButton'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('render_boolean_value', [$this, 'renderBooleanValue'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('render_card_status', [$this, 'renderCardStatus'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('render_TransactionType', [$this, 'renderTransactionType'], ['is_safe' => ['html']])
        ];
    }

    public function notAvailable($string)
    {
        return ($string == '') ? 'N/A' : $string;
    }

    public function noContents($message = '')
    {
        $message = $message ?: 'No data available';
        return "<div class=\"text-danger p-10\"><i class=\"icon-warning2\"></i> &nbsp; {$message}</div>";
    }


    function renderModal($title, $id, $includeFooter = false)
    {
        $footer = "";

        if($includeFooter)
        {
            $footer = "<div class=\"modal-footer\">
                    <button type=\"button\" class=\"btn btn-danger\" data-dismiss=\"modal\">Close</button>
                </div>";
        }

        $html = "<div class=\"modal fade\" id=\"{$id}\" tabindex=\"-1\" >
        <div class=\"modal-dialog\" role=\"document\">
            <div class=\"modal-content\">
                <div class=\"modal-header bg-blue-300\">
                    <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>
                    <h4 class=\"modal-title\">{$title}</h4>
                </div>
                <div class=\"modal-body\">

                    <div class=\"col-md-12 text-center\" style=\"padding: 60px auto;\">
                        <i class=\"fa fa-2x fa-cog fa-spin\"></i>
                    </div>

                    <div class=\"clearfix\"></div>

                </div>

                {$footer}

            </div>
        </div>
    </div>";

        return $html;
    }

    public function modalDefaultContent()
    {
        $html =  "<div class=\"col-md-12 text-center\" style=\"padding: 60px auto;\">";
        $html .=  "<i class=\"fa fa-2x fa-cog fa-spin\"></i>";
        $html .=  "</div><div class=\"clearfix\"></div>";

        return $html;
    }

    public function renderShowFilterButton()
    {
        $html = "<button id='showHideFilterButton' class='btn btn-navy'  title='filter' data-toggle='collapse' data-target='#ysDataFilter' >";
        $html .= "<i class='fa fa-filter position-left'></i>";
        $html .= " Filter</button>";

        return $html;
    }

    public function showFilterOnRefresh(Request $request)
    {
        $query = $request->query->all();

        if( count($query) )
        {
            unset($query['page']);
            unset($query['limit']);
            unset($query['sort']);
            unset($query['order']);

            if( count($query) )
            {
                return true;
            }

        }

        return false;
    }



    public function renderBooleanValue($value){

       if($value == true){

           $html = '<span class="badge bg-green">Yes</span>';

       }else{

           $html = '<span class="badge bg-danger">No</span>';
       }

       return $html;


    }

    public function renderCardStatus($value){

        if($value == Card::CARD_STATUS_ACTIVE){

            $html = '<span class="badge bg-green">Active</span>';

        }else{

            $html = '<span class="badge bg-danger">Inactive</span>';
        }

        return $html;


    }

   public function renderTransactionType($value){

       switch ($value){

           case Transaction::TRANSACTION_TYPE_DEPOSIT:
                $type = 'Deposit';
                break;

           case Transaction::TRANSACTION_TYPE_CHARGE:
               $type = 'Charge';
               break;

           case Transaction::TRANSACTION_TYPE_REFUND_PARTIAL:
               $type = 'Partial Refund';
               break;

           case Transaction::TRANSACTION_TYPE_REFUND_FULL:
               $type = 'Full Refund';
               break;

       }

       return '<span class="badge badge-success">'.$type.'</span>';



   }



    public function getName()
    {
        return 'render_extension';
    }

}
