<?php

require_once __DIR__ . "/../../vendor/autoload.php";
require_once __DIR__."/../models/model_participant.php";
require_once __DIR__."/../models/model_interpreters/participant_tree.php";
require_once __DIR__."/../repositories/repository_participant.php";

/** A Participant Service Model. Has the following request possibilities:
 * - Generate: generates an array with $request amount of participants, adding new ones to a local database ($request == int)
 * - Reset: resets database to its inital state ($request == 'reset')
 */
class Service_Participant extends Service
{

    public function request($request = 0)
    {
        if (is_numeric($request)) {
            $participantRepository = new ParticipantRepository();

            $this->error = $participantRepository->requestReset();
            $participantIniInfo = $participantRepository->requestRead();
            if (is_string($participantIniInfo)) {
                $this->error = $participantIniInfo;
            }

            if ($this->error != null) {
                return $this->error;
            }

            $participantAffiliates = new ParticipantTree($participantIniInfo);

            $newParticipantPointer = count($participantAffiliates);
            $participantAffiliates->insertRandom($request - $newParticipantPointer);

            $dbArray = $participantAffiliates->getAllAsDbArray();

            for ($i = $newParticipantPointer; $i < $request; $i++) {
                if (isset($dbArray[$i])) $participantRepository->requestInsert($dbArray[$i]);
            }

            return $dbArray;
        } elseif ($request === 'reset') {
            $participantRepository = new ParticipantRepository();

            $this->error = $participantRepository->requestReset();

            $participantIniInfo = $participantRepository->requestRead();
            if (is_string($participantIniInfo)) {
                $this->error = $participantIniInfo;
            }

            if ($this->error != null) {
                return $this->error;
            }

            $participantAffiliates = new ParticipantTree($participantIniInfo);
            return $participantAffiliates->getAllAsDbArray();
        } else {
            $this->error = 'User Error: Impossible Request.';
            return $this->error;
        }
    }
}
