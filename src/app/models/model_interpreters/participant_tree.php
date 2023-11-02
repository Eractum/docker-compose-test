<?php
require_once __DIR__.'/../miscellaneous/participant_position_updater.php';
require_once __DIR__.'/../model_participant.php';
/** A Participant Logic Tree. Stores Participant instances and their connections according to these instructions:
 * 1. Each participant can have up to 4 affiliate participants
 * 2. Each participant can have one parent participant
 */
class ParticipantTree implements ArrayAccess, Countable
{
    private $affiliateArray = [];
    private $affiliateArrayUnconnectable = [];

    public function __construct(array $participantsIniInfo = [])
    {

        foreach ($participantsIniInfo as $participantIniInfo) {

            $parentParticipantID = null;
            if ($participantIniInfo[7] != 0) {
                $parentParticipantID = $participantIniInfo[7];
            }
            $this->addParticipant(ParticipantFactory::createParticipant(
                $participantIniInfo[1],
                $participantIniInfo[2],
                $participantIniInfo[3],
                $participantIniInfo[4],
                $participantIniInfo[5],
                $participantIniInfo[6]), $parentParticipantID);
        }
    }
    //ArrayAccess implementation
    public function offsetExists($offset): bool
    {
        if (isset($this->affiliateArray[$offset])) {
            return true;
        } else return isset($this->affiliateArrayUnconnectable[$offset]);
    }
    function offsetGet($offset)
    {
        if (isset($this->affiliateArray[$offset])) {
            return $this->affiliateArray[$offset];
        } else return $this->affiliateArrayUnconnectable[$offset];
    }
    function offsetSet($offset, $value)
    {
        if (is_null($offset)) $this->affiliateArray[] = $value;
        else $this->affiliateArray[$offset] = $value;
    }
    function offsetUnset($offset)
    {
        if (isset($this[$offset])) {
            $parentID = $this[$offset]['parentID'];
            $affiliateID = $this[$offset]['affiliateID'];
            if (isset($parentID)) {
                if (isset($this->affiliateArray[$parentID])) {
                    for ($i = 0; $i < count($this->affiliateArray[$parentID]['affiliateID']); $i++) {
                        if ($this->affiliateArray[$parentID]['affiliateID'][$i] === $offset) {
                            unset($this->affiliateArray[$parentID]['affiliateID'][$i]);
                            $this->checkConnectability($this->affiliateArray[$parentID]);
                            break;
                        }
                    }
                } elseif (isset($this->affiliateArrayUnconnectable[$parentID])) {
                    for ($i = 0; $i < count($this->affiliateArrayUnconnectable[$parentID]['affiliateID']); $i++) {
                        if ($this->affiliateArrayUnconnectable[$parentID]['affiliateID'][$i] === $offset) {
                            unset($this->affiliateArrayUnconnectable[$parentID]['affiliateID'][$i]);
                            $this->checkConnectability($this->affiliateArrayUnconnectable[$parentID]);
                            break;
                        }
                    }
                }
            }

            $this->deleteAffiliates($affiliateID);

            if (isset($this->affiliateArray[$parentID]))

                if (isset($this->affiliateArray[$offset])) {
                    unset($this->affiliateArray[$offset]);
                } elseif (isset($this->affiliateArrayUnconnectable[$offset])) unset($this->affiliateArrayUnconnectable[$offset]);

        }
    }
    //Countable implementation
    public function count(): int
    {
        return (count($this->affiliateArray) + count($this->affiliateArrayUnconnectable));
    }

    /** Used when offsetUnset is triggered. Deletes affiliated participants.
     * @param array $affiliateIDs
     * @return void
     */
    private function deleteAffiliates(array $affiliateIDs)
    {
        foreach ($affiliateIDs as $affiliateID) {
            unset($this[$affiliateID]);
        }
    }

    /** Checks whether if a participant can have more affiliates.
     * (WARNING: Automatically switches state of a participant to "Connectable" or "Non-connectable" if it initially has incorrect state)
     * @param int $id
     * @return bool
     */
    public function checkConnectability(int $id)
    {
        if (count($this[$id]['affiliateID']) >= 4) {

            if (isset($this->affiliateArray[$id])) {
                $this->switchPositions($id);
            }
            return false;
        } else {
            if (isset($this->affiliateArrayUnconnectable[$id])) {
                $this->switchPositions($id);
            }
            return true;
        }
    }

    /** Switches state of a participant to "Non-connectable"
     * @param int $id
     * @return void
     */
    private function switchPositions(int $id)
    {
        if (isset($this->affiliateArray[$id])) {
            $this->affiliateArrayUnconnectable[$id] = $this->affiliateArray[$id];
            unset($this->affiliateArray[$id]);
        } elseif (isset($this->affiliateArrayUnconnectable[$id])) {
            $this->affiliateArray[$id] = $this->affiliateArrayUnconnectable[$id];
            unset($this->affiliateArrayUnconnectable[$id]);
        }
    }

    /** Creates $amount of new Participant instances
     * @param $amount
     * @return void
     */
    public function insertRandom($amount = 1)
    {
        for ($i = 0; $i < $amount; $i++) {
            $parentID = array_rand($this->affiliateArray);
            $parent = $this->affiliateArray[$parentID];

            $this->addParticipant(ParticipantFactory::createRandomParticipant($parent['participant']), $parentID);
        }
        $this->updateParticipantPositions();
    }

    /** Inserts a participant. Returns false if its parent is non-connectable.
     * @param Participant $participant
     * @param int|null $parentID
     * @return bool
     */
    public function insert(Participant $participant, int $parentID = null):bool
    {
        if (!$this->checkConnectability($this[$parentID])) {
            return false;
        }
        $this->addParticipant($participant, $parentID);
        $this->updateParticipantPositions();
        return true;
    }

    /** Inserts a participant and moves parent to $affiliateArrayUnconnectable if they exceed affiliate amount.
     * @param Participant $participant
     * @param int|null $parentID
     * @return void
     */
    private function addParticipant(Participant $participant, int $parentID = null)
    {
        if ($parentID === null) {
            $this->affiliateArray[count($this)]['participant'] = $participant;
        } elseif ((!isset($this[$parentID]['affiliateID']) || $this->checkConnectability($parentID)) && $this[$parentID]['participant']->getStartDate() <= $participant->getStartDate()) {
            $newId = count($this);
            $this->affiliateArray[$newId]['participant'] = $participant;
            $this->affiliateArray[$newId]['parentID'] = $parentID;
            $this->affiliateArray[$parentID]['affiliateID'][] = $newId;
            $this->checkConnectability($parentID); //automatically moves parent to $affiliateArrayUnconnectable if it has max affiliates
        }
    }

    /** Updates Participants' "position" value. Uses ParticipantPositionUpdater as instructions.
     * Possible values:
     * - "novice"
     * - "manager"
     * - "vice president"
     * - "president"
     * @param int $affiliateLevels
     * @return void
     */
    public function updateParticipantPositions(int $affiliateLevels = 3)
    {
        for ($i = 0; $i < count($this); $i++) {
            if (isset($this[$i])) ParticipantPositionUpdater::updateParticipantPosition($this->getRelevantParticipantInfo($i, $affiliateLevels));
        }
    }

    /** Returns relevant participant information, its parent, affiliates.
     * @param int $id
     * @param int $affiliateLevels
     * @return array [
     *  'parent' => Participant,
     *  'participant' => Participant,
     *  'affiliates' => [
     *  Participant[], Participant[], ..., Participant[]]]
     */
    private function getRelevantParticipantInfo(int $id, int $affiliateLevels = 3)
    {
        $participantRelevantInfo = [
            'parent' => null,
            'participant' => $this[$id]['participant'],
            'affiliates' => null
        ];

        if (isset($this[$id]['parentID']) && isset($this[$this[$id]['parentID']])) {
            $participantRelevantInfo['parent'] = $this[$this[$id]['parentID']]['participant'];
        }

        if (isset($this[$id]['affiliateID'])) {
            $participantRelevantInfo['affiliates'] = $this->getRelevantAffiliates($this[$id]['affiliateID'], $affiliateLevels);
        }

        return $participantRelevantInfo;
    }

    /** Returns participant's affiliate information.
     * @param array $affiliateIDs
     * @param int $affiliateLevel
     * @return array
     */
    private function getRelevantAffiliates(array $affiliateIDs, int $affiliateLevel = 3)
    {
        $affiliates = [];
        for ($i = 0; $i < $affiliateLevel; $i++) {
            if (count($affiliateIDs) == 0) break;
            $affiliatesAffiliateIDs = [];
            $currAffiliatesCount = count($affiliates);
            foreach ($affiliateIDs as $affiliateID) {
                if (isset($this[$affiliateID])) {
                    $affiliates[$currAffiliatesCount][] = $this[$affiliateID]['participant'];
                }
                if (isset($this[$affiliateID]['affiliateID'])) {
                    $affiliatesAffiliateIDs = array_merge($affiliatesAffiliateIDs, $this[$affiliateID]['affiliateID']);
                }
            }
            $affiliateIDs = $affiliatesAffiliateIDs;
        }
        return $affiliates;
    }

    /** Returns an associative array of participants and their relations to each other.
     * @return array
     */
    public function getAllAsDbArray()
    {
        $returnArray = [];
        for ($i = 0; $i < count($this); $i++) {
            if (isset($this[$i])) {
                $fetchedParticipantInfo = $this[$i]['participant']->get_db_values();
                $returnArray[] = [
                    'entity_id' => $i + 1,
                    'firstname' => $fetchedParticipantInfo['firstname'],
                    'lastname' => $fetchedParticipantInfo['lastname'],
                    'mailto' => $fetchedParticipantInfo['mailto'],
                    'position' => $fetchedParticipantInfo['position'],
                    'shares_amount' => $fetchedParticipantInfo['shares_amount'],
                    'start_date' => $fetchedParticipantInfo['start_date'],
                    'parent_id' => isset($this[$i]['parentID']) ? $this[$i]['parentID'] + 1 : 0
                ];
            }
        }
        //var_dump($returnArray);
        return $returnArray;
    }

    /** Returns an array of IDs of connectable participants
     * @return int[]|string[]
     */
    public function getConnectableKeys()
    {
        return array_keys($this->affiliateArray);
    }

    /** returns participant's information as an associative array with following values:
     * - 'parentID' => ID of parent participant
     * - 'participant' => a Participant Model instance
     * - 'affiliateID' => IDs of participant's affiliates
     * @param int $id
     * @return mixed
     */
    public function getParticipantInfo(int $id)
    {
        return $this[$id];
    }

}