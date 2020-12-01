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
use Websolute\TransporterBase\Model\Action\UploadAction;

class UploadCommand extends Command
{
    const TYPE = 'type';

    /**
     * @var UploadAction
     */
    private $uploadAction;

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
     * @param UploadAction $uploadAction
     * @param Console $consoleLogger
     * @param TransporterListInterface $transporterList
     */
    public function __construct(
        $name = null,
        UploadAction $uploadAction,
        Console $consoleLogger,
        TransporterListInterface $transporterList
    ) {
        parent::__construct($name);
        $this->uploadAction = $uploadAction;
        $this->consoleLogger = $consoleLogger;
        $this->transporterList = $transporterList;
    }

    /**
     * @return string
     */
    public function getHelp()
    {
        $text = [];
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
        $this->setDescription('Transporter: Upload for a specific Type');
        $this->addArgument(
            self::TYPE,
            InputArgument::REQUIRED,
            'UploaderList Type name'
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
        $this->uploadAction->execute($type);
    }
}
