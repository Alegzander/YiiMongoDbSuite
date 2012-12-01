<?php
/**
 * Created by JetBrains PhpStorm.
 * User: alegz
 * Date: 12/1/12
 * Time: 9:45 PM
 * To change this template use File | Settings | File Templates.
 */
class CMongoMessageSource extends CMessageSource
{
    const CACHE_KEY_PREFIX = "application.component.";

    public $connectionID = "mongodb";

    public $translateMessageCollection = "i18n";

    public $cachingDuration = 0;

    public $cacheID = "cacheID";

    /**
     * @var EMongoDB
     */
    private $_db;

    public function loadMessages($category, $language)
    {

    }

    protected function loadMessageFromDb($category, $language)
    {
        $this->_db->command();
    }

    protected function getDbConnection()
    {
        if ($this->_db === null)
        {
            $this->_db = Yii::app()->getComponent($this->connectionID);

            if (!$this->_db instanceof EMongoDB)
            {
                throw new CException(Yii::t('app', 'CMongoMessageSource.connectionId is invalid. Please make sure "{id}" refers to a valid database application component.'),
                    array("{id}" => $this->connectionID));
            }
        }

        return $this->_db;
    }
}

class i18n extends EMongoDocument
{
    public $category;
    public $messages;

    public function getCollectionName()
    {
        /**
         * @var CMongoMessageSource $messageComponent
         */
        $messageComponent = Yii::app()->getComponent("messages");

        return $messageComponent->translateMessageCollection;
    }
}
