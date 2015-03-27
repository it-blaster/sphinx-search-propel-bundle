<?php

namespace ItBlaster\SphinxSearchPropelBundle\Search;

use Artsofte\MainBundle\Model\Accessory;
use Artsofte\MainBundle\Model\Article;
use Artsofte\MainBundle\Model\Contact;
use Artsofte\MainBundle\Model\Distributor;
use Artsofte\MainBundle\Model\Employee;
use Artsofte\MainBundle\Model\Event;
use Artsofte\MainBundle\Model\EventProgram;
use Artsofte\MainBundle\Model\GalleryCategory;
use Artsofte\MainBundle\Model\HowToBuy;
use Artsofte\MainBundle\Model\Media;
use Artsofte\MainBundle\Model\News;
use Artsofte\MainBundle\Model\Product;
use Artsofte\MainBundle\Model\ProductFact;
use Artsofte\MainBundle\Model\ProductMaterial;
use Artsofte\MainBundle\Model\ProductSpecification;
use Artsofte\MainBundle\Model\Story;
use Artsofte\MainBundle\Model\Vacancy;
use Artsofte\MainBundle\Model\VideoGallery;

class SearchResult extends BaseSearchResult
{

    /**
     * Добавляем объект резуоттата поиска
     *
     * @param $index
     * @param $object
     */
    protected function addResultItem($index, $object, $weight)
    {
        $result_item = new SearchResultItem();
        $result_item
            ->setIndex($index)
            ->setObject($object);

        switch ($index) {
            case 'newsIndex':                   $this->setParamsNews($result_item);                 break;
        }

        $this->result_items[] = array(
            'object' => $result_item,
            'weight' => $weight
        );
    }

    /**
     * Проставляем параметры у новости
     *
     * @param SearchResultItem $result_item
     */
    protected function setParamsNews(SearchResultItem &$result_item)
    {
        /** @var News $object */
        $object = $result_item->getObject();
        $result_item->setHasDate(true);
        $params = array(
            'title'                 => $object->getTitle(),
            'desc'                  => $object->getShortDesc(),
            'path_name'             => 'news-item',
            'path_params'           => array('alias'=>$object->getAlias()),
            'parent_title'          => 'News',
            'parent_path_name'      => 'news',
            'parent_path_params'    => array()
        );
        $result_item->setParams($params);
    }




}