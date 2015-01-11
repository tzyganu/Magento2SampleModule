<?php
namespace Sample\News\Block\Author\ListAuthor;

use Sample\News\Block\Author\ListAuthor;

class Rss extends ListAuthor
{
    /**
     * do nothing
     *
     * @return $this
     */
    protected function _prepareLayout() {
        return $this;
    }
}
