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
use LongitudeOne\Spatial\PHP\Types\Geometry\Point;
use LongitudeOne\Spatial\Tests\Fixtures\PointEntity;
use LongitudeOne\Spatial\Tests\Helper\PointHelperTrait;
use LongitudeOne\Spatial\Tests\OrmTestCase;

/**
 * ST_SnapToGrid DQL function tests.
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license https://dlambert.mit-license.org MIT
 *
 * @group dql
 * @group pgsql-only
 *
 * @internal
 * @coversDefaultClass
 */
class SpSnapToGridTest extends OrmTestCase
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
        $this->supportsPlatform('postgresql');

        parent::setUp();
    }

    /**
     * Test a DQL containing function with 2 parameters to test in the select.
     *
     * @throws Exception                    when connection failed
     * @throws ORMException                 when cache is not set
     * @throws UnsupportedPlatformException when platform is unsupported
     * @throws InvalidValueException        when geometries are not valid
     *
     * @group geometry
     */
    public function testSelectStSnapToGridSignature2Parameters()
    {
        $entity = new PointEntity();
        $entity->setPoint(new Point(1.25, 2.55));
        $this->getEntityManager()->persist($entity);

        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        $query = $this->getEntityManager()->createQuery(
            'SELECT ST_AsText(PgSql_SnapToGrid(p.point, 0.5)) FROM LongitudeOne\Spatial\Tests\Fixtures\PointEntity p'
        );
        $result = $query->getResult();

        $expected = [
            [1 => 'POINT(1 2.5)'],
        ];

        static::assertEquals($expected, $result);
    }

    /**
     * Test a DQL containing function with three parameters to test in the select.
     *
     * @throws Exception                    when connection failed
     * @throws ORMException                 when cache is not set
     * @throws UnsupportedPlatformException when platform is unsupported
     * @throws InvalidValueException        when geometries are not valid
     *
     * @group geometry
     */
    public function testSelectStSnapToGridSignature3Parameters()
    {
        $entity = new PointEntity();
        $entity->setPoint(new Point(1.25, 2.55));
        $this->getEntityManager()->persist($entity);

        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        $query = $this->getEntityManager()->createQuery(
            // phpcs:disable Generic.Files.LineLength.MaxExceeded
            'SELECT ST_AsText(PgSql_SnapToGrid(p.point, 0.5, 1)) FROM LongitudeOne\Spatial\Tests\Fixtures\PointEntity p'
            // phpcs:enable
        );
        $result = $query->getResult();

        $expected = [
            [1 => 'POINT(1 3)'],
        ];

        static::assertEquals($expected, $result);
    }

    /**
     * Test a DQL containing function with five parameters to test in the select.
     *
     * @throws Exception                    when connection failed
     * @throws ORMException                 when cache is not set
     * @throws UnsupportedPlatformException when platform is unsupported
     * @throws InvalidValueException        when geometries are not valid
     *
     * @group geometry
     */
    public function testSelectStSnapToGridSignature5Parameters()
    {
        $entity = new PointEntity();
        $entity->setPoint(new Point(5.25, 6.55));
        $this->getEntityManager()->persist($entity);

        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        $query = $this->getEntityManager()->createQuery(
        // phpcs:disable Generic.Files.LineLength.MaxExceeded
            'SELECT ST_AsText(PgSql_SnapToGrid(p.point, 5.55, 6.25, 0.5, 0.5)) FROM LongitudeOne\Spatial\Tests\Fixtures\PointEntity p'
        // phpcs:enable
        );
        $result = $query->getResult();

        $expected = [
            [1 => 'POINT(5.05 6.75)'],
        ];

        static::assertEquals($expected, $result);
    }

    /**
     * Test a DQL containing function with six paramters to test in the select.
     *
     * @throws Exception                    when connection failed
     * @throws ORMException                 when cache is not set
     * @throws UnsupportedPlatformException when platform is unsupported
     * @throws InvalidValueException        when geometries are not valid
     *
     * @group geometry
     */
    public function testSelectStSnapToGridSignature6Parameters()
    {
        $entity = new PointEntity();
        $entity->setPoint(new Point(5.25, 6.55));
        $this->getEntityManager()->persist($entity);

        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        $query = $this->getEntityManager()->createQuery(
        // phpcs:disable Generic.Files.LineLength.MaxExceeded
            'SELECT ST_AsText(PgSql_SnapToGrid(p.point, p.point, 0.005, 0.025, 0.5, 0.01)) FROM LongitudeOne\Spatial\Tests\Fixtures\PointEntity p'
        // phpcs:enable
        );
        $result = $query->getResult();

        $expected = [
            [1 => 'POINT(5.25 6.55)'],
        ];

        static::assertEquals($expected, $result);
    }
}
