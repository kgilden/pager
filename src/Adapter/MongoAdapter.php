<?php

namespace KG\Pager\Adapter;

class MongoAdapter
{
    /**
     * @var \MongoCursor
     */
    private $cursor;

    /**
     * @param \MongoCursor $cursor
     */
    public function __construct(\MongoCursor $cursor)
    {
        $this->cursor = $cursor;
    }

    /**
     * {@inheritDoc}
     *
     * @api
     */
    public function getItemCount()
    {
        return $this->cursor->count();
    }

    /**
     * {@inheritDoc}
     *
     * @api
     */
    public function getItems($offset, $limit)
    {
        $this->cursor->skip($offset);
        $this->cursor->limit($limit);

        return iterator_to_array($this->cursor);
    }
}
