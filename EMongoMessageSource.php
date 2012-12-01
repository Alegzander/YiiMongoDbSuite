<?php
/**
 * Created by JetBrains PhpStorm.
 * User: alegz
 * Date: 12/1/12
 * Time: 9:45 PM
 */

class CMongoMessageSource extends CMessageSource
{
    const CACHE_KEY_PREFIX = "application.component.";

    /**
     * @var string
     * @desc The id os database connection
     */
    public $connectionID = "mongodb";

    /**
     * @var string
     * @desc Name of collection with messages and their translations
     */
    public $translateMessageCollection = "i18n";

    /**
     * @var int
     * @desc The time in seconds that the message can remain a valid cache
     * default to 0, meaning that cache is disabled
     */
    public $cachingDuration = 0;

    /**
     * @var string
     * @desc cache id. Simple. Put yours if you don't like this
     */
    public $cacheID = 'cache';

    /**
     * @param string $category
     * @param string $language
     * @return array|null
     */
    public function loadMessages($category, $language)
    {
        if (
            $this->cachingDuration > 0 &&
            $this->cacheID !== false &&
            ($cache = Yii::app()->getComponent($this->cacheID)) !== null
        )
        {
            $key = self::CACHE_KEY_PREFIX.'.messages.'.$category.'.'.$language;
            $data = $cache->get($key);

            if ($data !== false)
                return unserialize($data);
        }

        $messages = $this->loadMessageFromDb($category, $language);

        if (isset($cache))
            $cache->set($key, serialize($messages), $this->cachingDuration);

        return $messages;
    }

    /**
     * @param $category
     * @param $language
     * @return array|null
     * @desc Method requests translation form mongo and returns request
     * result. Variable message contains array:
     * $message['someMessage'] = 'Some message in needed language'
     */
    protected function loadMessageFromDb($category, $language)
    {
        //Getting model
        $i18n = $this->getMessageModel();
        //Getting translations
        $translations = $i18n->getMessages($category, $language)->find();

        //Returning result
        return $translations->messages;
    }

    /**
     * @return EMongoI18nModel
     * @throws CException
     * @desc Checks component and returns model
     */
    protected function getMessageModel()
    {
        $_db = Yii::app()->getComponent($this->connectionID);

        /**
         * Checking wether we have correct db component
         */
        if (!$_db instanceof EMongoDB)
        {
            throw new CException(Yii::t('app', 'EMongoMessageSource.connectionId is invalid. Please make sure "{id}" refers to a valid database application component.'),
                array("{id}" => $this->connectionID));
        }

        return EMongoI18nModel::model();
    }
}
