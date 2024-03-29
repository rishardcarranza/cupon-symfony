<?php
/**
 * Created by PhpStorm.
 * User: rcarranza
 * Date: 3/22/2019
 * Time: 7:56 AM
 */

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Oferta;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
/**
 * Crea los datos de prueba para la entidad Oferta.
 */
class Ofertas extends AbstractFixture implements OrderedFixtureInterface
{
    const NUM_OFERTAS = 15;
    public function getOrder()
    {
        return 30;
    }
    public function load(ObjectManager $manager)
    {
        // Obtener todas las tiendas y ciudades de la base de datos
        $ciudades = $manager->getRepository('AppBundle:Ciudad')->findAll();
        foreach ($ciudades as $ciudad) {
            $tiendas = $manager->getRepository('AppBundle:Tienda')->findByCiudad(
                $ciudad->getId()
            );
            for ($j = 1; $j <= self::NUM_OFERTAS; ++$j) {
                $oferta = new Oferta();
                $oferta->setNombre($this->getNombre());
                $oferta->setDescripcion($this->getDescripcion());
                $oferta->setCondiciones($this->getCondiciones());
                $oferta->setRutaFoto('foto'.mt_rand(1, 20).'.jpg');
                $oferta->setPrecio(number_format(mt_rand(100, 10000) / 100, 2));
                $oferta->setDescuento($oferta->getPrecio() * (mt_rand(10, 70) / 100));
                // Una oferta se publica hoy, el resto se reparte entre el pasado y el futuro
                if (1 === $j) {
                    $fecha = 'today';
                    $oferta->setRevisada(true);
                } elseif ($j < 10) {
                    $fecha = 'now - '.($j - 1).' days';
                    // el 80% de las ofertas pasadas se marcan como revisadas
                    $oferta->setRevisada((mt_rand(1, 1000) % 10) < 8);
                } else {
                    $fecha = 'now + '.($j - 10 + 1).' days';
                    $oferta->setRevisada(true);
                }
                $fechaPublicacion = new \DateTime($fecha);
                $fechaPublicacion->setTime(23, 59, 59);
                // Se debe clonar el valor de la fechaPublicacion porque si se usa directamente
                // el método ->add(), se modificaría el valor original, que no se guarda en la BD
                // hasta que se hace el ->flush()
                $fechaExpiracion = clone $fechaPublicacion;
                $fechaExpiracion->add(\DateInterval::createFromDateString('24 hours'));
                $oferta->setFechaPublicacion($fechaPublicacion);
                $oferta->setFechaExpiracion($fechaExpiracion);
                $oferta->setUmbral(mt_rand(25, 100));
                $oferta->setCiudad($ciudad);
                // Seleccionar aleatoriamente una tienda que pertenezca a la ciudad anterior
                $tienda = $tiendas[array_rand($tiendas)];
                $oferta->setTienda($tienda);
                $manager->persist($oferta);
                $manager->flush();
            }
        }
    }
    /**
     * Generador aleatorio de nombres de ofertas.
     *
     * @return string Nombre/título aletorio generado para la oferta.
     */
    private function getNombre()
    {
        $palabras = array_flip(array(
            'Lorem', 'Ipsum', 'Sitamet', 'Et', 'At', 'Sed', 'Aut', 'Vel', 'Ut',
            'Dum', 'Tincidunt', 'Facilisis', 'Nulla', 'Scelerisque', 'Blandit',
            'Ligula', 'Eget', 'Drerit', 'Malesuada', 'Enimsit', 'Libero',
            'Penatibus', 'Imperdiet', 'Pendisse', 'Vulputae', 'Natoque',
            'Aliquam', 'Dapibus', 'Lacinia',
        ));
        $numeroPalabras = mt_rand(4, 8);
        return implode(' ', array_rand($palabras, $numeroPalabras));
    }
    /**
     * Generador aleatorio de descripciones de ofertas.
     *
     * @return string Descripción aletoria generada para la oferta.
     */
    private function getDescripcion()
    {
        $frases = array_flip(array(
            'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
            'Mauris ultricies nunc nec sapien tincidunt facilisis.',
            'Nulla scelerisque blandit ligula eget hendrerit.',
            'Sed malesuada, enim sit amet ultricies semper, elit leo lacinia massa, in tempus nisl ipsum quis libero.',
            'Aliquam molestie neque non augue molestie bibendum.',
            'Pellentesque ultricies erat ac lorem pharetra vulputate.',
            'Donec dapibus blandit odio, in auctor turpis commodo ut.',
            'Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.',
            'Nam rhoncus lorem sed libero hendrerit accumsan.',
            'Maecenas non erat eu justo rutrum condimentum.',
            'Suspendisse leo tortor, tempus in lacinia sit amet, varius eu urna.',
            'Phasellus eu leo tellus, et accumsan libero.',
            'Pellentesque fringilla ipsum nec justo tempus elementum.',
            'Aliquam dapibus metus aliquam ante lacinia blandit.',
            'Donec ornare lacus vitae dolor imperdiet vitae ultricies nibh congue.',
        ));
        $numeroFrases = mt_rand(4, 7);
        return implode("\n", array_rand($frases, $numeroFrases));
    }
    /**
     * Generador aleatorio de condiciones de ofertas.
     *
     * @return string Condiciones aletorias generadas para la oferta.
     */
    private function getCondiciones()
    {
        $frases = array_flip(array(
            'Máximo 1 consumición por persona.',
            'No acumulable a otras ofertas.',
            'No disponible para llevar. Debe consumirse en el propio local.',
            'Válido para cualquier día entre semana.',
            'No válido en festivos ni fines de semana.',
            'Reservado el derecho de admisión.',
            'Oferta válida si se realizan consumiciones adicionales por valor de 50 euros.',
            'Válido solamente para comidas, no para cenas.',
        ));
        $numeroFrases = mt_rand(2, 4);
        return implode(' ', array_rand($frases, $numeroFrases));
    }
}