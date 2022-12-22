<?php

declare(strict_types=1);

namespace Test\Unit\Common\Domain\Model\ValueObject\Object;

use Common\Adapter\Validation\ValidationChain;
use Common\Domain\Model\ValueObject\Object\UserImage;
use Common\Domain\Ports\FileUpload\FileInterface;
use Common\Domain\Validation\VALIDATION_ERRORS;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints\BuiltInFunctionsReturn;

require_once 'tests/BuiltinFunctions/SymfonyComponentValidatorConstraints.php';

class UserImageTest extends TestCase
{
    private const PATH_FILE = 'tests/Fixtures/Files/file.txt';
    private const PATH_FILE_NOT_FOUND = 'tests/Fixtures/Files/fileNotFound.txt';
    private const PATH_FILE_EMPTY = 'tests/Fixtures/Files/FileEmpty.txt';

    private UserImage $object;
    private ValidationChain $validator;
    private MockObject|FileInterface $fileInterface;

    public function setUp(): void
    {
        parent::setUp();

        $this->fileInterface = $this->getFileInterface(self::PATH_FILE);
        $this->object = new UserImage($this->fileInterface);
        $this->validator = new ValidationChain();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        BuiltInFunctionsReturn::$is_readable = null;
        BuiltInFunctionsReturn::$filesize = null;
        BuiltInFunctionsReturn::$getimagesize = null;
        BuiltInFunctionsReturn::$imagecreatefromstring = null;
        BuiltInFunctionsReturn::$unlink = null;
    }

    private function getFileInterface(string $fileName): MockObject|FileInterface
    {
        $file = $this
            ->getMockBuilder(File::class)
            ->setConstructorArgs([$fileName, false])
            ->getMock();

        $file
            ->expects($this->any())
            ->method('getPathname')
            ->willReturn($fileName);

        $fileInterface = $this
            ->getMockBuilder(FileInterface::class)
            ->onlyMethods(['getFile'])
            ->getMockForAbstractClass();

        $fileInterface
            ->expects($this->any())
            ->method('getFile')
            ->willReturn($file);

        return $fileInterface;
    }

    /** @test */
    public function itShouldValidateTheImage(): void
    {
        /** @var MockObject|File $file */
        $file = $this->fileInterface->getFile();

        $file
            ->expects($this->any())
            ->method('getMimeType')
            ->willReturn('image/png');

        BuiltInFunctionsReturn::$getimagesize = [100, 100];
        $object = new UserImage($this->fileInterface);
        $return = $this->validator->validateValueObject($object);

        $this->assertEmpty($return);
    }

    /** @test */
    public function itShouldFailFileCanNotBeNull(): void
    {
        $this->object = new UserImage(null);
        $return = $this->validator->validateValueObject($this->object);

        $this->assertEquals([VALIDATION_ERRORS::NOT_NULL, VALIDATION_ERRORS::NOT_BLANK], $return);
    }

    /** @test */
    public function itShouldFailFileMimeTypeCanNotBeTxt(): void
    {
        $return = $this->validator->validateValueObject($this->object);

        $this->assertEquals([VALIDATION_ERRORS::FILE_INVALID_MIME_TYPE], $return);
    }

    /** @test */
    public function itShouldFailFileNotFound(): void
    {
        $object = new UserImage($this->getFileInterface(self::PATH_FILE_NOT_FOUND));

        $return = $this->validator->validateValueObject($object);

        $this->assertEquals([VALIDATION_ERRORS::FILE_NOT_FOUND], $return);
    }

    /** @test */
    public function itShouldFailFileIsNotReadable(): void
    {
        /** @var MockObject|File $file */
        $file = $this->fileInterface->getFile();
        BuiltInFunctionsReturn::$is_readable = false;

        $return = $this->validator->validateValueObject($this->object);

        $this->assertEquals([VALIDATION_ERRORS::FILE_NOT_READABLE], $return);
    }

    /** @test */
    public function itShouldFailFileIsEmpty(): void
    {
        $fileInterface = $this->getFileInterface(self::PATH_FILE_EMPTY);
        /** @var MockObject|File $file */
        $file = $fileInterface->getFile();
        $file
            ->expects($this->any())
            ->method('getMimeType')
            ->willReturn('image/png');

        $object = new UserImage($fileInterface);
        BuiltInFunctionsReturn::$is_readable = true;

        $return = $this->validator->validateValueObject($object);

        $this->assertEquals([VALIDATION_ERRORS::FILE_EMPTY], $return);
    }

    /** @test */
    public function itShouldFailFileSizeIslargeThan2MB(): void
    {
        /** @var MockObject|File $file */
        $file = $this->fileInterface->getFile();

        $file
            ->expects($this->any())
            ->method('getMimeType')
            ->willReturn('image/png');

        BuiltInFunctionsReturn::$filesize = 2 * 1_000_000 + 1;

        $return = $this->validator->validateValueObject($this->object);

        $this->assertEquals([VALIDATION_ERRORS::FILE_USER_IMAGE_TOO_LARGE], $return);
    }

    /** @test */
    public function itShouldFailCanNotDetermineWidthAndHeigth(): void
    {
        /** @var MockObject|File $file */
        $file = $this->fileInterface->getFile();

        $file
            ->expects($this->any())
            ->method('getMimeType')
            ->willReturn('image/png');

        BuiltInFunctionsReturn::$filesize = 1;
        BuiltInFunctionsReturn::$getimagesize = [];

        $return = $this->validator->validateValueObject($this->object);

        $this->assertEquals([VALIDATION_ERRORS::FILE_USER_IMAGE_SIZE_NOT_DETECTED], $return);
    }

    /** @test */
    public function itShouldValidateFileWidthHasNotMinWidth(): void
    {
        /** @var MockObject|File $file */
        $file = $this->fileInterface->getFile();
        $file
            ->expects($this->any())
            ->method('getMimeType')
            ->willReturn('image/png');

        BuiltInFunctionsReturn::$getimagesize = [100, 100];

        $return = $this->validator->validateValueObject($this->object);

        $this->assertEmpty($return);
    }

    /** @test */
    public function itShouldValidateFileWidthHasNotMaxWidth(): void
    {
        /** @var MockObject|File $file */
        $file = $this->fileInterface->getFile();

        $file
            ->expects($this->any())
            ->method('getMimeType')
            ->willReturn('image/png');

        BuiltInFunctionsReturn::$getimagesize = [100, 100];

        $return = $this->validator->validateValueObject($this->object);

        $this->assertEmpty($return);
    }

    /** @test */
    public function itShouldValidateFileHeigthHasNotMinWidth(): void
    {
        /** @var MockObject|File $file */
        $file = $this->fileInterface->getFile();

        $file
            ->expects($this->any())
            ->method('getMimeType')
            ->willReturn('image/png');

        BuiltInFunctionsReturn::$getimagesize = [100, 100];

        $return = $this->validator->validateValueObject($this->object);

        $this->assertEmpty($return);
    }

    /** @test */
    public function itShouldValidateFileHeigthHasNotMaxWidth(): void
    {
        /** @var MockObject|File $file */
        $file = $this->fileInterface->getFile();

        $file
            ->expects($this->any())
            ->method('getMimeType')
            ->willReturn('image/png');

        BuiltInFunctionsReturn::$getimagesize = [1, 100];

        $return = $this->validator->validateValueObject($this->object);

        $this->assertEmpty($return);
    }

    /** @test */
    public function itShouldValidateFilePixelsHasNotMinWidth(): void
    {
        /** @var MockObject|File $file */
        $file = $this->fileInterface->getFile();

        $file
            ->expects($this->any())
            ->method('getMimeType')
            ->willReturn('image/png');

        BuiltInFunctionsReturn::$getimagesize = [100, 100];

        $return = $this->validator->validateValueObject($this->object);

        $this->assertEmpty($return);
    }

    /** @test */
    public function itShouldValidateFilePixelsHasNotMaxWidth(): void
    {
        /** @var MockObject|File $file */
        $file = $this->fileInterface->getFile();

        $file
            ->expects($this->any())
            ->method('getMimeType')
            ->willReturn('image/png');

        BuiltInFunctionsReturn::$getimagesize = [100, 100];

        $return = $this->validator->validateValueObject($this->object);

        $this->assertEmpty($return);
    }

    /** @test */
    public function itShouldValidateFileAspectRatioHasNotMinWidth(): void
    {
        /** @var MockObject|File $file */
        $file = $this->fileInterface->getFile();

        $file
            ->expects($this->any())
            ->method('getMimeType')
            ->willReturn('image/png');

        BuiltInFunctionsReturn::$getimagesize = [100, 100];

        $return = $this->validator->validateValueObject($this->object);

        $this->assertEmpty($return);
    }

    /** @test */
    public function itShouldValidateFileAspectRatioHasNotMaxWidth(): void
    {
        /** @var MockObject|File $file */
        $file = $this->fileInterface->getFile();

        $file
            ->expects($this->any())
            ->method('getMimeType')
            ->willReturn('image/png');

        BuiltInFunctionsReturn::$getimagesize = [100, 100];

        $return = $this->validator->validateValueObject($this->object);

        $this->assertEmpty($return);
    }

    /** @test */
    public function itShouldValidateFileCanBeAnSquare(): void
    {
        /** @var MockObject|File $file */
        $file = $this->fileInterface->getFile();

        $file
            ->expects($this->any())
            ->method('getMimeType')
            ->willReturn('image/png');

        BuiltInFunctionsReturn::$filesize = 1;
        BuiltInFunctionsReturn::$getimagesize = [100, 100];

        $return = $this->validator->validateValueObject($this->object);

        $this->assertEmpty($return);
    }

    /** @test */
    public function itShouldFailFileCanNotBeALandscape(): void
    {
        /** @var MockObject|File $file */
        $file = $this->fileInterface->getFile();

        $file
            ->expects($this->any())
            ->method('getMimeType')
            ->willReturn('image/png');

        BuiltInFunctionsReturn::$filesize = 1;
        BuiltInFunctionsReturn::$getimagesize = [101, 100];

        $return = $this->validator->validateValueObject($this->object);

        $this->assertEquals([VALIDATION_ERRORS::FILE_USER_IMAGE_LANDSCAPE_NOT_ALLOWED], $return);
    }

    /** @test */
    public function itShouldValidateFileIsCorrupted(): void
    {
        /** @var MockObject|File $file */
        $file = $this->fileInterface->getFile();

        $file
            ->expects($this->any())
            ->method('getMimeType')
            ->willReturn('image/png');

        BuiltInFunctionsReturn::$getimagesize = [100, 100];
        BuiltInFunctionsReturn::$imagecreatefromstring = false;

        $return = $this->validator->validateValueObject($this->object);

        $this->assertEmpty($return);
    }

    /** @test */
    public function itSouldReturnNullAsAValidationValue(): void
    {
        $object = new UserImage(null);
        $return = $object->getValidationValue();

        $this->assertNull($return);
    }

    /** @test */
    public function itSouldReturnTheValidationValue(): void
    {
        $return = $this->object->getValidationValue();

        $this->assertInstanceOf(File::class, $return);
    }
}
