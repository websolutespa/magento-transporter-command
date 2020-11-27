<?php
/*
 * Copyright © Websolute spa. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Websolute\TransporterCommand\Console\Command;

use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Websolute\TransporterAmqp\Model\Data\DownloaderInfoFactory;
use Websolute\TransporterAmqp\Model\DownloaderConsumer;
use Websolute\TransporterBase\Logger\Handler\Console;

class DownloadDequeueCommand extends Command
{
    const TYPE = 'type';
    const ACTIVITY_ID = 'activity_id';

    /**
     * @var Console
     */
    private $consoleLogger;

    /**
     * @var DownloaderInfoFactory
     */
    private $downloaderInfoFactory;

    /**
     * @var DownloaderConsumer
     */
    private $downloaderConsumer;

    /**
     * @param Console $consoleLogger
     * @param DownloaderInfoFactory $downloaderInfoFactory
     * @param DownloaderConsumer $downloaderConsumer
     * @param null $name
     */
    public function __construct(
        Console $consoleLogger,
        DownloaderInfoFactory $downloaderInfoFactory,
        DownloaderConsumer $downloaderConsumer,
        $name = null
    ) {
        parent::__construct($name);
        $this->consoleLogger = $consoleLogger;
        $this->downloaderInfoFactory = $downloaderInfoFactory;
        $this->downloaderConsumer = $downloaderConsumer;
    }

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->setDescription('Transporter: Download dequeue for a specific Type and ActivityId');
        $this->addArgument(
            self::TYPE,
            InputArgument::REQUIRED,
            'DownloaderList Type'
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
        $type = $input->getArgument(self::TYPE);
        $activityId = (int)$input->getArgument(self::ACTIVITY_ID);

        $downloaderInfo = $this->downloaderInfoFactory->create(
            [
                'activity_id' => $activityId,
                'downloader_type' => $type
            ]
        );

        $this->downloaderConsumer->process($downloaderInfo);
    }
}
