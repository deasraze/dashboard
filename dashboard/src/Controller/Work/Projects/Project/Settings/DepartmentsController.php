<?php

declare(strict_types=1);

namespace App\Controller\Work\Projects\Project\Settings;

use App\Annotation\Guid;
use App\Controller\ErrorHandler;
use App\Model\Work\Entity\Projects\Project\Department\Id;
use App\Model\Work\Entity\Projects\Project\Project;
use App\Model\Work\UseCase\Projects\Project\Department\Create;
use App\Model\Work\UseCase\Projects\Project\Department\Edit;
use App\Model\Work\UseCase\Projects\Project\Department\Remove;
use App\ReadModel\Work\Projects\Project\DepartmentFetcher;
use App\Security\Voter\Work\Projects\ProjectAccess;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/work/projects/{project_id}/settings/departments",
 *     name="work.projects.project.settings.departments",
 *     requirements={"project_id"=Guid::PATTERN}
 * )
 * @ParamConverter("project", options={"id" = "project_id"})
 */
class DepartmentsController extends AbstractController
{

    private ErrorHandler $errors;

    public function __construct(ErrorHandler $errors)
    {
        $this->errors = $errors;
    }

    /**
     * @Route("", name="")
     */
    public function index(Project $project, DepartmentFetcher $fetcher): Response
    {
        $this->denyAccessUnlessGranted(ProjectAccess::MANAGE_MEMBERS, $project);

        return $this->render('app/work/projects/project/settings/departments/index.html.twig', [
            'project' => $project,
            'departments' => $fetcher->allOfProject($project->getId()->getValue()),
        ]);
    }

    /**
     * @Route("/create", name=".create")
     */
    public function create(Project $project, Request $request, Create\Handler $handler): Response
    {
        $this->denyAccessUnlessGranted(ProjectAccess::MANAGE_MEMBERS, $project);

        $command = new Create\Command($project->getId()->getValue());

        $form = $this->createForm(Create\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);

                return $this->redirectToRoute('work.projects.project.settings.departments', ['project_id' => $project->getId()]);
            } catch (\DomainException $e) {
                $this->errors->handle($e);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('app/work/projects/project/settings/departments/create.html.twig', [
            'project' => $project,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name=".edit", requirements={"id"=Guid::PATTERN})
     */
    public function edit(Project $project, string $id, Request $request, Edit\Handler $handler): Response
    {
        $this->denyAccessUnlessGranted(ProjectAccess::MANAGE_MEMBERS, $project);

        $department = $project->getDepartment(new Id($id));

        $command = Edit\Command::fromDepartment($project, $department);

        $form = $this->createForm(Edit\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);

                return $this->redirectToRoute('work.projects.project.settings.departments.show', [
                    'project_id' => $project->getId(),
                    'id' => $department->getId()
                ]);
            } catch (\DomainException $e) {
                $this->errors->handle($e);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('app/work/projects/project/settings/departments/edit.html.twig', [
            'project' => $project,
            'department' => $department,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/delete", name=".delete", requirements={"id"=Guid::PATTERN}, methods={"POST"})
     */
    public function delete(Project $project, string $id, Request $request, Remove\Handler $handler): Response
    {
        $this->denyAccessUnlessGranted(ProjectAccess::MANAGE_MEMBERS, $project);

        if (!$this->isCsrfTokenValid('delete', $request->request->get('token'))) {
            return $this->redirectToRoute('work.projects.project.settings.departments', ['project_id' => $project->getId()]);
        }

        $department = $project->getDepartment(new Id($id));

        $command = new Remove\Command($project->getId()->getValue(), $department->getId()->getValue());

        try {
            $handler->handle($command);
        } catch (\DomainException $e) {
            $this->errors->handle($e);
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('work.projects.project.settings.departments', ['project_id' => $project->getId()]);
    }

    /**
     * @Route("/{id}", name=".show", requirements={"id"=Guid::PATTERN})
     */
    public function show(Project $project): Response
    {
        return $this->redirectToRoute('work.projects.project.settings.departments', ['project_id' => $project->getId()]);
    }
}
