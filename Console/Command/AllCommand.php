<?php
/*
 * Copyright © Websolute spa. All rights reserved.
 * See LICENSE and/or COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Websolute\TransporterCommand\Console\Command;

use Exception;
use Magento\Framework\Exception\NoSuchEntityException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Websolute\TransporterActivity\Model\HasRunningActivity;
use Websolute\TransporterBase\Api\TransporterListInterface;
use Websolute\TransporterBase\Logger\Handler\Console;
use Websolute\TransporterCommand\Model\ProcessAll;

class AllCommand extends Command
{
    /** @var string */
    const EXTRA = 'extra';

    /** @var string */
    const TYPE = 'type';

    /** @var string */
    const FORCE = 'force';

    /**
     * @var Console
     */
    private $consoleLogger;

    /**
     * @var TransporterListInterface
     */
    private $transporterList;

    /**
     * @var HasRunningActivity
     */
    private $hasRunningActivity;

    /**
     * @var ProcessAll
     */
    private $processAll;

    /**
     * @param Console $consoleLogger
     * @param TransporterListInterface $transporterList
     * @param HasRunningActivity $hasRunningActivity
     * @param ProcessAll $processAll
     * @param null $name
     */
    public function __construct(
        Console $consoleLogger,
        TransporterListInterface $transporterList,
        HasRunningActivity $hasRunningActivity,
        ProcessAll $processAll,
        $name = null
    ) {
        parent::__construct($name);
        $this->consoleLogger = $consoleLogger;
        $this->transporterList = $transporterList;
        $this->hasRunningActivity = $hasRunningActivity;
        $this->processAll = $processAll;
    }

    /**
     * @return string
     */
    public function getHelp()
    {
        $text = [];
        $text[] = __('Available DownloaderList types: ')->getText();
        $allDownlaoderList = $this->transporterList->getAllDownloaderList();
        foreach ($allDownlaoderList as $name => $downlaoderList) {
            $text[] = $name;
            $text[] = ', ';
        }
        $text[] = __('Available ManipulatorList types: ')->getText();
        $allManipulatorList = $this->transporterList->getAllManipulatorList();
        foreach ($allManipulatorList as $name => $manipulatorList) {
            $text[] = $name;
            $text[] = ', ';
        }
        $text[] = __('Available UploaderList types: ')->getText();
        $allUplaoderList = $this->transporterList->getAllUploaderList();
        foreach ($allUplaoderList as $name => $uplaoderList) {
            $text[] = $name;
            $text[] = ', ';
        }
        array_pop($text);
        return implode('', $text);
    }

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->setDescription('Transporter: Download + Manipulate + Upload for a specific Type');
        $this->addArgument(
            self::TYPE,
            InputArgument::REQUIRED,
            'Type name'
        );

        $this->addArgument(
            self::EXTRA,
            InputArgument::OPTIONAL,
            'Extra data',
            ''
        );

        $this->addOption(
            self::FORCE,
            'f',
            InputOption::VALUE_NONE,
            'Force if already there is a running activity',
            null
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
        $extra = $input->getArgument(self::EXTRA);

        $force = (bool)$input->getOption(self::FORCE);

        if (!$force && $this->hasRunningActivity->execute($type)) {
            throw new NoSuchEntityException(
                __(
                    'There is an activity with type:%1 that is already running',
                    $type
                )
            );
        }

        $this->processAll->execute($type, $extra);
    }
}
