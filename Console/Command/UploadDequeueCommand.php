<?php
/*
 * Copyright © Websolute spa. All rights reserved.
 * See LICENSE and/or COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Websolute\TransporterCommand\Console\Command;

use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Websolute\TransporterAmqp\Model\Data\UploaderInfoFactory;
use Websolute\TransporterAmqp\Model\UploaderConsumer;
use Websolute\TransporterBase\Logger\Handler\Console;

class UploadDequeueCommand extends Command
{
    const UPLOADER_TYPE = 'uploader_type';
    const ACTIVITY_ID = 'activity_id';

    /**
     * @var Console
     */
    private $consoleLogger;

    /**
     * @var UploaderInfoFactory
     */
    private $uploaderInfoFactory;

    /**
     * @var UploaderConsumer
     */
    private $uploaderConsumer;

    /**
     * @param Console $consoleLogger
     * @param UploaderInfoFactory $uploaderInfoFactory
     * @param UploaderConsumer $uploaderConsumer
     * @param string $name
     */
    public function __construct(
        Console $consoleLogger,
        UploaderInfoFactory $uploaderInfoFactory,
        UploaderConsumer $uploaderConsumer,
        $name = null
    ) {
        parent::__construct($name);
        $this->consoleLogger = $consoleLogger;
        $this->uploaderInfoFactory = $uploaderInfoFactory;
        $this->uploaderConsumer = $uploaderConsumer;
    }

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->setDescription('Transporter: Upload dequeue for a specific UploaderType and ActivityId');
        $this->addArgument(
            self::UPLOADER_TYPE,
            InputArgument::REQUIRED,
            'UploaderType'
        );
        $this->addArgument(
            self::ACTIVITY_ID,
            InputArgument::REQUIRED,
            'ActivityId'
        );
        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->consoleLogger->setConsoleOutput($output);
        $uploaderType = $input->getArgument(self::UPLOADER_TYPE);
        $activityId = (int)$input->getArgument(self::ACTIVITY_ID);

        $uploaderInfo = $this->uploaderInfoFactory->create(
            [
                'activity_id' => $activityId,
                'uploader_type' => $uploaderType
            ]
        );

        $this->uploaderConsumer->process($uploaderInfo);
    }
}
