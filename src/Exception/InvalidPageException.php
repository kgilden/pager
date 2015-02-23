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

class InvalidPageException extends \RuntimeException
{
    /**
     * @var integer
     */
    private $currentPage;

    /**
     * @var integer
     */
    private $pageCount;

    /**
     * @param integer $currentPage
     * @param integer $pageCount
     * @param string  $message
     */
    public function __construct($currentPage, $pageCount, $message = null)
    {
        $this->currentPage = $currentPage;
        $this->pageCount = $pageCount;

        $message = $message ?: sprintf('The current page (%d) is out of the paginated page range (%d).', $currentPage, $pageCount);

        parent::__construct($message);
    }

    /**
     * @return integer
     */
    public function getCurrentPage()
    {
        return $this->currentPage;
    }

    /**
     * @return integer
     */
    public function getPageCount()
    {
        return $this->pageCount;
    }
}
