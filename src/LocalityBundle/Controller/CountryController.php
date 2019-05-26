<?php

namespace LocalityBundle\Controller;

use LocalityBundle\Entity\Country;
use LocalityBundle\Form\CountryType;
use MainBundle\Annotations\Permissions;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;

/**
 * Class CountryController
 * @package LocalityBundle\Controller
 * @Breadcrumb("Country", routeName="country_list")
 */
class CountryController extends Controller
{

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Route("/country", name="country_list")
     * @Route("/country/{id}/update", name="country_update")
     * @Permissions("list_countries", group="country",desc="list all countries")
     * @Permissions("create_countries", group="country",desc="create country")
     * @Permissions("update_countries", group="country",desc="update country")
     */
    public function listAction(Request $request){
        $id = $request->get('id');
        $em = $this->getDoctrine()->getManager();
        $countries = $em->getRepository(Country::class)->findBy([
            'deleted' => false
        ]);
        $message = "Country added.";

        $isUpdating = false;
        if($id){
            $country = $em->getRepository(Country::class)->findOneBy([
                'deleted' => false,
                'id' => $id
            ]);
            if(! $country instanceof Country){
                return $this->redirectToRoute('country_list');
            }
            $isUpdating = true;
            $message = "Country updated.";
            $apy = $this->get('apy_breadcrumb_trail');
            $apy->add($country->getName())->add('Update');
        }   else    {
            $country = new Country();
        }

        $form = $this->createForm(CountryType::class, $country);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $formData = $form->getData();
            $em->persist($formData);
            try{
                $em->flush();
                $this->addFlash('success',$message);
            }   catch (\Throwable $e){
                $this->addFlash('message', $e->getMessage());
            }
            return $this->redirectToRoute('country_list');
        }

        $data['isUpdating'] = $isUpdating;
        $data['form'] = $form->createView();
        $data['countries'] = $countries;
        return $this->render('@Locality/country.html.twig', $data);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/country/{id}/delete", name="country_delete")
     * @Permissions("delete_country", group="country", desc="Delete Country")
     */
    public function deleteAction(Request $request){
        $id = $request->get('id');
        $em = $this->getDoctrine()->getManager();
        $country = $em->getRepository(Country::class)->find($id);
        if($country instanceof Country){
            $country->setDeleted(true);
            $em->persist($country);
            try{
                $em->flush();
                $this->addFlash('success','Country deleted.');
            }   catch (\Throwable $e){
                $this->addFlash('error', $e->getMessage());
            }
        }
        return $this->redirectToRoute('country_list');
    }

}
