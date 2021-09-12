<?php

namespace App\DataFixtures;

use App\Entity\User;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{

    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }
    public function load(ObjectManager $manager)
    {
                $admin = new User();
                $admin->setNom('admin');
                $admin->setPrenom('admin');
                $admin->setEmail('admin@outlook.com');
                $admin->setLieudenaissance('tunis');
                $admin->setClasse('pas de classe');
                $date = new \DateTime('@'.strtotime('now'));
                
                $admin->setDatedenaissance($date);
        $password = $this->encoder->encodePassword($admin, '123456789');
        $admin->setPassword($password);
                $admin->setRoles(array("ROLE_SUPER_ADMIN"));
                $admin->setNumerotelephone('22568988');
               
                $admin->setImage('8bdf55106b0828f01a9f3534a1c32/99f.png');
              

    
            $manager->flush();

    }
}
