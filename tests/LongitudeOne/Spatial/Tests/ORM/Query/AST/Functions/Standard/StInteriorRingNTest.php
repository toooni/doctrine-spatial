<?php
/**
 * This file is part of the doctrine spatial extension.
 *
 * PHP 7.4 | 8.0
 *
 * (c) Alexandre Tranchant <alexandre.tranchant@gmail.com> 2017 - 2021
 * (c) Longitude One 2020 - 2021
 * (c) 2015 Derek J. Lambert
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

namespace LongitudeOne\Spatial\Tests\ORM\Query\AST\Functions\Standard;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\ORMException;
use LongitudeOne\Spatial\Exception\InvalidValueException;
use LongitudeOne\Spatial\Exception\UnsupportedPlatformException;
use LongitudeOne\Spatial\Tests\Helper\PolygonHelperTrait;
use LongitudeOne\Spatial\Tests\OrmTestCase;

/**
 * ST_InteriorRingN DQL function tests.
 *
 * @author  Alexandre Tranchant <alexandre-tranchant@gmail.com>
 * @license https://alexandre-tranchant.mit-license.org MIT
 *
 * @group dql
 *
 * @internal
 * @coversDefaultClass
 */
class StInteriorRingNTest extends OrmTestCase
{
    use PolygonHelperTrait;

    /**
     * Setup the function type test.
     *
     * @throws Exception                    when connection failed
     * @throws ORMException                 when cache is not set
     * @throws UnsupportedPlatformException when platform is unsupported
     */
    protected function setUp(): void
    {
        $this->usesEntity(self::POLYGON_ENTITY);
        $this->supportsPlatform('postgresql');
        $this->supportsPlatform('mysql');

        parent::setUp();
    }

    /**
     * Test a DQL containing function to test in the select.
     *
     * @throws Exception                    when connection failed
     * @throws ORMException                 when cache is not set
     * @throws UnsupportedPlatformException when platform is unsupported
     * @throws InvalidValueException        when geometries are not valid
     *
     * @group geometry
     */
    public function testSelectStInteriorRingN()
    {
        $bigPolygon = $this->createBigPolygon();
        $smallPolygon = $this->createSmallPolygon();
        $holeyPolygon = $this->createHoleyPolygon();
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        $query = $this->getEntityManager()->createQuery(
            // phpcs:disable Generic.Files.LineLength.MaxExceeded
            'SELECT p, ST_AsText(ST_InteriorRingN(p.polygon, :p)) FROM LongitudeOne\Spatial\Tests\Fixtures\PolygonEntity p'
            // phpcs:enable
        );
        $query->setParameter('p', 1);
        $result = $query->getResult();

        static::assertCount(3, $result);
        static::assertEquals($bigPolygon, $result[0][0]);
        static::assertNull($result[0][1]);
        static::assertEquals($smallPolygon, $result[1][0]);
        static::assertNull($result[1][1]);
        static::assertEquals($holeyPolygon, $result[2][0]);
        static::assertEquals('LINESTRING(5 5,7 5,7 7,5 7,5 5)', $result[2][1]);
    }
}
