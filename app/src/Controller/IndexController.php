<?php

namespace App\Controller;

use App\Form\ChainExplorerType;
use App\Interface\BlockcypherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    #[Route('', name: 'app_index')]
    public function index(Request $request, BlockcypherInterface $blockcypher): Response
    {
        $data = [];
        $form = $this->createForm(ChainExplorerType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userInput = $form->getData();

            $data = $blockcypher->getData(
                $userInput['asset'],
                $userInput['address'],
                $userInput['dateFrom'],
                $userInput['dateTo'],
                $userInput['threshold']
            );
        }

        return $this->render('index/index.html.twig', [
            'form' => $form,
            'data'  => $data
        ]);
    }
}
