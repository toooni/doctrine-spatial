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

namespace LongitudeOne\Spatial\Tests\ORM\Query\AST\Functions\PostgreSql;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\ORMException;
use LongitudeOne\Spatial\Exception\InvalidValueException;
use LongitudeOne\Spatial\Exception\UnsupportedPlatformException;
use LongitudeOne\Spatial\Tests\Helper\PointHelperTrait;
use LongitudeOne\Spatial\Tests\OrmTestCase;

/**
 * ST_DWithin DQL function tests.
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license https://alexandre-tranchant.mit-license.org MIT
 *
 * @group dql
 * @group pgsql-only
 *
 * @internal
 * @coversDefaultClass
 */
class SpDWithinTest extends OrmTestCase
{
    use PointHelperTrait;

    /**
     * Setup the function type test.
     *
     * @throws Exception                    when connection failed
     * @throws ORMException                 when cache is not set
     * @throws UnsupportedPlatformException when platform is unsupported
     */
    protected function setUp(): void
    {
        $this->usesEntity(self::POINT_ENTITY);
        $this->usesEntity(self::GEOGRAPHY_ENTITY);
        $this->supportsPlatform('postgresql');

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
    public function testSelectGeography()
    {
        $newYork = $this->createNewYorkGeography();
        $losAngeles = $this->createLosAngelesGeography();
        $dallas = $this->createDallasGeography();
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        $query = $this->getEntityManager()->createQuery(
            // phpcs:disable Generic.Files.LineLength.MaxExceeded
            'SELECT g, PgSql_DWithin(g.geography, PgSql_GeographyFromText(:p), :d, :spheroid) FROM LongitudeOne\Spatial\Tests\Fixtures\GeographyEntity g'
            // phpcs:enable
        );

        $query->setParameter('p', 'POINT(-89.4 43.066667)', 'string');
        $query->setParameter('d', 2000000.0); //2.000.000m=2.000km
        $query->setParameter('spheroid', true, 'boolean');

        $result = $query->getResult();

        static::assertCount(3, $result);
        static::assertEquals($newYork, $result[0][0]);
        static::assertTrue($result[0][1]);
        static::assertEquals($losAngeles, $result[1][0]);
        static::assertFalse($result[1][1]);
        static::assertEquals($dallas, $result[2][0]);
        static::assertTrue($result[2][1]);
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
    public function testSelectGeometry()
    {
        $newYork = $this->createNewYorkGeometry();
        $losAngeles = $this->createLosAngelesGeometry();
        $dallas = $this->createDallasGeometry();
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        $query = $this->getEntityManager()->createQuery(
            // phpcs:disable Generic.Files.LineLength.MaxExceeded
            'SELECT p, PgSql_DWithin(p.point, ST_GeomFromText(:p), :d) FROM LongitudeOne\Spatial\Tests\Fixtures\PointEntity p'
            // phpcs:enable
        );

        $query->setParameter('p', 'POINT(-89.4 43.066667)', 'string');
        $query->setParameter('d', 20.0);

        $result = $query->getResult();

        static::assertCount(3, $result);
        static::assertEquals($newYork, $result[0][0]);
        static::assertTrue($result[0][1]);
        static::assertEquals($losAngeles, $result[1][0]);
        static::assertFalse($result[1][1]);
        static::assertEquals($dallas, $result[2][0]);
        static::assertTrue($result[2][1]);
    }
}
