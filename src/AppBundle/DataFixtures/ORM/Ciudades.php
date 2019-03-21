<?php
/**
 * Created by PhpStorm.
 * User: rcarranza
 * Date: 3/21/2019
 * Time: 3:41 PM
 */

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Ciudad;

class Ciudades implements FixtureInterface
{

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        // TODO: Implement load() method.
        $ciudades = array(
            array('nombre' => 'El Salvador', 'slug' => 'el-salvador'),
            array('nombre' => 'Madrid', 'slug' => 'madrid'),
            array('nombre' => 'Barcelona', 'slug' => 'barcelona'),
        );

        foreach ($ciudades as $ciudad) {
            $entidad = new Ciudad();
            $entidad->setNombre($ciudad['nombre']);
            $entidad->setSlug($ciudad['slug']);
            $manager->persist($entidad);
        }
        $manager->flush();
    }
}