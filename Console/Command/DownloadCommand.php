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
use Websolute\TransporterBase\Api\TransporterListInterface;
use Websolute\TransporterBase\Logger\Handler\Console;
use Websolute\TransporterBase\Model\Action\DownloadAction;

class DownloadCommand extends Command
{
    const TYPE = 'type';

    /**
     * @var DownloadAction
     */
    private $downloadAction;

    /**
     * @var Console
     */
    private $consoleLogger;

    /**
     * @var TransporterListInterface
     */
    private $transporterList;

    /**
     * @param null $name
     * @param DownloadAction $downloadAction
     * @param Console $consoleLogger
     * @param TransporterListInterface $transporterList
     */
    public function __construct(
        DownloadAction $downloadAction,
        Console $consoleLogger,
        TransporterListInterface $transporterList,
        $name = null
    ) {
        parent::__construct($name);
        $this->downloadAction = $downloadAction;
        $this->consoleLogger = $consoleLogger;
        $this->transporterList = $transporterList;
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
        array_pop($text);
        return implode('', $text);
    }

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->setDescription('Transporter: Download for a specific Type');
        $this->addArgument(
            self::TYPE,
            InputArgument::REQUIRED,
            'DownloaderList Type name'
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
        $this->downloadAction->execute($type);
    }
}
