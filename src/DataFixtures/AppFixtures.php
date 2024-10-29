<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\Article;
use App\Entity\Section;
use Cocur\Slugify\Slugify;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

use Faker\Factory AS Faker;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $userPasswordHasher){
        $this->hasher = $userPasswordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $slugify = new Slugify();
        $faker = Faker::create('fr-FR');
        // users
        // admin
        $user = new User();
        $user->setUsername("admin");
        $user->setRoles(["ROLE_ADMIN"]);
        $user->setFullname($faker->name());
        $user->setUniqid(uniqid());
        $user->setEmail($faker->email());
        $user->setActivate(random_int(0, 3) == 3);
        $user->setPassword($this->hasher->hashPassword($user, 'admin'));
        $users[] = $user;
        $creators[] = $user;

        $manager->persist($user);

        // users
        for($i=1;$i<=24;$i++){
            $user = new User();
            $user->setUsername("user$i");
            $user->setRoles(["ROLE_USER"]);
            $user->setFullname($faker->name());
            $user->setUniqid(uniqid());
            $user->setEmail($faker->email());
            $user->setActivate(random_int(0, 3) == 3);
            $user->setPassword($this->hasher->hashPassword($user, "user$i"));
            $users[] = $user;

            $manager->persist($user);
        }

        // redators
        for($i=1;$i<=24;$i++){
            $user = new User();
            $user->setUsername("redac$i");
            $user->setRoles(["ROLE_REDAC"]);
            $user->setFullname($faker->name());
            $user->setUniqid(uniqid());
            $user->setEmail($faker->email());
            $user->setActivate(random_int(0, 3) == 3);
            $user->setPassword($this->hasher->hashPassword($user, "redac$i"));
            $users[] = $user;
            $creators[] = $user;

            $manager->persist($user);
        }

        // articles
        for ($i=1;$i<=160;$i++){
            $article = new Article();
            $rand_user = $creators[array_rand($creators)];

            $article->setUser($rand_user);
            $article->setTitle(ucfirst($faker->words(mt_rand(2,5),true)));
            $article->setTitleSlug($slugify->slugify($article->getTitle()));
            $article->setText($faker->paragraphs(mt_rand(3,6), true));
            $article->setArticleDateCreate(new \dateTime('now - 6 months'));
            $article->setPublished(rand(0, 3) < 3);
            if ($article->getPublished()){
                $article->setArticleDatePosted($faker->dateTimeBetween($article->getArticleDateCreate(), "now"));
            }

            $articles[] = $article;

            $manager->persist($article);
        }

        // sections
        for ($i=1;$i<=6;$i++){
            $section = new Section();

            $title = ucfirst($faker->words(rand(1, 2),true));
            $section->setSectionTitle($title);
            $section->setSectionDetail($faker->paragraphs(1,true));
            $section->setSectionSlug($slugify->slugify($title));
            $past_articles = [];
            $number = rand(2, 40);
            for ($j=0;$j <= $number;$j++){
                $random_article = rand(0, sizeof($articles) - 1);
                while (in_array($random_article, $past_articles)){
                    $random_article = rand(0, sizeof($articles) - 1);
                }
                $past_articles[] = $random_article;
                $section->addArticle($articles[$random_article]);
            }
            $sections[] = $section;
            $manager->persist($section);
        }

        /*
        $published_articles = array_values(array_filter($articles, fn(Article $article)=>$article->isArticlePublished()));

        // comments
        for ($i=1;$i<=sizeof($published_articles)*5;$i++){
            $comment = new Comment();
            $rand_user = $users[array_rand($users)];
            $rand_article = $published_articles[array_rand($published_articles)];

            $comment->setUser($rand_user);
            $comment->setArticle($rand_article);
            $comment->setCommentPublished(true);
            $comment->setCommentMessage($faker->paragraphs(1, true));
            $comment->setCommentDateCreated(new \dateTime('now - ' . mt_rand(0, 4) . ' days'));
            $manager->persist($comment);
        }
        */
        $manager->flush();
    }
}