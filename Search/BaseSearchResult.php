<?php

namespace ItBlaster\SphinxSearchPropelBundle\Search;

abstract class BaseSearchResult
{
    protected $results;                 //изначальные (сырые) результаты поиска
    protected $count;                   //текущее кол-во результатов поиска
    protected $total;                   //общее кол-во результатов поиска
    protected $result_items = array();  //список текущих объектов результата поиска

    public function __construct($results)
    {
        $this->setResults($results);
        $total = 0;
        $count = 0;
        if (count($results)) {
            foreach ($results as $result) {
                if (isset($result['total'])) {
                    $total += $result['total'];
                }
                if (isset($result['matches'])) {
                    $count += count($result['matches']);
                }
            }
        }
        $this->setTotal($total);
        $this->setCount($count);
        $this->initResultItems();
    }

    /**
     * Инициализируем объекты результатов поиска
     */
    protected function initResultItems()
    {
        if (count($this->getResults())) {
            foreach ($this->getResults() as $result) {
                if (isset($result['matches']) && count($result['matches'])) {
                    $index = $result['index'];
                    foreach ($result['matches'] as $result_match) {
                        if ($result_match['object']) {
                            $this->addResultItem($index, $result_match['object'], $result_match['weight']);
                        } else {
                            $this->setCount($this->getCount() - 1);
                        }
                    }
                }
            }
            $this->sortResults();
        }
    }

    /**
     * Сортируем найденные результаты по весам
     */
    protected function sortResults()
    {
        $results = array();
        foreach ($this->result_items as $i => $result_item) {
            $results[$i] = $result_item['weight'];
        }
        arsort($results);
        $result_items = array();
        foreach ($results as $i => $weight) {
            $result_items[] = $this->result_items[$i]['object'];
        }
        $this->result_items = $result_items;
    }

    /**
     * Элементы указанной страницы
     *
     * @param int $num
     * @param int $limit
     *
     * @return array
     */
    public function getPage($num = 1, $limit = 10)
    {
        $result = array();

        if (ceil($this->getCount() / $limit) >= $num && $num > 0) {
            $total = $this->getCount();
            $first = ($num - 1) * $limit;
            $last = $first + $limit;
            $last = $last < $total ? $last : $total;
            $last -= 1;
            $i = $first;

            while ($i <= $last) {
                $result[] = $this->result_items[$i++];
            }
        }

        return $result;
    }

    /**
     * Номер последней страницы
     *
     * @param int $limit
     *
     * @return float|int
     */
    public function getLastPage($limit = 10)
    {
        $total = $this->getCount();
        $last_page = 0;
        if ($total) {
            $last_page = intval(ceil($total / $limit));
        }

        return $last_page;
    }

    /**
     * Объекты результата поиска
     *
     * @return array
     */
    public function getResultItems()
    {
        return $this->result_items;
    }

    /**
     * @return mixed
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * @param mixed $count
     */
    public function setCount($count)
    {
        $this->count = $count;
    }

    /**
     * @return mixed
     */
    public function getResults()
    {
        return $this->results;
    }

    /**
     * @param mixed $results
     */
    public function setResults($results)
    {
        $this->results = $results;
    }

    /**
     * @return mixed
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * @param mixed $total
     */
    public function setTotal($total)
    {
        $this->total = $total;
    }
}