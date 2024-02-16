<?php

namespace App\Controller;

use App\Entity\Campagne;
use App\Entity\CampagneVotant;
use App\Entity\Vote;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CampagneController extends AbstractController{
    private $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    /**
     * @Route("/", name="mes_campagnes")
     */
    public function mesCampagnes(Request $request){
        $id=1;
        $campagne=$this->em->getRepository(Campagne::class)->find($id);
        if (!$campagne){
            return $this->render('base.html.twig',['mesCampagnes'=>[]]);

        }
        $mesCampagnes=$this->em->getRepository(Campagne::class)->getMyCampains($id);
        //dd($mesCampagnes);
        return $this->render('base.html.twig',['mesCampagnes'=>$mesCampagnes]);
    }
    /**
     * @Route("/all_campagnes", name="all_campagnes")
     */
    public function allCampagnes(Request $request){
        $id=1;
        $campagnes=$this->em->getRepository(Campagne::class)->allCampains();
        for($i=0;$i<count($campagnes);$i++){
           $candidats=$this->em->getRepository(Vote::class)->getScoresCandidats($campagnes[$i]['id']);
              $campagnes[$i]['candidats']=$candidats;
        }
        dd($campagnes);
        if (!$campagnes){
            return $this->render('base.html.twig',['mesCampagnes'=>[],'campagne'=>null]);

        }
        $mesCampagnes=$this->em->getRepository(Campagne::class)->getMyCampains($id);
        dd($mesCampagnes);
        return $this->render('base.html.twig',['mesCampagnes'=>$mesCampagnes,'campagne'=>$campagne]);
    }
}
