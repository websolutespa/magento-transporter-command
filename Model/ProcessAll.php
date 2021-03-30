<?php
/*
 * Copyright © Websolute spa. All rights reserved.
 * See LICENSE and/or COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Websolute\TransporterCommand\Model;

use Magento\Framework\Exception\NoSuchEntityException;
use Websolute\TransporterBase\Exception\TransporterException;
use Websolute\TransporterBase\Model\Action\DownloadAction;
use Websolute\TransporterBase\Model\Action\ManipulateAction;
use Websolute\TransporterBase\Model\Action\UploadAction;

class ProcessAll
{
    /**
     * @var DownloadAction
     */
    private $downloadAction;

    /**
     * @var ManipulateAction
     */
    private $manipulateAction;

    /**
     * @var UploadAction
     */
    private $uploadAction;

    /**
     * @param DownloadAction $downloadAction
     * @param ManipulateAction $manipulateAction
     * @param UploadAction $uploadAction
     */
    public function __construct(
        DownloadAction $downloadAction,
        ManipulateAction $manipulateAction,
        UploadAction $uploadAction
    ) {

        $this->downloadAction = $downloadAction;
        $this->manipulateAction = $manipulateAction;
        $this->uploadAction = $uploadAction;
    }

    /**
     * @param string $type
     * @param string $extra
     * @throws NoSuchEntityException
     * @throws TransporterException
     */
    public function execute(string $type, string $extra = '')
    {
        $this->downloadAction->execute($type, $extra);
        $this->manipulateAction->execute($type);
        $this->uploadAction->execute($type);
    }
}
