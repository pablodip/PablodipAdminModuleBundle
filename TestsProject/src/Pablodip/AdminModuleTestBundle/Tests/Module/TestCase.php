<?php

namespace Pablodip\AdminModuleTestBundle\Tests\Module;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class TestCase extends WebTestCase
{
    private $client;
    private $molino;

    protected function setUp()
    {
        $this->client = static::createClient();
        $this->molino = $this->registerMolino();
        $this->cleanDatabase();
    }

    public function testListAction()
    {
        $this->loadFixtures(8);

        $crawler = $this->client->request('GET', $this->getRoutePrefix());
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertSame(8, $crawler->filter('.admin-list-table tbody tr')->count());
    }

    /**
     * @dataProvider listActionSimpleSearchProvider
     */
    public function testListActionSimpleSearch($q, $nbExpected)
    {
        $this->loadFixtures(8);

        $crawler = $this->client->request('GET', $this->getRoutePrefix(), array(
            'q' => $q
        ));
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertSame($nbExpected, $crawler->filter('.admin-list-table tbody tr')->count());
    }

    public function listActionSimpleSearchProvider()
    {
        return array(
            array('*', 8),
            array('Article*', 8),
            array('*Article*', 8),
            array('*Article*', 8),
            array('Article1', 1),
        );
    }

    public function testNewAction()
    {
        $crawler = $this->client->request('GET', $this->getRoutePrefix().'/new');
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $content = $this->client->getResponse()->getContent();
        $this->assertRegExp('/form\[title\]/', $content);
        $this->assertRegExp('/form\[content\]/', $content);
    }

    public function testCreateAction()
    {
        $crawler = $this->client->request('GET', $this->getRoutePrefix().'/new');
        $form = $crawler->selectButton('Save')->form();
        $this->client->submit($form, array(
            'form[title]'   => 'foo',
            'form[content]' => 'bar'
        ));
        $this->assertTrue($this->client->getResponse()->isRedirect());

        $this->assertSame(1, $this->getNbArticles());
        $article = $this->getOneArticle();
        $this->assertSame('foo', $article->getTitle());
        $this->assertSame('bar', $article->getContent());
    }

    public function testEditAction()
    {
        $this->loadFixtures(10);

        $article = $this->getOneArticle();
        $crawler = $this->client->request('GET', sprintf('%s/%s/edit', $this->getRoutePrefix(), $article->getId()));
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $content = $this->client->getResponse()->getContent();
        $this->assertRegExp('/form\[title\]/', $content);
        $this->assertRegExp('/form\[content\]/', $content);
    }

    public function testUpdateAction()
    {
        $this->loadFixtures(10);

        $article = $this->getOneArticle();
        $crawler = $this->client->request('GET', sprintf('%s/%s/edit', $this->getRoutePrefix(), $article->getId()));
        $form = $crawler->selectButton('Save')->form();
        $this->client->submit($form, array(
            'form[title]'   => 'foo',
            'form[content]' => 'bar'
        ));
        $this->assertTrue($this->client->getResponse()->isRedirect());

        $this->assertSame(10, $this->getNbArticles());
        $this->molino->refresh($article);
        $this->assertSame('foo', $article->getTitle());
        $this->assertSame('bar', $article->getContent());
    }

    public function testDeleteAction()
    {
        $this->loadFixtures(10);

        $article = $this->getOneArticle();
        $this->client->request('DELETE', sprintf('%s/%s', $this->getRoutePrefix(), $article->getId()));
        $this->assertTrue($this->client->getResponse()->isRedirect());

        $this->assertSame(9, $this->getNbArticles());
        $this->assertNull($this->getArticle($article->getId()));
    }

    private function loadFixtures($nb)
    {
        for ($i = 0; $i < $nb; $i++) {
            $article = $this->molino->create($this->getArticleClass());
            $article->setTitle('Article'.$i);
            $article->setContent('Content'.$i);
            $this->molino->save($article);
        }
    }

    private function getOneArticle()
    {
        return $this->createSelectQuery()->one();
    }

    private function getNbArticles()
    {
        return $this->createSelectQuery()->count();
    }

    private function getArticle($id)
    {
        return $this->createSelectQuery()->filterEqual('id', $id)->one();
    }

    private function createSelectQuery()
    {
        return $this->molino->createSelectQuery($this->getArticleClass());
    }

    abstract protected function getRoutePrefix();

    abstract protected function registerMolino();

    abstract protected function getArticleClass();

    abstract protected function cleanDatabase();
}
