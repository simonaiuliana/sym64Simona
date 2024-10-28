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
        $admin->setUsername('admin'); // or uniqid('admin_')
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'admin'));
        $admin->setEmail('admin@example.com');
        $admin->setFullname('Admin User');
        $manager->persist($admin);
        $users[] = $admin; // Array to hold users

        // Create redactor users
        for ($i = 1; $i <= 5; $i++) {
            $user = new User();
            $username = uniqid("redac_$i"); // Ensure unique username
            $user->setUsername($username);
            $user->setRoles(['ROLE_REDAC']);
            $user->setPassword($this->passwordHasher->hashPassword($user, "redac$i"));
            $user->setEmail($faker->unique()->email);
            $user->setFullname($faker->name);
            $user->setActivate(true); // Active by default
            $manager->persist($user);
            $users[] = $user; // Store in users array
        }

        // Create regular users (24 total, 3 active out of 4)
        for ($i = 1; $i <= 24; $i++) {
            $user = new User();
            $username = uniqid("user_$i"); // Ensure unique username
            $user->setUsername($username);
            $user->setRoles(['ROLE_USER']);
            $user->setPassword($this->passwordHasher->hashPassword($user, "user$i"));
            $user->setEmail($faker->unique()->email);
            $user->setFullname($faker->name);
            $user->setActivate($i % 4 !== 0); // 3 out of 4 users are active
            $manager->persist($user);
            $users[] = $user; // Store in users array
        }

        // Create sections
        $sections = [];
        for ($i = 0; $i < 6; $i++) {
            $section = new Section();
            $sectionTitle = $faker->sentence(2);
            $section->setSectionTitle($sectionTitle);
            $section->setSectionSlug($slugify->slugify($sectionTitle));
            $section->setSectionDetail($faker->paragraph);
            $sections[] = $section; // Store for later
            $manager->persist($section);
        }

        // Create articles
        $articles = []; // Array to store articles
        for ($i = 0; $i < 160; $i++) {
            $article = new Article();
            $title = $faker->sentence(6);
            $article->setTitle($title);
            $article->setTitleSlug($slugify->slugify($title));
            $article->setText($faker->paragraphs(3, true));
            $article->setArticleDateCreate($faker->dateTimeBetween('-6 months', 'now'));
            $article->setPublished($faker->boolean(75)); // 75% chance of being published
            
            // Randomly select an author from Admin and Redactor users
            $authors = array_merge([$admin], array_slice($users, 1, 5)); // Only REDAC users
            $article->setUserId($faker->randomElement($authors));
            $manager->persist($article);
            $articles[] = $article; // Store articles for later assignment
        }

        // Assign articles to sections
        foreach ($sections as $section) {
            $numberOfArticles = rand(2, 40); // Randomly assign between 2 and 40 articles
            for ($j = 0; $j < $numberOfArticles; $j++) {
                if (!empty($articles)) {
                    $randomArticle = $faker->randomElement($articles);
                    $section->addArticle($randomArticle); // Assuming addArticle method exists
                }
            }
        }

        $manager->flush();
    }
}
