<?php
namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Category;
use App\Entity\Notification;
use App\Entity\User;
use App\Entity\Note;
use App\Entity\Network;
use App\Entity\Like;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker\Factory;

class AppFixtures extends Fixture
{
    private $slugger;
    private $hasher;

    public function __construct(SluggerInterface $slugger, UserPasswordHasherInterface $hasher)
    {
        $this->slugger = $slugger;
        $this->hasher = $hasher;
    }
    
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        
        // Création des catégories
        $categories = [
            'HTML' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/html5/html5-plain.svg',
            'CSS' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/css3/css3-plain.svg',
            'JavaScript' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/javascript/javascript-plain.svg',
            'PHP' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/php/php-plain.svg',
            'SQL' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/postgresql/postgresql-plain.svg',
            'JSON' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/json/json-plain.svg',
            'Python' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/python/python-plain.svg',
            'Ruby' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/ruby/ruby-plain.svg',
            'C++' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/cplusplus/cplusplus-plain.svg',
            'Go' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/go/go-wordmark.svg',
            'bash' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/bash/bash-plain.svg',
            'Markdown' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/markdown/markdown-original.svg',
            'Java' => 'https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/java/java-original-wordmark.svg',
        ];

        $categoryArray = [];

        foreach ($categories as $title => $icon) {
            $category = new Category();
            $category->setTitle($title);
            $category->setIcon($icon);
            array_push($categoryArray, $category);
            $manager->persist($category);
        }

        // 10 utilisateurs
        $users = [];
        for ($i = 0; $i < 10; $i++) {
            $username = $faker->userName();
            $usernameFinal = $this->slugger->slug($username);
            $user = new User();
            $user->setEmail($usernameFinal . '@' . $faker->freeEmailDomain);
            $user->setUsername($username);
            $user->setPassword($this->hasher->hashPassword($user, 'admin'));
            $user->setRoles(['ROLE_USER']);
            $user->setCreatedAt(new \DateTimeImmutable());
            $user->setUpdatedAt(new \DateTimeImmutable());
            $manager->persist($user);
            array_push($users, $user);
        }

        $notes = [];
        for ($j = 0; $j < 10; $j++) {
            $note = new Note();
            $note->setTitle($faker->sentence());
            $note->setSlug($this->slugger->slug($note->getTitle()));
            $note->setContent($faker->paragraph(4, true));
            $note->setPublic($faker->boolean(50));
            $note->setViews((string)$faker->numberBetween(100, 1000));
            $note->setCreator($faker->randomElement($users));
            $note->setCategory($faker->randomElement($categoryArray));
            $note->setCreatedAt(new \DateTimeImmutable());
            $note->setUpdatedAt(new \DateTimeImmutable());
            $manager->persist($note);
            $notes[] = $note;


            for ($k = 0; $k < $faker->numberBetween(1, 3); $k++) {
                $notification = new Notification();
                $notification->setTitle($faker->sentence());
                $notification->setContent($faker->paragraph());
                $notification->setType($faker->randomElement(['info', 'warning', 'error']));
                $notification->setArchived($faker->boolean(20));
                $notification->setNote($note);
                $notification->setUpdatedAt(new \DateTimeImmutable());
                $manager->persist($notification);
            }
        }

        for ($l = 0; $l < 5; $l++) {
            $network = new Network();
            $network->setName($faker->company);
            $network->setUrl($faker->url);
            $network->setCreator((string)$faker->randomElement($users)->getId());
            $manager->persist($network);
        }


        for ($m = 0; $m < 20; $m++) {
            $like = new Like();
            $like->setNote((string)$faker->randomElement($notes)->getId());
            $like->setCreator((string)$faker->randomElement($users)->getId());
            $manager->persist($like);
        }

        $manager->flush();
    }
}