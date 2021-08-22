<?php

declare(strict_types=1);

namespace App\Controller\Work\Projects\Project\Settings;

use App\Annotation\Guid;
use App\Model\Work\Entity\Projects\Project\Project;
use App\Model\Work\UseCase\Projects\Project\Archive;
use App\Model\Work\UseCase\Projects\Project\Edit;
use App\Model\Work\UseCase\Projects\Project\Reinstate;
use App\Model\Work\UseCase\Projects\Project\Remove;
use App\Security\Voter\Work\Projects\ProjectAccess;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/work/projects/{project_id}/settings",
 *     name="work.projects.project.settings",
 *     requirements={"project_id"=Guid::PATTERN}
 * )
 * @ParamConverter("project", options={"id" = "project_id"})
 */
class SettingsController extends AbstractController
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @Route("", name="")
     */
    public function show(Project $project): Response
    {
        $this->denyAccessUnlessGranted(ProjectAccess::EDIT, $project);

        return $this->render('app/work/projects/project/settings/show.html.twig', compact('project'));
    }

    /**
     * @Route("/edit", name=".edit")
     */
    public function edit(Project $project, Request $request, Edit\Handler $handler): Response
    {
        $this->denyAccessUnlessGranted(ProjectAccess::EDIT, $project);

        $command = Edit\Command::fromProject($project);

        $form = $this->createForm(Edit\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);

                return $this->redirectToRoute('work.projects.project.show', ['id' => $project->getId()]);
            } catch (\DomainException $e) {
                $this->logger->warning($e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('app/work/projects/project/settings/edit.html.twig', [
            'project' => $project,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/archive", name=".archive", methods={"POST"})
     */
    public function archive(Project $project, Request $request, Archive\Handler $handler): Response
    {
        $this->denyAccessUnlessGranted(ProjectAccess::EDIT, $project);

        if (!$this->isCsrfTokenValid('archive', $request->request->get('token'))) {
            return $this->redirectToRoute('work.projects.project.show', ['id' => $project->getId()]);
        }

        $command = new Archive\Command($project->getId()->getValue());

        try {
            $handler->handle($command);
        } catch (\DomainException $e) {
            $this->logger->warning($e->getMessage(), ['exception' => $e]);
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('work.projects.project.settings', ['project_id' => $project->getId()]);
    }

    /**
     * @Route("/reinstate", name=".reinstate", methods={"POST"})
     */
    public function reinstate(Project $project, Request $request, Reinstate\Handler $handler): Response
    {
        $this->denyAccessUnlessGranted(ProjectAccess::EDIT, $project);

        if (!$this->isCsrfTokenValid('reinstate', $request->request->get('token'))) {
            return $this->redirectToRoute('work.projects.project.show', ['id' => $project->getId()]);
        }

        $command = new Reinstate\Command($project->getId()->getValue());

        try {
            $handler->handle($command);
        } catch (\DomainException $e) {
            $this->logger->warning($e->getMessage(), ['exception' => $e]);
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('work.projects.project.settings', ['project_id' => $project->getId()]);
    }

    /**
     * @Route("/delete", name=".delete", methods={"POST"})
     */
    public function delete(Project $project, Request $request, Remove\Handler $handler): Response
    {
        $this->denyAccessUnlessGranted(ProjectAccess::EDIT, $project);

        if (!$this->isCsrfTokenValid('delete', $request->request->get('token'))) {
            return $this->redirectToRoute('work.projects.project.show', ['id' => $project->getId()]);
        }

        $command = new Remove\Command($project->getId()->getValue());

        try {
            $handler->handle($command);
        } catch (\DomainException $e) {
            $this->logger->warning($e->getMessage(), ['exception' => $e]);
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('work.projects');
    }
}
