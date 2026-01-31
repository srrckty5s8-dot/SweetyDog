<?php

class AppointmentController extends Controller
{
    public function index()
    {
        $this->requireLogin();

        $liste_animaux = Animal::getListForAppointments();
        $events = RendezVous::getCalendarEvents();

        $this->view('calendrier_view', compact('liste_animaux', 'events'));
    }

    public function create()
    {
        $this->requireLogin();

        $id_animal = (int)($_POST['id_animal'] ?? 0);
        $titre = trim($_POST['titre'] ?? 'Toilettage');
        $debut = $_POST['date_debut'] ?? null;
        $fin = $_POST['date_fin'] ?? null;

        if ($id_animal <= 0 || !$debut || !$fin) {
            http_response_code(400);
            die("Erreur : Des informations sont manquantes (Animal, date de début ou de fin).");
        }

        RendezVous::create([
            'id_animal' => $id_animal,
            'titre' => $titre,
            'date_debut' => $debut,
            'date_fin' => $fin,
        ]);

        redirect('appointments.index');
    }

    public function delete($id = 0)
    {
        $this->requireLogin();

        $id = (int)$id;
        if ($id <= 0) {
            redirect('appointments.index');
            exit;
        }

        RendezVous::delete($id);

        redirect('appointments.index');
    }
}
