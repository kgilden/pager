<?php

/*
 * This file is part of the Pager package.
 *
 * (c) Kristen Gilden kristen.gilden@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace KG\Pager\Exception;

class OutOfBoundsException extends \RuntimeException
{
    /**
     * @var integer
     */
    private $pageNumber;

    /**
     * @var integer
     */
    private $pageCount;

    /**
     * @var string
     */
    private $redirectKey;

    /**
     * @param integer $pageNumber
     * @param integer $pageCount
     * @param string  $redirectKey
     * @param string  $message
     */
    public function __construct($pageNumber, $pageCount, $redirectKey = 'page', $message = null)
    {
        $this->pageNumber = $pageNumber;
        $this->pageCount = $pageCount;
        $this->redirectKey = $redirectKey;

        $message = $message ?: sprintf('The current page (%d) is out of the paginated page range (%d).', $pageNumber, $pageCount);

        parent::__construct($message);
    }

    /**
     * @return integer
     */
    public function getPageNumber()
    {
        return $this->pageNumber;
    }

    /**
     * @return integer
     */
    public function getPageCount()
    {
        return $this->pageCount;
    }

    /**
     * @return string
     */
    public function getRedirectKey()
    {
        return $this->redirectKey;
    }
}
