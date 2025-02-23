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
use LongitudeOne\Spatial\Tests\Helper\GeometryHelperTrait;
use LongitudeOne\Spatial\Tests\OrmTestCase;

/**
 * ST_LineStringFromWKB DQL function tests.
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license https://alexandre-tranchant.mit-license.org MIT
 *
 * @group dql
 *
 * @internal
 * @coversDefaultClass
 */
class StLineStringFromWkbTest extends OrmTestCase
{
    use GeometryHelperTrait;

    /**
     * Setup the function type test.
     *
     * @throws Exception                    when connection failed
     * @throws ORMException                 when cache is not set
     * @throws UnsupportedPlatformException when platform is unsupported
     */
    protected function setUp(): void
    {
        $this->usesEntity(self::GEOMETRY_ENTITY);
        $this->supportsPlatform('postgresql');
        $this->supportsPlatform('mysql');

        parent::setUp();
    }

    /**
     * Test a DQL containing function to test in the select.
     *
     * @throws InvalidValueException        when linestring is an invalid geometry
     * @throws Exception                    when connection failed
     * @throws ORMException                 when cache is not set
     * @throws UnsupportedPlatformException when platform is unsupported
     *
     * @group geometry
     */
    public function testSelect()
    {
        $this->createStraightLineString();
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        // phpcs:disable Generic.Files.LineLength.MaxExceeded
        $query = $this->getEntityManager()->createQuery(
            'SELECT g, St_AsText(ST_LineStringFromWkb(St_AsBinary(g.geometry))) FROM LongitudeOne\Spatial\Tests\Fixtures\GeometryEntity g'
        );
        // phpcs:enable

        $result = $query->getResult();

        static::assertCount(1, $result);
        static::assertEquals('LINESTRING(1 1,2 2,5 5)', $result[0][1]);
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
    public function testSelectWithSrid()
    {
        $this->createStraightLineString();
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        // phpcs:disable Generic.Files.LineLength.MaxExceeded
        $query = $this->getEntityManager()->createQuery(
            'SELECT g, ST_SRID(ST_LineStringFromWkb(:wkb, :srid)) FROM LongitudeOne\Spatial\Tests\Fixtures\GeometryEntity g'
        );
        $query->setParameter('wkb', hex2bin('010200000003000000000000000000000000000000000000000000000000000040000000000000004000000000000014400000000000001440'), 'blob');
        $query->setParameter('srid', 2154);
        // phpcs:enable

        $result = $query->getResult();

        static::assertCount(1, $result);
        static::assertEquals(2154, $result[0][1]);
    }
}
