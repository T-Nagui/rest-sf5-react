<?php
declare(strict_types=1);
/*
 * This file is part of a intract project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Tests\Funct;

use App\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProductControllerTest extends WebTestCase
{
    public function testGetCategoryList(): void
    {
        $client = static::createClient([], [
            'HTTP_HOST' => 'localhost:8080',
        ]);

        $client->request('GET', '/api/category/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
    public function testAddCategory(): void
    {
        $client = static::createClient([], [
            'HTTP_HOST' => 'localhost:8080',
        ]);



        $client->request('POST', '/api/category/new', json_encode([
            [
                'name' => 'test',
            ]
        ]));
        $res = [
            'id' => 1,
            'name' => 'test'
        ];
        dd($client->getResponse());

    }
}
