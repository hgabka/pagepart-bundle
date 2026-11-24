<?php

namespace Hgabka\PagePartBundle\Tests\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Hgabka\PagePartBundle\Entity\PagePartRef;
use Hgabka\PagePartBundle\Entity\TextPagePart;
use Hgabka\PagePartBundle\Repository\PagePartRefRepository;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[AllowMockObjectsWithoutExpectations]
class PagePartRefRepositoryTest extends TestCase
{
    /** @var MockObject&EntityManagerInterface */
    private $em;

    private PagePartRefRepository $repository;

    protected function setUp(): void
    {
        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->repository = new PagePartRefRepository($this->em, new ClassMetadata(PagePartRef::class));
    }

    public function testGetPagePartReturnsNullWhenTheRefIsGone(): void
    {
        $this->em->method('find')->willReturn(null);
        $this->em->expects(self::never())->method('persist');
        $this->em->expects(self::never())->method('flush');

        self::assertNull($this->repository->getPagePart(3470, 'main', 2));
    }

    public function testGetPagePartMovesTheRefAndReturnsThePagePart(): void
    {
        $ref = (new PagePartRef())
            ->setId(5)
            ->setPagePartId(55)
            ->setPagePartEntityname(TextPagePart::class);
        $part = new TextPagePart();
        $part->setId(55);

        $this->em->method('find')->willReturn($ref);
        $this->em->expects(self::once())->method('persist')->with($ref);
        $this->em->expects(self::once())->method('flush');

        $partRepo = $this->createMock(EntityRepository::class);
        $partRepo->expects(self::once())->method('find')->with(55)->willReturn($part);
        $this->em->method('getRepository')->with(TextPagePart::class)->willReturn($partRepo);

        self::assertSame($part, $this->repository->getPagePart(5, 'mobile', 4));
        self::assertSame('mobile', $ref->getContext());
        self::assertSame(4, $ref->getSequencenumber());
    }
}
