<?php

namespace App\DataFixtures;

use App\Entity\Articles;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ArticleFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i=0; $i < 100 ; $i++) { 
            $article = new Articles();
            $article->setName('article' . $i);
            $article->setSlug('slug' . $i);
            $article->setContent('content' . $i);
            $manager->persist($article);
        }

        $manager->flush();
    }
}
