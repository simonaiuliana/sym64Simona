<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Article;
use App\Entity\Section;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Cocur\Slugify\Slugify;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();
        $slugify = new Slugify();

        // Create admin user
        $admin = new User();
        $admin->setUsername('admin');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'admin'));
        $admin->setEmail('admin@example.com');
        $admin->setFullname('Admin User');
        $manager->persist($admin);

        // Array to store users
        $users = [];
        
        // Create additional users
        for ($i = 1; $i <= 5; $i++) {
            $user = new User();
            $user->setUsername("redac$i");
            $user->setRoles(['ROLE_REDAC']);
            $user->setPassword($this->passwordHasher->hashPassword($user, "redac$i"));
            $user->setEmail($faker->email);
            $user->setFullname($faker->name);
            $manager->persist($user);
            $users[] = $user; // Add each user to the $users array
        }

        // Create sections
        $sections = [];
        for ($i = 0; $i < 6; $i++) {
            $section = new Section();
            $sectionTitle = $faker->word;
            $section->setSectionTitle($sectionTitle);
            $section->setSectionSlug($slugify->slugify($sectionTitle));
            $section->setSectionDetail($faker->sentence);
            $sections[] = $section;
            $manager->persist($section);
        }

        // Create articles
        for ($i = 0; $i < 160; $i++) {
            $article = new Article();
            $title = $faker->sentence;
            $article->setTitle($title);
            $article->setTitleSlug($slugify->slugify($title));
            $article->setText($faker->paragraphs(3, true));
            $article->setArticleDataCreate($faker->dateTimeBetween('-6 months', 'now'));
            $article->setPublished($faker->boolean(75)); // 75% chance of being published
            $article->setUserId($faker->randomElement($users)); // Assign a random user
            $manager->persist($article);
        }

        $manager->flush();
    }
}
