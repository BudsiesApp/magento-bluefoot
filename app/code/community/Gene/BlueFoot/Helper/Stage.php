<?php

/**
 * Various helper functions for the stages functionality within the admin. The stage could also be known as the
 * page builder
 *
 * Class Gene_BlueFoot_Helper_Stage
 *
 * @author Dave Macaulay <dave@gene.co.uk>
 */
class Gene_BlueFoot_Helper_Stage extends Gene_BlueFoot_Helper_Data
{
    /**
     * Constant for form key marker
     */
    const FORM_KEY_MARKER = 'GENE_BLUEFOOT_REPLACED_FORM_KEY';

    /**
     * Is the stage enabled?
     *
     * @return bool
     */
    public function isStageEnabled()
    {
        // Logic to see if the system is enabled
        if (Mage::getSingleton('admin/session')->isAllowed('page_builder')) {
            return true;
        }

        return false;
    }

    /**
     * Return the buttons label HTML
     *
     * @return string
     */
    public function getButtonLabelHtml()
    {
        return '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" x="0px" y="0px" viewBox="0 0 100 100" enable-background="new 0 0 100 100" xml:space="preserve"><path d="M35.758,8.057c6.159,2.536,9.541,7.488,12.198,13.165c0.725,1.57,1.329,3.261,1.933,4.952  C55.686,17.84,61.482,9.506,67.281,1.052c4.709,9.663,14.01,17.03,10.506,30.678c6.28-3.985,10.509-6.643,15.1-9.542  c5.676,11.716,8.452,24.881,3.502,39.012c0,0.121,0,0.361-0.122,0.481c-8.334,5.436-8.334,14.254-10.024,22.587  c-1.327,6.884-8.695,11.11-17.028,10.507c-6.282-0.482-12.442-1.933-18.722-3.021c-6.643-1.326-13.044-1.207-19.445,1.934  c-9.904,4.711-20.291-3.02-19.084-14.011c0.604-5.676-0.121-9.903-5.314-13.526c-6.401-4.47-7.368-19.808-2.657-27.779  c2.294-3.865,4.589-7.61,6.522-10.628c4.832,2.415,10.024,5.072,16.546,8.454c-5.918-11.836-4.952-22.585,3.382-31.402  C31.893,5.762,33.704,7.212,35.758,8.057z"/></svg>
        ' . $this->__('Activate BlueFoot');
    }

    /**
     * Return the entity config for entities
     *
     * @param $entityIds
     *
     * @return mixed
     */
    public function getEntityConfig($entityIds)
    {
        return Mage::getSingleton('gene_bluefoot/stage_data')->getEntityConfig($entityIds);
    }

    /**
     * Return a form key marker
     *
     * @return string
     */
    public static function getFormKeyMarker()
    {
        $marker = self::FORM_KEY_MARKER;
        $marker = '{{' . chr(1) . chr(2) . chr(3) . $marker . chr(3) . chr(2) . chr(1) . '}}';
        return $marker;
    }
}