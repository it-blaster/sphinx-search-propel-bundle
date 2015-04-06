<?php

namespace ItBlaster\SphinxSearchPropelBundle\Search;

use Artsofte\MainBundle\Model\Contact;
use Artsofte\MainBundle\Model\News;
use Artsofte\MainBundle\Model\Product;
use Etfostra\ContentBundle\Model\Page;
use Artsofte\MainBundle\Model\Document;
use Artsofte\MainBundle\Model\Service;
use Artsofte\MainBundle\Model\Solution;

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
        if (method_exists($object, 'getActive')) {
            $active = $object->getActive();
        } else {
            $active = true;
        }

        if ($active) {
            $result_item = new SearchResultItem();
            $result_item
                ->setIndex($index)
                ->setObject($object);

            switch ($index) {
                case 'newsIndex':  $this->setParamsNews($result_item); break;
                case 'pageIndex':  $this->setParamsPage($result_item); break;
                case 'documentIndex':  $this->setParamsDocument($result_item); break;
                case 'productIndex':  $this->setParamsProduct($result_item); break;
                case 'serviceIndex':  $this->setParamsService($result_item); break;
                case 'solutionIndex':  $this->setParamsSolution($result_item); break;
                case 'contactIndex':  $this->setParamsContact($result_item); break;
            }

            $this->result_items[] = array(
                'object' => $result_item,
                'weight' => $weight
            );
        } else {
            $this->setCount($this->getCount() - 1);
        }

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
            'path_name'             => 'news_item',
            'path_params'           => array('slug' => $object->getSlug()),
            'parent_title'          => 'News',
            'parent_path_name'      => 'news_index',
            'parent_path_params'    => array(),
        );
        $result_item->setParams($params);
    }

    /**
     * Параметры статьи
     *
     * @param SearchResultItem $result_item
     */
    protected function setParamsPage(SearchResultItem &$result_item)
    {
        /** @var Page $object */
        $object = $result_item->getObject();

        $params = array(
            'title'                 => $object->getTitle(),
            'desc'                  => $object->getContent(),
            'path_name'             => 'page',
            'path_params'           => array('slug' => $object->getSlug()),
            'parent_title'          => 'Page',
            'parent_path_name'      => 'page',
            'parent_path_params'    => array()
        );
        $result_item->setParams($params);
    }

    /**
     * Параметры документа
     *
     * @param SearchResultItem $result_item
     */
    protected function setParamsDocument(SearchResultItem &$result_item)
    {
        /** @var Document $object */
        $object = $result_item->getObject();

        $params = array(
            'title'                 => $object->getFileTitle(),
            'desc'                  => null,
            'path_name'             => 'document_show',
            'path_params'           => array('slug' => $object->getDocumentGroup()->getSlug()),
            'parent_title'          => 'Document',
            'parent_path_name'      => 'document_index',
            'parent_path_params'    => array()
        );
        $result_item->setParams($params);
    }

    /**
     * Параметры услуги
     *
     * @param SearchResultItem $result_item
     */
    protected function setParamsService(SearchResultItem &$result_item)
    {
        /** @var Service $object */
        $object = $result_item->getObject();

        $params = array(
            'title'                 => $object->getTitle(),
            'desc'                  => $object->getListDesc(),
            'path_name'             => 'service_item',
            'path_params'           => array('slug' => $object->getSlug()),
            'parent_title'          => 'Service',
            'parent_path_name'      => 'service_index',
            'parent_path_params'    => array()
        );
        $result_item->setParams($params);
    }

    /**
     * Параметры продукта
     *
     * @param SearchResultItem $result_item
     */
    protected function setParamsProduct(SearchResultItem &$result_item)
    {
        /** @var Product $object */
        $object = $result_item->getObject();

        $params = array(
            'title'                 => $object->getTitle(),
            'desc'                  => $object->getListDesc(),
            'path_name'             => 'product_show',
            'path_params'           => array('slug' => $object->getSlug()),
            'parent_title'          => 'Product',
            'parent_path_name'      => 'product_list',
            'parent_path_params'    => array()
        );
        $result_item->setParams($params);
    }

    /**
     * Параметры решения
     *
     * @param SearchResultItem $result_item
     */
    protected function setParamsSolution(SearchResultItem &$result_item)
    {
        /** @var Solution $object */
        $object = $result_item->getObject();

        $params = array(
            'title'                 => $object->getTitle(),
            'desc'                  => $object->getDescription(),
            'path_name'             => 'solution_list',
            'path_params'           => array('slug' => $object->getSolutionGroup()->getSlug()),
            'parent_title'          => 'Solution',
            'parent_path_name'      => 'solution_index',
            'parent_path_params'    => array()
        );
        $result_item->setParams($params);
    }

    /**
     * Параметры контакта
     *
     * @param SearchResultItem $result_item
     */
    protected function setParamsContact(SearchResultItem &$result_item)
    {
        /** @var Contact $object */
        $object = $result_item->getObject();

        $params = array(
            'title'                 => $object->getTitle(),
            'desc'                  => $object->getDescription(),
            'path_name'             => 'contacts_show',
            'path_params'           => array('slug' => $object->getContactGroup()->getSlug()),
            'parent_title'          => 'Contact',
            'parent_path_name'      => 'contacts_index',
            'parent_path_params'    => array()
        );
        $result_item->setParams($params);
    }




}