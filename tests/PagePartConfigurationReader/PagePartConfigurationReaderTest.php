<?php

namespace Hgabka\PagePartBundle\Tests\PagePartConfigurationReader;

use Hgabka\PagePartBundle\Helper\HasPagePartsInterface;
use Hgabka\PagePartBundle\PagePartAdmin\PagePartAdminConfigurator;
use Hgabka\PagePartBundle\PagePartConfigurationReader\PagePartConfigurationParserInterface;
use Hgabka\PagePartBundle\PagePartConfigurationReader\PagePartConfigurationReader;
use PHPUnit\Framework\TestCase;

class PagePartConfigurationReaderTest extends TestCase
{
    private function page(array $configurations): HasPagePartsInterface
    {
        return new class($configurations) implements HasPagePartsInterface {
            public function __construct(private array $configurations)
            {
            }

            public function getId()
            {
                return 1;
            }

            public function getPagePartAdminConfigurations()
            {
                return $this->configurations;
            }
        };
    }

    private function configurator(string $context): PagePartAdminConfigurator
    {
        $configurator = new PagePartAdminConfigurator();
        $configurator->setContext($context);

        return $configurator;
    }

    public function testStringConfigurationsAreParsedOnceAndCached(): void
    {
        $main = $this->configurator('main');
        $parser = $this->createMock(PagePartConfigurationParserInterface::class);
        $parser->expects($this->once())->method('parse')->with('main')->willReturn($main);
        $reader = new PagePartConfigurationReader($parser);

        $this->assertSame([$main], $reader->getPagePartAdminConfigurators($this->page(['main'])));
        $this->assertSame([$main], $reader->getPagePartAdminConfigurators($this->page(['main'])));
    }

    public function testConfiguratorObjectsAreUsedAsIs(): void
    {
        $custom = $this->configurator('custom');
        $parser = $this->createMock(PagePartConfigurationParserInterface::class);
        $parser->expects($this->never())->method('parse');

        $result = (new PagePartConfigurationReader($parser))->getPagePartAdminConfigurators($this->page([$custom]));

        $this->assertSame([$custom], $result);
    }

    public function testUnsupportedConfigurationIsRejected(): void
    {
        $this->expectException(\Exception::class);
        (new PagePartConfigurationReader($this->createStub(PagePartConfigurationParserInterface::class)))->getPagePartAdminConfigurators($this->page([new \stdClass()]));
    }

    public function testContextsAreUnique(): void
    {
        $parser = $this->createStub(PagePartConfigurationParserInterface::class);
        $parser->method('parse')->willReturnCallback(fn (string $name): PagePartAdminConfigurator => $this->configurator('main' === $name ? 'main' : 'footer'));
        $reader = new PagePartConfigurationReader($parser);

        $contexts = $reader->getPagePartContexts($this->page(['main', 'footer', $this->configurator('main')]));

        $this->assertSame(['main', 'footer'], $contexts);
    }
}
