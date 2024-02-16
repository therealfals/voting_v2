<?php

namespace App\Controller;

use App\Entity\Campagne;
use App\Entity\CampagneCandidat;
use App\Entity\CampagneVotant;
use App\Entity\Candidat;
use App\Entity\Votant;
use App\Entity\Vote;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class VoteController extends AbstractController
{
    private $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    /**
     * @Route("/doVote", name="doVote")
     */
    public function doVote(Request $request){
        $connectedVotant=1;

        if ($request->isMethod('POST')){
            $data=$request->request->all();
            $votant=$this->em->getRepository(Votant::class)->find($connectedVotant);
            $campagne=$this->em->getRepository(Campagne::class)->find($data["idCampagne"]);
            $candidat=isset($data['idCandidat'])?
                $this->em->getRepository(Candidat::class)->find($data['idCandidat']):null;
            $vote=new Vote();
            $vote
                ->setCampagne($campagne)
                ->setVotant($votant)
                ->setCandidat($candidat);
            $this->em->persist($vote);
            $this->em->flush();
            return $this->json([
                'message'=>"Votre vote à été pris en compte!",
                'campagne'=>$campagne,
                "candidats"=>null,
                "canVote"=>false,
                "success"=>true]);

        }
    }
    /**
     * @Route("/vote/{id}", name="voter")
     */
    public function voter($id,Request $request)
    {
        $connectedVotant=1;
        $campCandidats=$this->em->getRepository(CampagneCandidat::class)->findBy(['campagne'=>$id]);
        $candidats=[];
        foreach($campCandidats as $campCandidat){
            $candidats[]=$campCandidat->getCandidat();
        }
        $campagneVotant=$this->em->getRepository(CampagneVotant::class)->findOneBy([
            "votant"=>$connectedVotant,
            'campagne'=>$id]);
        if (!$campagneVotant){
            return $this->render('voter.html.twig',[
                'message'=>"Vous n'etes pas autorisé à voter sur cet élection!",
                'campagne'=>null,
                "candidats"=>null,
                "canVote"=>false,
                "success"=>false]);
        }
        $campagne=$campagneVotant->getCampagne();
        if ($campagne && $campagne->isIsClosed()){
            return $this->render('voter.html.twig',
                ['message'=>"Le vote a été clos pour cet élection!",'campagne'=>$campagne,"candidats"=>$candidats,
                "canVote"=>false,
                "success"=>false]);
        }
        $hasVoted=$this->em->getRepository(Vote::class)->findOneBy(["votant"=>$connectedVotant,'campagne'=>$id]);
        if ($hasVoted){
            return $this->render('voter.html.twig',[
                'message'=>"Vous avez déjà voter pour cet élection!",
                'campagne'=>$campagne,
                "candidats"=>$candidats,
                "canVote"=>false,
                "success"=>false
            ]);
        }
        if ($request->isMethod('POST')){
            $data=$request->request->all();
            dd($data);
            $votant=$this->em->getRepository(Votant::class)->find($connectedVotant);
            $candidat=isset($data['idCandidat'])?
                $this->em->getRepository(Candidat::class)->find($data['idCandidat']):null;
            $vote=new Vote();
            $vote
                ->setCampagne($campagne)
                ->setVotant($votant)
                ->setCandidat($candidat);
            $this->em->persist($vote);
            $this->em->flush();
            return $this->render('voter.html.twig',[
                'message'=>"Votre vote à été pris en compte!",
                'campagne'=>$campagne,
                "candidats"=>$candidats,
                "canVote"=>false,
                "success"=>true]);

        }else{

            return $this->render('voter.html.twig',[
                'message'=>null,
                'campagne'=>$campagne,
                "candidats"=>$candidats,
                "canVote"=>true,
                "success"=>true]);
            dd($id);
        }
        }
}
