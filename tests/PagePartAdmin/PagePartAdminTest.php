<?php

namespace Hgabka\PagePartBundle\Tests\PagePartAdmin;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Hgabka\PagePartBundle\Entity\PagePartRef;
use Hgabka\PagePartBundle\Entity\TextPagePart;
use Hgabka\PagePartBundle\Helper\HasPagePartsInterface;
use Hgabka\PagePartBundle\PagePartAdmin\PagePartAdmin;
use Hgabka\PagePartBundle\PagePartAdmin\PagePartAdminConfiguratorInterface;
use Hgabka\PagePartBundle\Repository\PagePartRefRepository;
use Hgabka\UtilsBundle\Entity\EntityInterface;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[AllowMockObjectsWithoutExpectations]
class PagePartAdminTest extends TestCase
{
    /** @var MockObject&EntityManagerInterface */
    private $em;

    /** @var MockObject&PagePartRefRepository */
    private $refRepo;

    /** @var array<int, TextPagePart> keyed by pagepart id */
    private array $storedParts = [];

    protected function setUp(): void
    {
        $this->storedParts = [];
        $this->refRepo = $this->createMock(PagePartRefRepository::class);
        $this->em = $this->createMock(EntityManagerInterface::class);
    }

    /**
     * Builds an admin for a page whose "main" context holds the given refs (ref id => pagepart id).
     *
     * @param array<int, int> $refs
     */
    private function createAdmin(array $refs): TestablePagePartAdmin
    {
        $refEntities = [];
        $parts = [];
        $sequenceNumber = 1;
        foreach ($refs as $refId => $partId) {
            $ref = (new PagePartRef())
                ->setId($refId)
                ->setPagePartId($partId)
                ->setPagePartEntityname(TextPagePart::class)
                ->setContext('main')
                ->setSequencenumber($sequenceNumber++);
            $refEntities[] = $ref;

            $part = new TextPagePart();
            $part->setId($partId);
            $parts[] = $part;
            $this->storedParts[$partId] = $part;
        }

        $this->refRepo->method('getPagePartRefs')->willReturn($refEntities);

        $partRepo = $this->createStub(EntityRepository::class);
        $partRepo->method('findBy')->willReturn($parts);

        $this->em->method('getRepository')->willReturnCallback(
            fn (string $class) => PagePartRef::class === $class ? $this->refRepo : $partRepo
        );

        $configurator = $this->createStub(PagePartAdminConfiguratorInterface::class);
        $configurator->method('getContext')->willReturn('main');

        return new TestablePagePartAdmin($configurator, $this->em, new FakePage(), 'main', $this->createStub(EventDispatcherInterface::class));
    }

    public function testLoadsExistingPagePartsKeyedByRefId(): void
    {
        $admin = $this->createAdmin([1 => 11, 2 => 12]);

        self::assertSame([1 => $this->storedParts[11], 2 => $this->storedParts[12]], $admin->loadedPageParts());
    }

    public function testSequenceOrdersTheLoadedPageParts(): void
    {
        $admin = $this->createAdmin([1 => 11, 2 => 12, 3 => 13]);

        $admin->preBindRequest(new Request([], ['main_sequence' => ['3', '1', '2']]));

        self::assertSame(['3', '1', '2'], array_map('strval', array_keys($admin->loadedPageParts())));
        self::assertSame($this->storedParts[13], $admin->loadedPageParts()[3]);
    }

    public function testDeletedPagePartIsRemovedAndNotLookedUpAgainFromTheSequence(): void
    {
        $admin = $this->createAdmin([1 => 11, 2 => 12, 3 => 13]);

        $removed = [];
        $this->em->method('remove')->willReturnCallback(function (object $entity) use (&$removed) {
            $removed[] = $entity;
        });
        $this->em->expects(self::once())->method('flush');

        // The browser still posts the deleted id in the sequence list: this used to call
        // PagePartRefRepository::getPagePart() on the already removed ref and fatal.
        $this->refRepo->expects(self::never())->method('getPagePart');

        $admin->preBindRequest(new Request([], [
            '2_deleted' => 'true',
            'main_sequence' => ['1', '2', '3'],
        ]));

        self::assertSame([1, 3], array_keys($admin->loadedPageParts()));
        self::assertContains($this->storedParts[12], $removed);
        self::assertCount(2, $removed, 'the pagepart and its ref are both removed');
    }

    public function testDeletedFlagOtherThanTrueKeepsThePagePart(): void
    {
        $admin = $this->createAdmin([1 => 11, 2 => 12]);

        $this->em->expects(self::never())->method('remove');

        $admin->preBindRequest(new Request([], ['2_deleted' => 'false', 'main_sequence' => ['1', '2']]));

        self::assertSame([1, 2], array_keys($admin->loadedPageParts()));
    }

    public function testSequenceIdUnknownToTheAdminIsFetchedFromTheRepository(): void
    {
        $admin = $this->createAdmin([1 => 11]);
        $extra = new TextPagePart();
        $extra->setId(99);

        $this->refRepo->expects(self::once())
            ->method('getPagePart')
            ->with('7', 'main', 2)
            ->willReturn($extra);

        $admin->preBindRequest(new Request([], ['main_sequence' => ['1', '7']]));

        self::assertSame($extra, $admin->loadedPageParts()[7]);
    }

    public function testSequenceIdWithoutAnyRefIsSkippedInsteadOfCrashing(): void
    {
        $admin = $this->createAdmin([1 => 11, 2 => 12]);

        $this->refRepo->method('getPagePart')->willReturn(null);

        $admin->preBindRequest(new Request([], ['main_sequence' => ['1', '404', '2']]));

        self::assertSame([1, 2], array_keys($admin->loadedPageParts()));
    }

    public function testNewPagePartsAreCreatedFromTheirPostedType(): void
    {
        $admin = $this->createAdmin([1 => 11]);

        $admin->preBindRequest(new Request([], [
            'main_new' => ['newpp_1'],
            'main_type_newpp_1' => TextPagePart::class,
            'main_sequence' => ['newpp_1', '1'],
        ]));

        $loaded = $admin->loadedPageParts();
        self::assertSame(['newpp_1', '1'], array_map('strval', array_keys($loaded)));
        self::assertInstanceOf(TextPagePart::class, $loaded['newpp_1']);
        self::assertNull($loaded['newpp_1']->getId());
    }

    public function testRequestWithoutSequenceLeavesThePagePartsUntouched(): void
    {
        $admin = $this->createAdmin([1 => 11, 2 => 12]);

        $admin->preBindRequest(new Request());

        self::assertSame([1, 2], array_keys($admin->loadedPageParts()));
    }

    public function testNonEntityPageIsRejected(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new PagePartAdmin(
            $this->createStub(PagePartAdminConfiguratorInterface::class),
            $this->em,
            $this->createStub(HasPagePartsInterface::class),
            'main',
            $this->createStub(EventDispatcherInterface::class)
        );
    }
}

class TestablePagePartAdmin extends PagePartAdmin
{
    /** @return array<int|string, mixed> */
    public function loadedPageParts(): array
    {
        return $this->pageParts;
    }
}

class FakePage implements HasPagePartsInterface, EntityInterface
{
    public function getId(): ?int
    {
        return 1;
    }

    public function setId(?int $id): static
    {
        return $this;
    }

    public function getPagePartAdminConfigurations()
    {
        return [];
    }
}
