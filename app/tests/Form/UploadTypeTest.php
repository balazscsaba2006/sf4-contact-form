<?php

namespace App\Tests\Form;

use App\Form\LegacyDataType;
use App\Form\UploadType;
use App\Tests\WebTestCaseUtilsTrait;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Validator\Validation;

/**
 * Class UploadTypeTest.
 */
class UploadTypeTest extends TypeTestCase
{
    use WebTestCaseUtilsTrait;

    /**
     * @return ValidatorExtension[]|array
     */
    protected function getExtensions(): array
    {
        $validator = Validation::createValidatorBuilder()
            ->enableAnnotationMapping()
            ->getValidator();

        $childType = new LegacyDataType();

        return [
            new ValidatorExtension($validator),
            new PreloadedExtension([$childType], []),
        ];
    }

    /**
     * @return array
     */
    protected function getTypes(): array
    {
        return [
            new UploadType(2, true, ';'),
        ];
    }

    /**
     * Unit test form type.
     */
    public function testSubmitValidData(): void
    {
        $file = $this->getUploadedFile('correct.csv');
        $formData = ['file' => $file];

        $form = $this->factory->create(UploadType::class);

        // submit the data to the form directly
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }
}
