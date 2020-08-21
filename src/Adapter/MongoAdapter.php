<?php

declare(strict_types=1);

namespace KG\Pager\Adapter;

class MongoAdapter
{
    private \MongoCursor $cursor;

    public function __construct(\MongoCursor $cursor)
    {
        $this->cursor = $cursor;
    }

    /**
     * {@inheritDoc}
     *
     * @api
     */
    public function getItemCount(): int
    {
        return $this->cursor->count();
    }

    /**
     * {@inheritDoc}
     *
     * @api
     */
    public function getItems(int $offset, int $limit): array
    {
        $this->cursor->skip($offset);
        $this->cursor->limit($limit);

        return iterator_to_array($this->cursor);
    }
}
