<?php

namespace Hgabka\PagePartBundle\Tests\Entity;

use Hgabka\MediaBundle\Entity\Media;
use Hgabka\PagePartBundle\Entity\AbstractPagePart;
use Hgabka\PagePartBundle\Entity\HeaderPagePart;
use Hgabka\PagePartBundle\Entity\ImagePagePart;
use Hgabka\PagePartBundle\Entity\LinePagePart;
use Hgabka\PagePartBundle\Entity\LinkPagePart;
use Hgabka\PagePartBundle\Entity\RawHTMLPagePart;
use Hgabka\PagePartBundle\Entity\TextPagePart;
use Hgabka\PagePartBundle\Entity\TocPagePart;
use Hgabka\PagePartBundle\Entity\ToTopPagePart;
use Hgabka\PagePartBundle\Form\HeaderPagePartAdminType;
use Hgabka\PagePartBundle\Form\ImagePagePartAdminType;
use Hgabka\PagePartBundle\Form\LinePagePartAdminType;
use Hgabka\PagePartBundle\Form\LinkPagePartAdminType;
use Hgabka\PagePartBundle\Form\RawHTMLPagePartAdminType;
use Hgabka\PagePartBundle\Form\TextPagePartAdminType;
use Hgabka\PagePartBundle\Form\TocPagePartAdminType;
use Hgabka\PagePartBundle\Form\ToTopPagePartAdminType;
use Hgabka\PagePartBundle\Helper\PagePartInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;

class PagePartsTest extends TestCase
{
    public static function pageParts(): array
    {
        return [
            'text' => [TextPagePart::class, '@HgabkaPagePart/TextPagePart/view.html.twig', TextPagePartAdminType::class],
            'header' => [HeaderPagePart::class, '@HgabkaPagePart/HeaderPagePart/view.html.twig', HeaderPagePartAdminType::class],
            'link' => [LinkPagePart::class, '@HgabkaPagePart/LinkPagePart/view.html.twig', LinkPagePartAdminType::class],
            'image' => [ImagePagePart::class, '@HgabkaPagePart/ImagePagePart/view.html.twig', ImagePagePartAdminType::class],
            'line' => [LinePagePart::class, '@HgabkaPagePart/LinePagePart/view.html.twig', LinePagePartAdminType::class],
            'raw html' => [RawHTMLPagePart::class, '@HgabkaPagePart/RawHTMLPagePart/view.html.twig', RawHTMLPagePartAdminType::class],
            'toc' => [TocPagePart::class, '@HgabkaPagePart/TocPagePart/view.html.twig', TocPagePartAdminType::class],
            'to top' => [ToTopPagePart::class, '@HgabkaPagePart/ToTopPagePart/view.html.twig', ToTopPagePartAdminType::class],
        ];
    }

    #[DataProvider('pageParts')]
    public function testViewsAndAdminTypes(string $class, string $view, string $adminType): void
    {
        /** @var AbstractPagePart $part */
        $part = new $class();

        $this->assertInstanceOf(PagePartInterface::class, $part);
        $this->assertSame($view, $part->getDefaultView());
        $this->assertSame($view, $part->getView());
        $this->assertSame($view, $part->getAdminView());
        $this->assertSame($adminType, $part->getDefaultAdminType());
        $this->assertTrue(class_exists($adminType));
    }

    public function testIdAccessors(): void
    {
        $part = new TextPagePart();

        $this->assertNull($part->getId());
        $this->assertSame($part, $part->setId(7));
        $this->assertSame(7, $part->getId());
    }

    public function testTextPagePart(): void
    {
        $part = (new TextPagePart())->setContent('tèst content with s3ç!àL');

        $this->assertSame('tèst content with s3ç!àL', $part->getContent());
        $this->assertSame('TextPagePart tèst content with s3ç!àL', (string) $part);
    }

    public function testRawHtmlIsEscapedInToString(): void
    {
        $part = (new RawHTMLPagePart())->setContent('<b>"x"</b>');

        $this->assertSame('<b>"x"</b>', $part->getContent());
        $this->assertSame('RawHTMLPagePart &lt;b&gt;&quot;x&quot;&lt;/b&gt;', (string) $part);
    }

    public function testHeaderPagePart(): void
    {
        $part = (new HeaderPagePart())->setNiv(2)->setTitle('Cím');

        $this->assertSame([2, 'Cím'], [$part->getNiv(), $part->getTitle()]);
        $this->assertSame('HeaderPagePart Cím', (string) $part);
    }

    public function testHeaderPagePartRequiresLevelAndTitle(): void
    {
        $validator = Validation::createValidatorBuilder()->addMethodMapping('loadValidatorMetadata')->getValidator();

        $violations = $validator->validate(new HeaderPagePart());

        $this->assertSame(
            ['headerpagepart.niv.not_blank', 'headerpagepart.title.not_blank'],
            array_map(static fn ($v) => $v->getMessage(), iterator_to_array($violations))
        );
        $this->assertCount(0, $validator->validate((new HeaderPagePart())->setNiv(1)->setTitle('x')));
    }

    public function testLinkPagePart(): void
    {
        $part = (new LinkPagePart())->setUrl('https://example.com')->setText('Example')->setOpenInNewWindow(true);

        $this->assertSame(['https://example.com', 'Example', true], [$part->getUrl(), $part->getText(), $part->getOpenInNewWindow()]);
        $this->assertSame('LinkPagePart', (string) $part);
    }

    public function testImagePagePart(): void
    {
        $media = new Media();
        $part = (new ImagePagePart())->setMedia($media)->setCaption('Caption')->setAltText('Alt')->setLink('/x')->setOpenInNewWindow(false);

        $this->assertSame([$media, 'Caption', 'Alt', '/x', false], [$part->getMedia(), $part->getCaption(), $part->getAltText(), $part->getLink(), $part->getOpenInNewWindow()]);
    }

    public function testNewPartsStartEmpty(): void
    {
        $this->assertNull((new TextPagePart())->getContent());
        $this->assertNull((new HeaderPagePart())->getTitle());
        $this->assertNull((new ImagePagePart())->getMedia());
        $this->assertNull((new LinkPagePart())->getUrl());
    }

    public function testMarkerPartsHaveStableLabels(): void
    {
        $this->assertSame('LinePagePart', (string) new LinePagePart());
        $this->assertSame('TocPagePart', (string) new TocPagePart());
        $this->assertSame('ToTopPagePart', (string) new ToTopPagePart());
    }
}
