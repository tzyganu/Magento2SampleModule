<?php
/**
 * Sample_News extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category  Sample
 * @package   Sample_News
 * @copyright 2016 Marius Strajeru
 * @license   http://opensource.org/licenses/mit-license.php MIT License
 * @author    Marius Strajeru
 */
namespace Sample\News\Api\Data;

/**
 * @api
 */
interface AuthorInterface
{
    /**
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const AUTHOR_ID         = 'author_id';
    const NAME              = 'name';
    const URL_KEY           = 'url_key';
    const IS_ACTIVE         = 'is_active';
    const IN_RSS            = 'in_rss';
    const BIOGRAPHY         = 'biography';
    const DOB               = 'dob';
    const TYPE              = 'type';
    const AWARDS            = 'awards';
    const AVATAR            = 'avatar';
    const RESUME            = 'resume';
    const COUNTRY           = 'country';
    const CREATED_AT        = 'created_at';
    const UPDATED_AT        = 'updated_at';
    const META_TITLE        = 'meta_title';
    const META_DESCRIPTION  = 'meta_description';
    const META_KEYWORDS     = 'meta_keywords';
    const STORE_ID          = 'store_id';


    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * Get name
     *
     * @return string
     */
    public function getName();

    /**
     * Get url key
     *
     * @return string
     */
    public function getUrlKey();

    /**
     * Get is active
     *
     * @return bool|int
     */
    public function getIsActive();

    /**
     * Get in rss
     *
     * @return bool|int
     */
    public function getInRss();

    /**
     * Get biography
     *
     * @return string
     */
    public function getBiography();

    /**
     * @return string
     */
    public function getProcessedBiography();

    /**
     * Get DOB
     *
     * @return string
     */
    public function getDob();

    /**
     * Get type
     *
     * @return int
     */
    public function getType();

    /**
     * Get awards
     *
     * @return string
     */
    public function getAwards();

    /**
     * Get avatar
     *
     * @return string
     */
    public function getAvatar();

    /**
     * Get resume
     *
     * @return string
     */
    public function getResume();

    /**
     * Get country
     *
     * @return string
     */
    public function getCountry();

    /**
     * set id
     *
     * @param $id
     * @return AuthorInterface
     */
    public function setId($id);

    /**
     * set name
     *
     * @param $name
     * @return AuthorInterface
     */
    public function setName($name);

    /**
     * set url key
     *
     * @param $urlKey
     * @return AuthorInterface
     */
    public function setUrlKey($urlKey);

    /**
     * Set is active
     *
     * @param $isActive
     * @return AuthorInterface
     */
    public function setIsActive($isActive);

    /**
     * Set in rss
     *
     * @param $inRss
     * @return AuthorInterface
     */
    public function setInRss($inRss);

    /**
     * Set biography
     *
     * @param $biography
     * @return AuthorInterface
     */
    public function setBiography($biography);

    /**
     * Set DOB
     *
     * @param $dob
     * @return AuthorInterface
     */
    public function setDob($dob);

    /**
     * set type
     *
     * @param $type
     * @return AuthorInterface
     */
    public function setType($type);

    /**
     * set awards
     *
     * @param $awards
     * @return AuthorInterface
     */
    public function setAwards($awards);

    /**
     * set avatar
     *
     * @param $avatar
     * @return AuthorInterface
     */
    public function setAvatar($avatar);

    /**
     * set resume
     *
     * @param $resume
     * @return AuthorInterface
     */
    public function setResume($resume);

    /**
     * Set country
     *
     * @param $country
     * @return AuthorInterface
     */
    public function setCountry($country);

    /**
     * Get created at
     *
     * @return string
     */
    public function getCreatedAt();

    /**
     * set created at
     *
     * @param $createdAt
     * @return AuthorInterface
     */
    public function setCreatedAt($createdAt);

    /**
     * Get updated at
     *
     * @return string
     */
    public function getUpdatedAt();

    /**
     * set updated at
     *
     * @param $updatedAt
     * @return AuthorInterface
     */
    public function setUpdatedAt($updatedAt);

    /**
     * @param $storeId
     * @return AuthorInterface
     */
    public function setStoreId($storeId);

    /**
     * @return int[]
     */
    public function getStoreId();

    /**
     * @return string
     */
    public function getMetaTitle();

    /**
     * @param $metaTitle
     * @return AuthorInterface
     */
    public function setMetaTitle($metaTitle);

    /**
     * @return string
     */
    public function getMetaDescription();

    /**
     * @param $metaDescription
     * @return AuthorInterface
     */
    public function setMetaDescription($metaDescription);

    /**
     * @return string
     */
    public function getMetaKeywords();

    /**
     * @param $metaKeywords
     * @return AuthorInterface
     */
    public function setMetaKeywords($metaKeywords);
}
