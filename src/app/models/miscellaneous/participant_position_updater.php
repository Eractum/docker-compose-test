<?php
require_once __DIR__.'/../model_participant.php';
/** Updates position values of a Participant instance, following these instructions:
 * 1. A participant without a parent is called a "president"
 * 2. A "president's" affiliate with the highest share amount is called a "vice president"
 * 3. A participant, whose share amount + (affiliate shares / affiliate level) is more than 1000 is called a "manager"
 */
class ParticipantPositionUpdater
{
    /** Updater function requires an array containing target's parent (if exists),
     * a target participant instance and its affiliate participants, accepts multidimentional affiliates.
     * Input array represents an associative array:
     *
     * @param array $participantRelevantInfo [
     * 'parent' => Participant,
     * 'participant' => Participant,
     * 'affiliates' => [
     * Participant[], Participant[], ..., Participant[]]]
     * @return void
     */
    public static function updateParticipantPosition(array $participantRelevantInfo)
    {
        if ($participantRelevantInfo['parent'] == null) {
            $participantRelevantInfo['participant']->setPosition('president');

            if (!isset($participantRelevantInfo['affiliates'][0])) {
                return;
            }

            $vicePresidents = [$participantRelevantInfo['affiliates'][0][0]];
            for ($i = 1; $i < count($participantRelevantInfo['affiliates'][0]); $i++) {
                $presidentAffiliate = $participantRelevantInfo['affiliates'][0][$i];
                if ($presidentAffiliate->getShares() > $vicePresidents[0]->getShares()) {
                    $vicePresidents = [$presidentAffiliate];
                } elseif ($presidentAffiliate->getShares() == $vicePresidents[0]->getShares()) {
                    $vicePresidents[] = $presidentAffiliate;
                }
            }
            foreach ($participantRelevantInfo['affiliates'][0] as $presidentAffiliate) {
                if (in_array($presidentAffiliate, $vicePresidents)) {
                    $presidentAffiliate->setPosition('vice president');
                } else {
                    $presidentAffiliate->setPosition('novice'); //could state as 'update-pending' and call update for this exact participant, but it's an excess in this context, since an updater is going to be sent to him after anyway
                }
            }

        } else {
            if ($participantRelevantInfo['participant']->getPosition() != 'vice president'
                && isset($participantRelevantInfo['affiliates'])) {
                $totalShares = $participantRelevantInfo['participant']->getShares();
                for ($i = 0; $i < count($participantRelevantInfo['affiliates']); $i++) {
                    for ($j = 0; $j < count($participantRelevantInfo['affiliates'][$i]); $j++) {
                        $totalShares += ($participantRelevantInfo['affiliates'][$i][$j]->getShares() / ($i + 1));
                    }
                }
                if ($totalShares >= 1000) {
                    $participantRelevantInfo['participant']->setPosition('manager');
                }
            }
        }
    }
}