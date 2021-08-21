<?php

declare(strict_types=1);

namespace App\Controller\Work\Projects\Project\Settings;

use App\Annotation\Guid;
use App\Model\Work\Entity\Members\Member\Id;
use App\Model\Work\Entity\Projects\Project\Project;
use App\Model\Work\UseCase\Projects\Project\Membership\Add;
use App\Model\Work\UseCase\Projects\Project\Membership\Edit;
use App\Model\Work\UseCase\Projects\Project\Membership\Remove;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/work/projects/{project_id}/settings/members",
 *     name="work.projects.project.settings.members",
 *     requirements={"project_id"=Guid::PATTERN, "member_id"=Guid::PATTERN}
 * )
 * @ParamConverter("project", options={"id" = "project_id"})
 * @IsGranted("ROLE_WORK_MANAGE_PROJECTS")
 */
class MembersController extends AbstractController
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @Route("", name="")
     */
    public function index(Project $project): Response
    {
        return $this->render('app/work/projects/project/settings/members/index.html.twig', [
            'project' => $project,
            'memberships' => $project->getMemberships(),
        ]);
    }

    /**
     * @Route("/assing", name=".assign")
     */
    public function assign(Project $project, Add\Handler $handler, Request $request): Response
    {
        if (!$project->getDepartments()) {
            $this->addFlash('error', 'Add department before assign member.');

            return $this->redirectToRoute('work.projects.project.settings.members', [
                'project_id' => $project->getId()->getValue()
            ]);
        }

        $command = new Add\Command($project->getId()->getValue());

        $form = $this->createForm(Add\Form::class, $command, ['project' => $project->getId()->getValue()]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);

                return $this->redirectToRoute('work.projects.project.settings.members', ['project_id' => $project->getId()]);
            } catch (\DomainException $e) {
                $this->logger->warning($e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('app/work/projects/project/settings/members/assign.html.twig', [
            'project' => $project,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{member_id}/edit", name=".edit")
     */
    public function edit(Project $project, string $member_id, Request $request, Edit\Handler $handler): Response
    {
        $membership = $project->getMembership(new Id($member_id));

        $command = Edit\Command::fromMembership($project, $membership);

        $form = $this->createForm(Edit\Form::class, $command, [
            'project' => $project->getId()->getValue(),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);

                return $this->redirectToRoute('work.projects.project.settings.members', ['project_id' => $project->getId()]);
            } catch (\DomainException $e) {
                $this->logger->warning($e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('app/work/projects/project/settings/members/edit.html.twig', [
            'project' => $project,
            'membership' => $membership,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{member_id}/revoke", name=".revoke", methods={"POST"})
     */
    public function revoke(Project $project, string $member_id, Request $request, Remove\Handler $handler): Response
    {
        if (!$this->isCsrfTokenValid('revoke', $request->request->get('token'))) {
            return $this->redirectToRoute('work.projects.project.settings.members', ['project_id' => $project->getId()]);
        }

        $command = new Remove\Command($project->getId()->getValue(), $member_id);

        try {
            $handler->handle($command);
        } catch (\DomainException $e) {
            $this->logger->warning($e->getMessage(), ['exception' => $e]);
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('work.projects.project.settings.members', ['project_id' => $project->getId()]);
    }

    /**
     * @Route("/{member_id}", name=".show")
     */
    public function show(Project $project): Response
    {
        return $this->redirectToRoute('work.projects.project.settings.members', ['project_id' => $project->getId()]);
    }
}
