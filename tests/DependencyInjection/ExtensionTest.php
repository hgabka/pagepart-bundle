<?php

namespace Hgabka\PagePartBundle\Tests\DependencyInjection;

use Hgabka\PagePartBundle\DependencyInjection\Configuration;
use Hgabka\PagePartBundle\DependencyInjection\HgabkaPagePartExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ExtensionTest extends TestCase
{
    public function testDefaults(): void
    {
        $config = (new Processor())->processConfiguration(new Configuration(), []);

        $this->assertFalse($config['extended_pagepart_chooser']);
        $this->assertSame([], $config['pagetemplates']);
    }

    public function testPagePartPresetsRequireNameAndContext(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        (new Processor())->processConfiguration(new Configuration(), [['pageparts' => [['types' => []]]]]);
    }

    public function testNestedRowsAreAccepted(): void
    {
        $config = (new Processor())->processConfiguration(new Configuration(), [[
            'pagetemplates' => ['t' => ['name' => 'T', 'template' => 't.twig', 'rows' => [['regions' => [['name' => 'main', 'rows' => [[['name' => 'inner', 'span' => 6]]]]]]]]],
        ]]);

        $this->assertSame('inner', $config['pagetemplates']['t']['rows'][0]['regions'][0]['rows'][0][0]['name']);
    }

    public function testPresetsAndSwitchesBecomeParameters(): void
    {
        $container = new ContainerBuilder();
        $config = [
            'extended_pagepart_chooser' => true,
            'pageparts' => ['main' => ['name' => 'Main', 'context' => 'main', 'types' => [['name' => 'Text', 'class' => 'App\Text']]]],
            'pagetemplates' => ['plain' => ['name' => 'Plain', 'template' => 't.html.twig']],
        ];

        (new HgabkaPagePartExtension())->load([$config], $container);

        $this->assertTrue($container->getParameter('hgabka_page_part.extended'));
        $this->assertSame('Main', $container->getParameter('hgabka_page_part.page_parts_presets')['main']['name']);
        $this->assertSame('Plain', $container->getParameter('hgabka_page_part.page_templates_presets')['plain']['name']);
    }
}
