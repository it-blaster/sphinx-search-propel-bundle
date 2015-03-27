<?php

namespace ItBlaster\SphinxSearchPropelBundle\Search;

class SearchResultItem
{
    protected $index;                           //название индекса
    protected $object;                          //объект результата поска
    protected $title;                           //выводимый заголовок
    protected $desc;                            //выводимое описание
    protected $path_name;                       //имя роута
    protected $path_params = array();           //параметры роута до страницы объекта
    protected $parent_title;                    //название раздела
    protected $parent_path_name;                //имя роута до родительского раздела
    protected $parent_path_params = array();    //парамтры роута до родительского раздела
    protected $has_date = false;                //имеет ли объект поиска дату

    /**
     * Проставляем параметры объекта результата поиска
     *
     * @param array $params
     */
    public function setParams(array $params)
    {
        /*
        $params = array(
            'title'                 => '',
            'desc'                  => '',
            'path_name'             => '',
            'path_params'           => array(),
            'parent_title'          => '',
            'parent_path_name'      => '',
            'parent_path_params'    => array()
        );
        */
        $this
            ->setTitle($params['title'])
            ->setDesc($params['desc'])
            ->setPathName($params['path_name'])
            ->setPathParams($params['path_params'])
            ->setParentTitle($params['parent_title'])
            ->setParentPathName($params['parent_path_name'])
            ->setParentPathParams($params['parent_path_params'])
        ;
    }

    /**
     * @return mixed
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * @param mixed $index
     */
    public function setIndex($index)
    {
        $this->index = $index;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * @param mixed $object
     */
    public function setObject($object)
    {
        $this->object = $object;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDesc()
    {
        return $this->desc;
    }

    /**
     * @param mixed $desc
     */
    public function setDesc($desc)
    {
        $this->desc = $desc;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getParentPathName()
    {
        return $this->parent_path_name;
    }

    /**
     * @param mixed $parent_path_name
     */
    public function setParentPathName($parent_path_name)
    {
        $this->parent_path_name = $parent_path_name;
        return $this;
    }

    /**
     * @return array
     */
    public function getParentPathParams()
    {
        return $this->parent_path_params;
    }

    /**
     * @param array $parent_path_params
     */
    public function setParentPathParams($parent_path_params)
    {
        $this->parent_path_params = $parent_path_params;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getParentTitle()
    {
        return $this->parent_title;
    }

    /**
     * @param mixed $parent_title
     */
    public function setParentTitle($parent_title)
    {
        $this->parent_title = $parent_title;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPathName()
    {
        return $this->path_name;
    }

    /**
     * @param mixed $path_name
     */
    public function setPathName($path_name)
    {
        $this->path_name = $path_name;
        return $this;
    }

    /**
     * @return array
     */
    public function getPathParams()
    {
        return $this->path_params;
    }

    /**
     * @param array $path_params
     */
    public function setPathParams($path_params)
    {
        $this->path_params = $path_params;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isHasDate()
    {
        return $this->has_date;
    }

    /**
     * @param boolean $has_date
     */
    public function setHasDate($has_date)
    {
        $this->has_date = $has_date;
    }

}