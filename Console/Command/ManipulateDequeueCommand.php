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
use Websolute\TransporterAmqp\Model\Data\ManipulatorInfoFactory;
use Websolute\TransporterAmqp\Model\ManipulatorConsumer;
use Websolute\TransporterBase\Logger\Handler\Console;

class ManipulateDequeueCommand extends Command
{
    const ENTITY_IDENTIFIER = 'entity_identifier';
    const ACTIVITY_ID = 'activity_id';

    /**
     * @var Console
     */
    private $consoleLogger;

    /**
     * @var ManipulatorInfoFactory
     */
    private $manipulatorInfoFactory;

    /**
     * @var ManipulatorConsumer
     */
    private $manipulatorConsumer;

    /**
     * @param Console $consoleLogger
     * @param ManipulatorInfoFactory $manipulatorInfoFactory
     * @param ManipulatorConsumer $manipulatorConsumer
     * @param string $name
     */
    public function __construct(
        Console $consoleLogger,
        ManipulatorInfoFactory $manipulatorInfoFactory,
        ManipulatorConsumer $manipulatorConsumer,
        $name = null
    ) {
        parent::__construct($name);
        $this->consoleLogger = $consoleLogger;
        $this->manipulatorInfoFactory = $manipulatorInfoFactory;
        $this->manipulatorConsumer = $manipulatorConsumer;
    }

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->setDescription('Transporter: Manipulate dequeue for a specific EntityIdentifier and ActivityId');
        $this->addArgument(
            self::ENTITY_IDENTIFIER,
            InputArgument::REQUIRED,
            'EntityIdentifier'
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
        $entityIdentifier = $input->getArgument(self::ENTITY_IDENTIFIER);
        $activityId = (int)$input->getArgument(self::ACTIVITY_ID);

        $manipulatorInfo = $this->manipulatorInfoFactory->create(
            [
                'activity_id' => $activityId,
                'entity_identifier' => $entityIdentifier
            ]
        );

        $this->manipulatorConsumer->process($manipulatorInfo);
    }
}
