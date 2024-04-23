<?php

namespace App\Api\v1\Controller\DemandeClinique;


use App\Repository\DemandeClinique\ReponseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


class ResponseController extends AbstractController
{
    /**
     * @Route("/reponse/validate", name="api_v1_depots_valider_reponse", methods={"POST"})
     */
    public function validerReponse(
        ReponseRepository $repository,
        Request $request,
        EntityManagerInterface $manager
    ):JsonResponse
    {

        $response = [
            "content"=>"Vous avez bien validé les réponses d'id ",
            "code"=>200
        ];
        $hasNewValidatedReponses = false;
        $data = json_decode($request->getContent(), true);
        foreach ($data['reponses'] as $item){
            $reponse = $repository->find($item["id"]);
            if ($reponse && $reponse->getState()===false){
                $hasNewValidatedReponses = true;
                $reponse->setState(true);
                $reponse->setValidationReason($item["reason"]);
                $response["content"] .= $reponse->getId() .",";
                $manager->persist($reponse);
            }
        }
        $manager->flush();
        if (!$hasNewValidatedReponses){
            $response["content"]= "Pas de nouvelles reponse a valider";
        }

        return $this->json($response);
    }

}
