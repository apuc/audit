<?php


namespace frontend\modules\url\models;
/**
 *
 * @property int $newSite
 * @property int $newUrl
 * @property int $newAudit
 * @property int $errorsUrl
 * @property [] $errorUrlArray
 *
 */

class ReportForm
{
    public $newSite = 0;
    public $newUrl = 0;
    public $newAudit = 0;
    public $errorsUrl = 0;
    public $errorUrlArray = array();
}