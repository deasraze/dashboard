<?php

declare(strict_types=1);

namespace App\Controller\Work\Members;

use App\Model\Work\Entity\Members\Group\Group;
use App\Model\Work\UseCase\Members\Group\Create;
use App\Model\Work\UseCase\Members\Group\Edit;
use App\Model\Work\UseCase\Members\Group\Remove;
use App\ReadModel\Work\Members\GroupFetcher;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/work/members/groups", name="work.members.groups")
 */
class GroupsController extends AbstractController
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @Route("", name="")
     */
    public function index(GroupFetcher $fetcher): Response
    {
        $groups = $fetcher->all();

        return $this->render('app/work/members/groups/index.html.twig', compact('groups'));
    }

    /**
     * @Route("/create", name=".create")
     */
    public function create(Request $request, Create\Handler $handler): Response
    {
        $command = new Create\Command();

        $form = $this->createForm(Create\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);

                return $this->redirectToRoute('work.members.groups');
            } catch (\DomainException $e) {
                $this->logger->error($e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('app/work/members/groups/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name=".edit")
     */
    public function edit(Group $group, Request $request, Edit\Handler $handler): Response
    {
        $command = Edit\Command::fromGroup($group);

        $form = $this->createForm(Edit\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);

                return $this->redirectToRoute('work.members.groups.show', ['id' => $group->getId()]);
            } catch (\DomainException $e) {
                $this->logger->error($e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('app/work/members/groups/edit.html.twig', [
            'group' => $group,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/delete", name=".delete", methods={"DELETE"})
     */
    public function delete(Group $group, Request $request, Remove\Handler $handler): Response
    {
        if (!$this->isCsrfTokenValid('delete', $request->request->get('token'))) {
            return $this->redirectToRoute('work.members.groups.show', ['id' => $group->getId()]);
        }

        $command = new Remove\Command($group->getId()->getValue());

        try {
            $handler->handle($command);

            return $this->redirectToRoute('work.members.groups');
        } catch (\DomainException $e) {
            $this->logger->error($e->getMessage(), ['exception' => $e]);
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('work.members.groups.show', ['id' => $group->getId()]);
    }

    /**
     * @Route("/{id}", name=".show")
     */
    public function show(): Response
    {
        return $this->redirectToRoute('work.members.groups');
    }
}
