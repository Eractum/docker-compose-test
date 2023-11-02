<?php
class ControllerParticipant extends Controller
{

    function __construct()
    {
        $this->service = new Service_Participant();
        $this->view = new View();
    }

    //make some actions like "generate_99_users" or "reset_db"
    function actionGenerate()
    {
        $users_amount_request = 100;

        $data = $this->service->request($users_amount_request);

        $this->view->generate('participant_view.php', 'template_participant_view.php', $data);
    }

    function actionReset()
    {
        $data = $this->service->request('reset');

        $this->view->generate('participant_view.php', 'template_participant_view.php', $data);
    }
    function actionIndex()
    {
        $data = $this->service->request();

        $this->view->generate('participant_view.php', 'template_participant_view.php', $data);
    }
}