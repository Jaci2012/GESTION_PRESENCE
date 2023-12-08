<?php

namespace App\Controller;

use App\Entity\Prof;
use App\Entity\Room;
use App\Entity\Level;
use App\Entity\Career;
use App\Form\ProfType;
use App\Form\RoomType;
use App\Entity\Student;
use App\Entity\Subject;
use App\Form\LevelType;
use App\Form\CareerType;
use App\Form\StudentType;
use App\Form\SubjectType;
use Endroid\QrCode\QrCode;
use App\Repository\ProfRepository;
use App\Repository\RoomRepository;
use App\Repository\LevelRepository;
use Endroid\QrCode\Builder\Builder;
use App\Repository\CareerRepository;
use Endroid\QrCode\Writer\PngWriter;
use App\Repository\StudentRepository;
use App\Repository\SubjectRepository;
use Endroid\QrCode\Encoding\Encoding;
use Doctrine\ORM\EntityManagerInterface;
use Endroid\QrCode\Response\QrCodeResponse;
use Endroid\QrCode\Builder\BuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[IsGranted('ROLE_ADMIN')]
class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    //STUDENTS

    #[Route('/students', name: 'students', methods: ['GET'])]
    public function student(StudentRepository $studentRepository): Response
    {
        return $this->render('home/students/index.html.twig', [
            'students' => $studentRepository->findAll(),
        ]);
    }

    #[Route('/students/filtered_year', name: 'students_filtered_year', methods: ['GET'])]
    public function studentFilterByYear(EntityManagerInterface $entityManager)
    {
        $repository = $entityManager->getRepository(Student::class);

        // Utilisez la méthode `findBy` pour trier par la propriété `nom`
        $studentFiltered = $repository->findBy([], ['years' => 'ASC']);

        // Vous pouvez également utiliser 'DESC' pour un tri descendant

        // Faites quelque chose avec les entités triées
        // Par exemple, renvoyez-les dans une vue
        return $this->render('home/students/index.html.twig', [
            'students' => $studentFiltered,
        ]);
    }

    #[Route('/students/filtered_niveau', name: 'students_filtered_niveau', methods: ['GET'])]
    public function studentFilterByNiveau(EntityManagerInterface $entityManager)
    {
        $repository = $entityManager->getRepository(Student::class);

        // Utilisez la méthode `findBy` pour trier par la propriété `nom`
        $studentFiltered = $repository->findBy([], ['niveau' => 'ASC']);

        // Vous pouvez également utiliser 'DESC' pour un tri descendant

        // Faites quelque chose avec les entités triées
        // Par exemple, renvoyez-les dans une vue
        return $this->render('home/students/index.html.twig', [
            'students' => $studentFiltered,
        ]);
    }

    #[Route('/students/filtered_parcour', name: 'students_filtered_parcour', methods: ['GET'])]
    public function studentFilterByParcour(EntityManagerInterface $entityManager)
    {
        $repository = $entityManager->getRepository(Student::class);

        // Utilisez la méthode `findBy` pour trier par la propriété `nom`
        $studentFiltered = $repository->findBy([], ['parcour' => 'ASC']);

        // Vous pouvez également utiliser 'DESC' pour un tri descendant

        // Faites quelque chose avec les entités triées
        // Par exemple, renvoyez-les dans une vue
        return $this->render('home/students/index.html.twig', [
            'students' => $studentFiltered,
        ]);
    }

    #[Route('/students/add', name: 'students_add', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $student = new Student();
        $form = $this->createForm(StudentType::class, $student);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $career = $form->get('career')->getData();
            $level = $form->get('level')->getData();
            $student->setCareer($career);
            $student->setLevel($level);
            $entityManager->persist($student);
            $entityManager->flush();

            return $this->redirectToRoute('students', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('home/students/add.html.twig', [
            'student' => $student,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/students/edit', name: 'students_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Student $student, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(StudentType::class, $student);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Vous n'avez pas besoin d'extraire l'objet Career du formulaire ici
            // Il devrait déjà être associé à l'étudiant que vous modifiez

            // Si tout est correct, il vous suffit de appeler flush pour enregistrer les modifications
            $entityManager->flush();

            return $this->redirectToRoute('students', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('home/students/edit.html.twig', [
            'student' => $student,
            'form' => $form->createView(),
            // Assurez-vous de créer une vue du formulaire
        ]);
    }

    #[Route('/student/{id}/delete', name: 'students_delete', methods: ['GET', 'POST'])]
    public function delete(Student $student, Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid($student->getId() . 'delete', $request->request->get('_token'))) {
            dd($student);
            $entityManager->remove($student);
            $entityManager->flush();
            $this->addFlash('success', 'L\'élément a été supprimé avec succès.');
        }

        return $this->redirectToRoute('students');
    }
    #[Route('/{id}/student/view', name: 'students_view', methods: ['GET'])]
    public function show(Student $student): Response
    {
        return $this->render('home/students/view.html.twig', [
            'student' => $student,
        ]);
    }
    //Subject
    #[Route('/profs', name: 'profs')]
    public function prof(ProfRepository $profRepository): Response
    {
        return $this->render('home/professeurs/index.html.twig', [
            'profs' => $profRepository->findAll(),
        ]);
    }

    #[Route('/profs/add', name: 'profs_add', methods: ['GET', 'POST'])]
    public function newProf(Request $request, EntityManagerInterface $entityManager): Response
    {
        $prof = new Prof();
        $form = $this->createForm(ProfType::class, $prof);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($prof);
            $entityManager->flush();

            return $this->redirectToRoute('profs', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('home/professeurs/add.html.twig', [
            'prof' => $prof,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/profs/edit', name: 'profs_edit', methods: ['GET', 'POST'])]
    public function editProf(Request $request, Prof $prof, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProfType::class, $prof);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('profs', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('home/professeurs/edit.html.twig', [
            'prof' => $prof,
            'form' => $form,
        ]);
    }

    //ROOM
    #[Route('/rooms', name: 'rooms')]
    public function room(RoomRepository $roomRepository): Response
    {
        return $this->render('home/salles/index.html.twig', [
            'rooms' => $roomRepository->findAll(),
        ]);
    }

    #[Route('/rooms/add', name: 'rooms_add', methods: ['GET', 'POST'])]
    public function newRoom(Request $request, EntityManagerInterface $entityManager): Response
    {
        $room = new Room();
        $form = $this->createForm(RoomType::class, $room);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($room);
            $entityManager->flush();

            return $this->redirectToRoute('rooms', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('home/salles/add.html.twig', [
            'room' => $room,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/rooms/edit', name: 'rooms_edit', methods: ['GET', 'POST'])]
    public function editRoom(Request $request, Room $room, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RoomType::class, $room);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('rooms', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('home/salles/edit.html.twig', [
            'room' => $room,
            'form' => $form,
        ]);
    }
    //LEVEL
    #[Route('/levels', name: 'levels', methods: ['GET'])]
    public function level(LevelRepository $levelRepository): Response
    {
        return $this->render('home/levels/index.html.twig', [
            'levels' => $levelRepository->findAll(),
        ]);
    }

    #[Route('/levels/add', name: 'levels_add', methods: ['GET', 'POST'])]
    public function newLevel(Request $request, EntityManagerInterface $entityManager): Response
    {
        $level = new Level();
        $form = $this->createForm(LevelType::class, $level);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($level);
            $entityManager->flush();

            return $this->redirectToRoute('levels', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('home/levels/add.html.twig', [
            'level' => $level,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/levels/edit', name: 'level_edit', methods: ['GET', 'POST'])]
    public function editLevel(Request $request, Level $level, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(LevelType::class, $level);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('levels', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('home/subjects/edit.html.twig', [
            'level' => $level,
            'form' => $form,
        ]);
    }

    //CAREER
    #[Route('/careers', name: 'careers', methods: ['GET'])]
    public function career(CareerRepository $careerRepository): Response
    {
        return $this->render('home/careers/index.html.twig', [
            'careers' => $careerRepository->findAll(),
        ]);
    }

    #[Route('/careers/add', name: 'careers_add', methods: ['GET', 'POST'])]
    public function newCareer(Request $request, EntityManagerInterface $entityManager): Response
    {
        $career = new Career();
        $form = $this->createForm(CareerType::class, $career);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($career);
            $entityManager->flush();

            return $this->redirectToRoute('careers', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('home/careers/add.html.twig', [
            'career' => $career,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/careers/edit', name: 'career_edit', methods: ['GET', 'POST'])]
    public function editCareer(Request $request, Career $career, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CareerType::class, $career);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('careers', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('home/careers/edit.html.twig', [
            'level' => $career,
            'form' => $form,
        ]);
    }

    //SUBJECT
    #[Route('/subjects', name: 'subjects', methods: ['GET'])]
    public function subject(SubjectRepository $subjectRepository): Response
    {
        return $this->render('home/subjects/index.html.twig', [
            'subjects' => $subjectRepository->findAll(),
        ]);
    }

    #[Route('/subjects/add', name: 'subjects_add', methods: ['GET', 'POST'])]
    public function newSubject(Request $request, EntityManagerInterface $entityManager): Response
    {
        $subject = new Subject();
        $form = $this->createForm(SubjectType::class, $subject);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($subject);
            $entityManager->flush();

            return $this->redirectToRoute('subjects', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('home/subjects/add.html.twig', [
            'subject' => $subject,
            'form' => $form,
        ]);
    }


    #[Route('/{id}/subjects/edit', name: 'subject_edit', methods: ['GET', 'POST'])]
    public function editSubject(Request $request, Subject $subject, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SubjectType::class, $subject);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('subjects', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('home/subjects/edit.html.twig', [
            'subject' => $subject,
            'form' => $form,
        ]);
    }
    //QRCODE
    #[Route('/students/{id}/qrcode', name: 'student_qrcode')]
    public function generateQRCode(BuilderInterface $qrCodeBuilder, Student $student)
    {
        // Récupérez les données de l'étudiant
        $studentData = [
            'Matricule' => $student->getMatricule(),
            'Nom' => $student->getLastname(),
            'Prénom' => $student->getFirstname(),
            'Parcours' => $student->getCareer(),
            'Niveau' => $student->getLevel(),
            'Année universitaire' => $student->getYears(),
        ];

        // Utilisez ces données pour personnaliser le contenu du QR Code
        $qrCodeBuilder->data(json_encode($studentData));

        // Utilisez le builder pour générer le QR Code
        $qrCode = $qrCodeBuilder->build();

        // Récupérez l'image du QR Code sous forme de ressource
        $qrCodeImage = $qrCode->writeString();

        // Créez une réponse Symfony pour afficher le QR Code dans un navigateur
        $response = new Response($qrCodeImage, Response::HTTP_OK, [
            'Content-Type' => 'image/png',
        ]);

        return $response;
    }
}