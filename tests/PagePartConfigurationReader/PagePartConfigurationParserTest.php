<?php

namespace Hgabka\PagePartBundle\Tests\PagePartConfigurationReader;

use Hgabka\PagePartBundle\PagePartAdmin\PagePartAdminConfigurator;
use Hgabka\PagePartBundle\PagePartConfigurationReader\PagePartConfigurationParser;
use Hgabka\UtilsBundle\Helper\HgabkaUtils;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\KernelInterface;

class PagePartConfigurationParserTest extends TestCase
{
    private string $dir;

    protected function setUp(): void
    {
        $this->dir = sys_get_temp_dir() . '/hg_pp_' . bin2hex(random_bytes(4));
        mkdir($this->dir . '/config/pageparts', 0777, true);
        mkdir($this->dir . '/bundle/Resources/config/pageparts', 0777, true);
    }

    protected function tearDown(): void
    {
        foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($this->dir, \FilesystemIterator::SKIP_DOTS), \RecursiveIteratorIterator::CHILD_FIRST) as $file) {
            $file->isDir() ? rmdir($file->getPathname()) : unlink($file->getPathname());
        }
        rmdir($this->dir);
    }

    private function parser(array $presets = []): PagePartConfigurationParser
    {
        $kernel = $this->createStub(KernelInterface::class);
        $kernel->method('getProjectDir')->willReturn($this->dir);
        $kernel->method('locateResource')->willReturnCallback(fn (string $name): string => $this->dir . '/bundle/' . substr($name, strpos($name, '/') + 1));

        $utils = $this->createStub(HgabkaUtils::class);
        $utils->method('replaceExtension')->willReturnCallback(static fn (string $file, string $ext): string => preg_replace('/\.[^.]+$/', '.' . $ext, $file));

        return new PagePartConfigurationParser($kernel, $utils, $presets);
    }

    private function write(string $relative, string $yaml): void
    {
        file_put_contents($this->dir . '/' . $relative, $yaml);
    }

    public function testParsesAYamlFileFromTheProject(): void
    {
        $this->write('config/pageparts/main.yaml', <<<'YAML'
name: Main
context: main
config: { viewCss: /a.css }
widget_template: '@App/widget.html.twig'
types:
    - { name: Text, class: App\TextPart }
    - { name: Video, class: App\VideoPart, preview: prev.png, pagelimit: 2 }
YAML);

        $configurator = $this->parser()->parse('main');

        $this->assertInstanceOf(PagePartAdminConfigurator::class, $configurator);
        $this->assertSame('Main', $configurator->getName());
        $this->assertSame('main', $configurator->getInternalName());
        $this->assertSame('main', $configurator->getContext());
        $this->assertSame(['viewCss' => '/a.css'], $configurator->getConfig());
        $this->assertSame('@App/widget.html.twig', $configurator->getWidgetTemplate());
        $this->assertSame(
            [
                ['name' => 'Text', 'class' => 'App\TextPart', 'preview' => ''],
                ['name' => 'Video', 'class' => 'App\VideoPart', 'preview' => 'prev.png', 'pagelimit' => 2],
            ],
            $configurator->getPossiblePagePartTypes()
        );
    }

    public function testYmlExtensionIsAFallback(): void
    {
        $this->write('config/pageparts/old.yml', "name: Old\ncontext: old\n");

        $configurator = $this->parser()->parse('old');

        $this->assertSame('old', $configurator->getContext());
        $this->assertSame([], $configurator->getPossiblePagePartTypes());
    }

    public function testPresetsWinOverFiles(): void
    {
        $this->write('config/pageparts/main.yaml', "name: File\ncontext: file\n");

        $configurator = $this->parser(['main' => ['name' => 'Preset', 'context' => 'preset']])->parse('main');

        $this->assertSame('Preset', $configurator->getName());
    }

    public function testNamespacedConfigIsLocatedInTheBundle(): void
    {
        $this->write('bundle/Resources/config/pageparts/shared.yaml', "name: Shared\ncontext: shared\n");

        $configurator = $this->parser()->parse('AppBundle:shared');

        $this->assertSame('Shared', $configurator->getName());
    }

    public function testExtendsMergesTypesAndEmptyClassRemovesOne(): void
    {
        $this->write('config/pageparts/base.yaml', <<<'YAML'
name: Base
context: base
types:
    - { name: Text, class: App\Text }
    - { name: Header, class: App\Header }
YAML);
        $this->write('config/pageparts/child.yaml', <<<'YAML'
name: Child
context: child
extends: base
types:
    - { name: Header, class: '' }
    - { name: Image, class: App\Image }
YAML);

        $configurator = $this->parser()->parse('child');

        $this->assertSame(['Text', 'Image'], array_column($configurator->getPossiblePagePartTypes(), 'name'));
    }

    public function testRecursiveExtendsIsDetected(): void
    {
        $this->write('config/pageparts/a.yaml', "name: A\ncontext: a\nextends: b\n");
        $this->write('config/pageparts/b.yaml', "name: B\ncontext: b\nextends: a\n");

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Recursion detected');
        $this->parser()->parse('a');
    }
}
