<?php

namespace App\Tests\Form;

use App\Form\LegacyDataType;
use App\Entity\LegacyData;
use Symfony\Component\Form\Test\TypeTestCase;

/**
 * Class LegacyDataTypeTest.
 */
class LegacyDataTypeTest extends TypeTestCase
{
    /**
     * Unit test form type.
     */
    public function testSubmitValidData(): void
    {
        $formData = [
            'email' => 'a@b.com',
            'message' => 'Some message',
        ];

        $objectToCompare = new LegacyData();
        $form = $this->factory->create(LegacyDataType::class, $objectToCompare);

        $object = new LegacyData();
        $object->setEmail($formData['email']);
        $object->setMessage($formData['message']);

        // submit the data to the form directly
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());

        // check that $objectToCompare was modified as expected when the form was submitted
        $this->assertEquals($object, $objectToCompare);

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }
}
