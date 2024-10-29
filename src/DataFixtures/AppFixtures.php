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
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();
        $slugify = new Slugify();
        $users = [];

        // 1. Create an Admin user
        $admin = new User();
        $admin->setUsername($slugify->slugify('admin'));
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'admin'));
        $admin->setEmail('admin@example.com');
        $admin->setFullname('Admin User');
        $admin->setActivate(true);
        $manager->persist($admin);
        $users[] = $admin;

        // 2. Create Editor users
        for ($i = 1; $i <= 5; $i++) {
            $editor = new User();
            $username = $slugify->slugify("editor$i");
            $editor->setUsername($username);
            $editor->setRoles(['ROLE_EDITOR']);
            $editor->setPassword($this->passwordHasher->hashPassword($editor, "editor$i"));
            $editor->setEmail($faker->unique()->email);
            $editor->setFullname($faker->name);
            $editor->setActivate(true);
            $manager->persist($editor);
            $users[] = $editor;
        }

        // 3. Create Regular users
        for ($i = 1; $i <= 24; $i++) {
            $user = new User();
            $username = $slugify->slugify("user$i");
            $user->setUsername($username);
            $user->setRoles(['ROLE_USER']);
            $user->setPassword($this->passwordHasher->hashPassword($user, "user$i"));
            $user->setEmail($faker->unique()->email);
            $user->setFullname($faker->name);
            $user->setActivate($i % 4 !== 0); // 3 out of 4 users are active
            $manager->persist($user);
            $users[] = $user;
        }

        // 4. Create Articles
        $articles = [];
        for ($i = 0; $i < 160; $i++) {
            $article = new Article();
            $title = $faker->sentence(6);
            $article->setTitle($title);
            $article->setTitleSlug($slugify->slugify($title));
            $article->setText($faker->paragraphs(3, true));
            $article->setArticleDateCreate($faker->dateTimeBetween('-6 months', 'now'));
            
            // Set publication probability (75%)
            $isPublished = $faker->boolean(75);
            $article->setPublished($isPublished);

            // Set publication date if published
            if ($isPublished) {
                $article->setArticleDatePosted($faker->dateTimeBetween($article->getArticleDateCreate(), 'now'));
            }

            // Assign a random author from Admin and Editors
            $randomUser = $faker->randomElement(array_slice($users, 0, 6)); // select from Admin and Editors
            $article->setUser($randomUser->getId()); // get user ID
            $manager->persist($article);
            $articles[] = $article;
        }

        // 5. Create Sections
        $sections = [];
        for ($i = 0; $i < 6; $i++) {
            $section = new Section();
            $sectionTitle = $faker->sentence(2);
            $section->setSectionTitle($sectionTitle);
            $section->setSectionSlug($slugify->slugify($sectionTitle));
            $section->setSectionDetail($faker->paragraph);
            $manager->persist($section);
            $sections[] = $section;
        }

        // 6. Associate Articles with Sections
        foreach ($sections as $section) {
            $numberOfArticles = rand(2, 40);
            for ($j = 0; $j < $numberOfArticles; $j++) {
                if (!empty($articles)) {
                    $randomArticle = $faker->randomElement($articles);
                    $section->addArticle($randomArticle); // Assuming `addArticle` method exists
                    $randomArticle->setSection($section); // Associate the article with the section
                }
            }
        }

        // Save all changes to the database
        $manager->flush();
    }
}
