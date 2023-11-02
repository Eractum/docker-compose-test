<?php
require_once __DIR__.'/../model_participant.php';

/**
 * ParticipantFactory creates Participant Model instances.
 */
class ParticipantFactory
{
    static public function createRandomParticipant(Participant $parent = null)
    {
        $faker = Faker\Factory::create();

        $firstName = $faker->firstName;
        $lastName = $faker->lastName;
        $mailTo = 'email:' . $faker->email;
        $shares = rand(0, 500);

        if ($parent !== null) {
            $startDate = rand($parent->getStartDate(), time() - 86400);
        } else {
            $startDate = rand(0, time() - 86400);
        }

        return new Participant(
            $firstName,
            $lastName,
            $mailTo,
            'novice',
            $shares,
            $startDate);
    }

    static public function createParticipant(
        string $firstName,
        string $lastName,
        string $mailTo,
        string $position,
        int    $shares,
        int    $startDate)
    {
        return new Participant(
            $firstName,
            $lastName,
            $mailTo,
            $position,
            $shares,
            $startDate);
    }
}