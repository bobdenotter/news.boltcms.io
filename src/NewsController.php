<?php

declare(strict_types=1);

namespace App;

use App\Entity\Hits;
use App\Repository\HitsRepository;
use Bolt\Controller\TwigAwareController;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class NewsController extends TwigAwareController
{
    /** @var EntityManager */
    private $entityManager;

    /** @var HitsRepository */
    private $hitsRepository;

    public function __construct(EntityManagerInterface $entityManager, HitsRepository $hitsRepository)
    {
        $this->entityManager = $entityManager;
        $this->hitsRepository = $hitsRepository;
    }

    /**
     * @Route("/", methods={"GET"}, name="news")
     */
    public function index(Request $request)
    {
        $this->addHit($request);

        $response = $this->renderTemplate('json.twig', []);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    private function addHit(Request $request): void
    {
        $options = @unserialize(base64_decode($request->get('hash', ''), false));

        if (! $options) {
            return;
        }

        $options['remote'] = $request->getClientIp();

        if ($this->hitsRepository->findOneForToday($options)) {
            return;
        }

        $hit = new Hits();
        $hit
            ->setVersion($options['v'] ?? '')
            ->setPhp($options['php'] ?? '')
            ->setLocal($options['host'] ?? '')
            ->setName($options['name'] ?? '')
            ->setEnv($options['env'] ?? '')
            ->setDbdriver($options['db_driver'] ?? '')
            ->setDbversion($options['db_version'] ?? '')
            ->setRemote($options['remote']);

        $this->entityManager->persist($hit);
        $this->entityManager->flush();
    }
}
