<?php

namespace App\Csv;

use App\Entity\LegacyData;
use App\Form\LegacyDataType;
use Doctrine\ORM\EntityManagerInterface;
use League\Csv\Exception as CsvException;
use League\Csv\Reader;
use League\Csv\Statement;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

/**
 * Class CsvHandler.
 */
class CsvHandler implements HandlerInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var bool
     */
    private $firstLineAsHeader;

    /**
     * @var string
     */
    private $delimiter;

    /**
     * CsvHandler constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param FormFactoryInterface   $formFactory
     * @param bool                   $firstLineAsHeader
     * @param string                 $delimiter
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        FormFactoryInterface $formFactory,
        bool $firstLineAsHeader,
        string $delimiter
    ) {
        $this->entityManager = $entityManager;
        $this->formFactory = $formFactory;
        $this->firstLineAsHeader = $firstLineAsHeader;
        $this->delimiter = $delimiter;
    }

    /**
     * {@inheritdoc}
     *
     * @throws CsvException
     */
    public function parse(string $path): Reader
    {
        $csv = Reader::createFromPath($path, 'r');
        $csv->setDelimiter($this->delimiter);

        if (true === $this->firstLineAsHeader) {
            $csv->setHeaderOffset(0);
        }

        return $csv;
    }

    /**
     * @param Reader $csv
     *
     * @return \Iterator
     */
    public function getRecords(Reader $csv): \Iterator
    {
        $header = $this->getHeader($csv);

        return $csv->getRecords($header);
    }

    /**
     * {@inheritdoc}
     */
    public function validateAndSave(\Iterator $rows): Result
    {
        $result = new Result();

        foreach ($rows as $rowNumber => $row) {
            $legacyData = new LegacyData();
            $form = $this->formFactory->create(LegacyDataType::class, $legacyData);
            $form->submit($row);

            if ($form->isSubmitted() && $form->isValid()) {
                $this->entityManager->persist($legacyData);
                $result->incrementSaved();

                // flush every 100 rows
                if (0 === ($rowNumber % 100)) {
                    $this->entityManager->flush();
                }
            } else {
                $errors = $this->getErrorMessages($form);
                foreach ($errors as $field => $messages) {
                    $result->addError($rowNumber, $field, $messages);
                }
            }
        }

        $this->entityManager->flush();
        $this->entityManager->clear();

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getFirstLine(Reader $csv)
    {
        if (true === $this->firstLineAsHeader) {
            return $this->getHeader($csv);
        }

        return $this->getFirstRecord($csv);
    }

    /**
     * {@inheritdoc}
     */
    public function getHeader(Reader $csv)
    {
        try {
            if (true === $this->firstLineAsHeader) {
                return $csv->getHeader();
            }

            return [];
        } catch (CsvException $e) {
            return [];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getFirstRecord(Reader $csv)
    {
        try {
            $stmt = (new Statement())
                ->offset(0)
                ->limit(1);

            $result = $stmt->process($csv);

            return $result->fetchOne(0);
        } catch (CsvException $e) {
            return [];
        }
    }

    /**
     * Get error messages from form.
     *
     * @param FormInterface $form
     *
     * @return array
     */
    private function getErrorMessages(FormInterface $form): array
    {
        $errors = [];

        if (0 === $form->count()) {
            return $errors;
        }

        foreach ($form->all() as $child) {
            if ($child->isValid()) {
                continue;
            }

            // remove "ERROR:" prefix added by FormErrorIterator
            $error = (string) $form[$child->getName()]->getErrors();
            $errors[$child->getName()][] = trim(str_replace('ERROR:', '', $error));
        }

        return $errors;
    }
}
