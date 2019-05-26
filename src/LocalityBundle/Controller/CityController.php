<?php

namespace LocalityBundle\Controller;

use LocalityBundle\Entity\City;
use LocalityBundle\Form\CityType;
use MainBundle\Annotations\Permissions;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;

/**
 * Class CityController
 * @package LocalityBundle\Controller
 * @Breadcrumb("City", routeName="city_list")
 */
class CityController extends Controller
{

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Route("/city", name="city_list")
     * @Route("/city/{id}/update", name="city_update")
     * @Permissions("list_cities", group="city",desc="list all cities")
     * @Permissions("create_city", group="city",desc="create city")
     * @Permissions("update_cities", group="city",desc="update city")
     */
    public function listAction(Request $request){
        $id = $request->get('id');
        $em = $this->getDoctrine()->getManager();
        $message = "City added";
        $isUpdating = false;
        if($id){
            $city = $em->getRepository(City::class)->findOneBy([
                'deleted' => false,
                'id' => $id
            ]);

            if(! $city instanceof City){
                return $this->redirectToRoute('city_list');
            }
            $isUpdating = true;
            $message = "City updated.";
            $this->get('apy_breadcrumb_trail')
                ->add($city->getName())
                ->add('Update');
        }   else    {
            $city = new City();
        }
        $form = $this->createForm(CityType::class, $city);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $formData = $form->getData();
            try{
                $em->persist($formData);
                $em->flush();
                $this->addFlash('success', $message);
            }   catch (\Throwable $e){
                $this->addFlash('error', $message);
            }
            return $this->redirectToRoute('city_list');
        }

        $data['isUpdating'] = $isUpdating;
        $data['cities'] = $em->getRepository(City::class)->findBy(['deleted' => false]);
        $data['form'] = $form->createView();
        return $this->render('@Locality/city.html.twig', $data);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/city/{id}/delete", name="city_delete")
     * @Permissions("delete_city", group="city",desc="Delete city")
     */
    public function deleteAction(Request $request){
        $id = $request->get('id');
        $em = $this->getDoctrine()->getManager();
        $city = $em->getRepository(City::class)->find($id);
        if($city instanceof City){
            $city->setDeleted(true);
            try{
                $em->persist($city);
                $em->flush();
                $this->addFlash('success','City deleted.');
            }   catch (\Throwable $e){
                $this->addFlash('error', $e->getMessage());
            }
        }
        return $this->redirectToRoute('city_list');
    }

}
