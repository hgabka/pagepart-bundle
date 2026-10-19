<?php

namespace Hgabka\PagePartBundle\Tests\PageTemplate;

use Hgabka\PagePartBundle\Helper\HasPageTemplateInterface;
use Hgabka\PagePartBundle\PageTemplate\PageTemplate;
use Hgabka\PagePartBundle\PageTemplate\PageTemplateConfigurationParser;
use Hgabka\PagePartBundle\PageTemplate\PageTemplateConfigurationParserInterface;
use Hgabka\PagePartBundle\PageTemplate\PageTemplateConfigurationReader;
use Hgabka\PagePartBundle\PageTemplate\Region;
use Hgabka\PagePartBundle\PageTemplate\Row;
use Hgabka\UtilsBundle\Helper\HgabkaUtils;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\KernelInterface;

class PageTemplateTest extends TestCase
{
    private string $dir;

    protected function setUp(): void
    {
        $this->dir = sys_get_temp_dir() . '/hg_pt_' . bin2hex(random_bytes(4));
        mkdir($this->dir . '/config/pagetemplates', 0777, true);
    }

    protected function tearDown(): void
    {
        foreach (glob($this->dir . '/config/pagetemplates/*') ?: [] as $file) {
            unlink($file);
        }
        rmdir($this->dir . '/config/pagetemplates');
        rmdir($this->dir . '/config');
        rmdir($this->dir);
    }

    private function parser(array $presets = []): PageTemplateConfigurationParser
    {
        $kernel = $this->createStub(KernelInterface::class);
        $kernel->method('getProjectDir')->willReturn($this->dir);

        return new PageTemplateConfigurationParser($kernel, $this->createStub(HgabkaUtils::class), $presets);
    }

    public function testValueObjects(): void
    {
        $region = new Region('main', 8, 'tpl.html.twig');
        $row = new Row([$region]);
        $template = (new PageTemplate());
        $template->setName('Name');
        $template->setRows([$row]);
        $template->setTemplate('t.html.twig');

        $this->assertSame(['main', 8, 'tpl.html.twig', [], []], [$region->getName(), $region->getSpan(), $region->getTemplate(), $region->getChildren(), $region->getRows()]);
        $this->assertSame([$region], $row->getRegions());
        $this->assertSame(['Name', [$row], 't.html.twig'], [$template->getName(), $template->getRows(), $template->getTemplate()]);
    }

    public function testParsesYamlWithDefaultsAndNesting(): void
    {
        file_put_contents($this->dir . '/config/pagetemplates/two.yaml', <<<'YAML'
name: Two columns
template: 'Pages\two.html.twig'
rows:
    - regions:
        - { name: left, span: 4 }
        - name: right
          regions:
              - { name: inner }
          rows:
              - [ { name: nested, span: 6 } ]
YAML);

        $template = $this->parser()->parse('two');

        $this->assertSame('Two columns', $template->getName());
        $this->assertSame('Pages\two.html.twig', $template->getTemplate());
        [$row] = $template->getRows();
        [$left, $right] = $row->getRegions();
        $this->assertSame(['left', 4], [$left->getName(), $left->getSpan()]);
        $this->assertSame(['right', 12], [$right->getName(), $right->getSpan()]);
        $this->assertSame('inner', $right->getChildren()[0]->getName());
        $this->assertSame('nested', $right->getRows()[0]->getRegions()[0]->getName());
    }

    public function testPresetsAreUsedBeforeFiles(): void
    {
        $template = $this->parser(['x' => ['name' => 'Preset', 'template' => 't', 'rows' => [['regions' => [['name' => 'main']]]]]])->parse('x');

        $this->assertSame('Preset', $template->getName());
        $this->assertSame('main', $template->getRows()[0]->getRegions()[0]->getName());
    }

    private function page(array $templates): HasPageTemplateInterface
    {
        return new class($templates) implements HasPageTemplateInterface {
            public function __construct(private array $templates)
            {
            }

            public function getId()
            {
                return 1;
            }

            public function getPagePartAdminConfigurations()
            {
                return [];
            }

            public function getPageTemplates()
            {
                return $this->templates;
            }
        };
    }

    public function testReaderIndexesTemplatesByName(): void
    {
        $object = new PageTemplate();
        $object->setName('Custom');
        $parsed = new PageTemplate();
        $parsed->setName('Parsed');
        $parser = $this->createStub(PageTemplateConfigurationParserInterface::class);
        $parser->method('parse')->willReturn($parsed);

        $result = (new PageTemplateConfigurationReader($parser))->getPageTemplates($this->page(['contentpage', $object]));

        $this->assertSame(['Parsed' => $parsed, 'Custom' => $object], $result);
    }

    public function testReaderRejectsUnknownTemplateTypes(): void
    {
        $this->expectException(\Exception::class);
        (new PageTemplateConfigurationReader($this->createStub(PageTemplateConfigurationParserInterface::class)))->getPageTemplates($this->page([new \stdClass()]));
    }
}
