<?php
/**
 * Class Gene_BlueFoot_Helper_Date
 *
 * @author Hob Adams <hob@gene.co.uk>
 */
class Gene_BlueFoot_Helper_Date extends Mage_Core_Helper_Abstract
{

    /* Date time constants */
    protected $_minute = 60;
    protected $_hour = 3600;
    protected $_day = 86400;


    /**
     * Function to get a friendly entity date.
     * @param bool $time - timestamp
     * @return bool|string
     */
    public function getFriendlyDateTime($time = false)
    {

        if ($time) {

            $delta = time() - $time;

            if ($delta < 2 * $this->_minute) {
                return $this->__('Just now');
            }
            if ($delta < 45 * $this->_minute) {
                return $this->__('%s minutes ago' ,floor($delta / $this->_minute));
            }
            if ($delta < 90 * $this->_minute) {
                return $this->__('An hour ago');
            }
            if ($delta < 24 * $this->_hour) {
                return $this->__('%s hours ago' ,floor($delta / $this->_hour));
            }
            if ($delta < 48 * $this->_hour) {
                return $this->__('yesterday');
            }
            if ($delta < 30 * $this->_day) {
                return $this->__('%s days ago' ,floor($delta / $this->_day));
            }
            else {
                return date('jS F', $time);
            }
        }
        return false;
    }
}