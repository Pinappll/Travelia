<?php

namespace App\DataFixtures;

use App\Entity\Activity;
use App\Entity\Comment;
use App\Entity\Country;
use App\Entity\Trip;
use App\Entity\User;
use App\Enum\UserTypeEnum;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class AppFixtures extends Fixture
{
    public const MAX_USERS = 10;
    public const MAX_TRIPS = 10;
    public const MAX_COMMENTS = 10;
    public const MAX_ACTIVITIES = 10;

    private HttpClientInterface $httpClient;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(HttpClientInterface $httpClient, UserPasswordHasherInterface $passwordHasher)
    {
        $this->httpClient = $httpClient;
        $this->passwordHasher = $passwordHasher;
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function load(ObjectManager $manager): void
    {

        $users = [];
        $country = [];
        $trip = [];
        $comment = [];
        $activity = [];

        $this->createUsers($manager, $users);
        $this->createCountries($manager, $country);
        $this->createTrips($manager, $trip, $country);
        $this->createComments($manager, $comment, $users, $trip);
        $this->createActivities($manager, $activity, $trip);


        $manager->flush();
    }

    /**
     * @param ObjectManager $manager
     * @param array $users
     */
    protected  function createUsers(ObjectManager $manager, array &$users): void
    {
        $faker = Factory::create();

        for ($i = 0; $i < self::MAX_USERS; $i++) {
            $user = new User();
            $user->setFirstname($faker->firstName);
            $user->setLastname($faker->lastName);
            $user->setEmail($user->getFirstname() . '.' . $user->getLastname() . '@mail.fr');
            $plainPassword = 'password';
            $hashedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);
            $user->setPassword($hashedPassword);
            $user->setRoles(UserTypeEnum::USER);
            $manager->persist($user);
            $users[] = $user;
        }

        /// Admin
        $admin = new User();
        $admin->setFirstname('Admin');
        $admin->setLastname('Admin');
        $admin->setEmail('admin@mail.fr');

        $plainPassword = 'admin';
        $hashedPassword = $this->passwordHasher->hashPassword($admin, $plainPassword);
        $admin->setPassword($hashedPassword);

        $admin->setRoles(UserTypeEnum::ADMIN);
        $manager->persist($admin);

        // Normal User
        $normal = new User();
        $normal->setFirstname('User');
        $normal->setLastname('User');
        $normal->setEmail('user@mail.fr');
        $plainPassword = 'user';
        $hashedPassword = $this->passwordHasher->hashPassword($normal, $plainPassword);
        $normal->setPassword($hashedPassword);
        $normal->setRoles(UserTypeEnum::USER);
        $manager->persist($normal);

        // Banned
        $banned = new User();
        $banned->setFirstname('Banned');
        $banned->setLastname('Banned');
        $banned->setEmail('banned');
        $plainPassword = 'banned';
        $hashedPassword = $this->passwordHasher->hashPassword($banned, $plainPassword);
        $banned->setPassword($hashedPassword);
        $banned->setRoles(UserTypeEnum::BANNED);
        $manager->persist($banned);
    }


    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    protected function createCountries(ObjectManager $manager, array &$countries): void
    {
        $data = $this->httpClient->request('GET', 'https://flagcdn.com/fr/codes.json');

        if ($data->getStatusCode() === 200) {
            $countryData = $data->toArray();

            foreach ($countryData as $code => $name) {
                $country = new Country();
                $country->setName($name);
                $country->setCode($code);
                $country->setImage('https://flagcdn.com/h120/' . $code . '.png');

                $manager->persist($country);
                $countries[] = $country; // Ajout des objets Country au tableau
            }

            $manager->flush();
        } else {
            throw new \Exception('Erreur lors de la récupération des données depuis l\'API.');
        }
    }

    protected function createTrips(ObjectManager $manager, array &$trips, array &$countries): void
    {
        $faker = Factory::create();

        for ($i = 0; $i < self::MAX_TRIPS; $i++) {
            $trip = new Trip();
            $trip->setTitle($faker->sentence(3));
            $trip->setDescription($faker->text(200));
            $trip->setPirce($faker->numberBetween(100, 1000));
            $trip->setCountry($countries[array_rand($countries)]); // Utilise un objet Country
            $trip->setImage('https://picsum.photos/id/'.$i.'/200/300');
            $trip->setStartDate(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-6 months')));
            $trip->setEndDate(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-6 months')));

            $manager->persist($trip);
            $trips[] = $trip;
        }
    }

    private function createComments(ObjectManager $manager, array $comment, array $users, array &$trip): void
    {
        $faker = Factory::create();

        for ($i = 0; $i < self::MAX_COMMENTS; $i++) {
            $comment = new Comment();
            $comment->setContent($faker->text(200));
            $comment->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-6 months')));
            $comment->setPublisher($users[array_rand($users)]);
            $comment->setTrip($trip[array_rand($trip)]);

            $manager->persist($comment);
        }
    }

    private function createActivities(ObjectManager $manager, array &$activities, array &$trip): void
    {
        $faker = Factory::create();
        $activityType = ['Sport', 'Culture', 'Détente', 'Aventure'];
        for ($i = 0; $i < self::MAX_ACTIVITIES; $i++) {
            $activity = new Activity();
            $activity->setName($faker->sentence(3));
            $activity->setDescription($faker->text(200));
            $activity->setDuration($faker->numberBetween(1, 10));
            $activity->setActivityType($faker->randomElement($activityType));
            $activity->setImage('https://picsum.photos/id/'.$i.'/200/300');
            $activity->setPrice($faker->numberBetween(10, 100));
            $activity->setTrip($trip[array_rand($trip)]);

            $manager->persist($activity);
            $activities[] = $activity;
        }
    }
}
