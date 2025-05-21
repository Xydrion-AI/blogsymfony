<?php

namespace App\DataFixtures;

use App\Entity\Categories;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $arrayCategories = [
            1 => [ 
                "title" => "cinema",
                "slug" => "cinema",
                "parent" => [
                    1 => [
                        "title" => "horreur",
                        "slug" => "horreur"
                    ],
                    2 => [
                        "title" => "action",
                        "slug" => "action"
                    ],
                    3 => [
                        "title" => "comédie",
                        "slug" => "comédie"
                    ],
                    4 => [
                        "title" => "drame",
                        "slug" => "drame"
                    ],
                    5 => [
                        "title" => "science-fiction",
                        "slug" => "science-fiction"
                    ],
                    6 => [
                        "title" => "animation",
                        "slug" => "animation"
                    ],
                    7 => [
                        "title" => "aventure",
                        "slug" => "aventure"
                    ],
                    8 => [
                        "title" => "fantastique",
                        "slug" => "fantastique"
                    ],
                    9 => [
                        "title" => "thriller",
                        "slug" => "thriller"
                    ],
                    10 => [
                        "title" => "romance",
                        "slug" => "romance"
                    ]
                ]
            ],
            2 => [ 
                "title" => "theatre",
                "slug" => "theatre",
                "parent" => [
                    1 => [
                        "title" => "horreur",
                        "slug" => "horreur"
                    ],
                    2 => [
                        "title" => "action",
                        "slug" => "action"
                    ],
                    3 => [
                        "title" => "comédie",
                        "slug" => "comédie"
                    ],
                    4 => [
                        "title" => "drame",
                        "slug" => "drame"
                    ],
                    5 => [
                        "title" => "science-fiction",
                        "slug" => "science-fiction"
                    ],
                    6 => [
                        "title" => "animation",
                        "slug" => "animation"
                    ],
                    7 => [
                        "title" => "aventure",
                        "slug" => "aventure"
                    ],
                    8 => [
                        "title" => "fantastique",
                        "slug" => "fantastique"
                    ],
                    9 => [
                        "title" => "thriller",
                        "slug" => "thriller"
                    ],
                    10 => [
                        "title" => "romance",
                        "slug" => "romance"
                    ]
                ]
            ]
        ];
        foreach ($arrayCategories as $item){
            $arrayCategories = new Categories();
            $arrayCategories->setName($item['title']);
            $arrayCategories->setSlug($item['slug']);
            $arrayCategories->setParent(null);
            
            $manager->persist($arrayCategories);
        foreach ($item['parent'] as $key => $value) {
            $arrayParent = new Categories();
            $arrayParent->setName($value['title']);
            $arrayParent->setSlug($value['slug']);
            $arrayParent->setParent($arrayCategories);

            $manager->persist($arrayParent);
        }
        $manager->flush();
    }
}
}