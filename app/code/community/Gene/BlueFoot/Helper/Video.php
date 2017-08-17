<?php

class Gene_BlueFoot_Helper_Video extends Mage_Core_Helper_Abstract
{

    /**
     * Grab the URL and detect if it is Vimeo or YouTube, then return correct URL
     * @return string
     */
    public function previewAction($url)
    {

        $videoUrl = '';

        // Detect if url is youtube or Vimeo and return correct url
        if(preg_match('#^(?:https?://|//)?(?:www\.|m\.)?(?:youtu\.be/|youtube\.com/(?:embed/|v/|watch\?v=|watch\?.+&v=))([\w-]{11})(?![\w-])#', $url, $id)) {

            if (count($id)) {
                $videoUrl = 'https://www.youtube.com/embed/' . $id[1];
            }

        } elseif (preg_match("/https?:\/\/(?:www\.)?vimeo.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|album\/(\d+)\/video\/|)(\d+)(?:$|\/|\?)/", $url, $id)) {

            if (count($id)) {
                $videoUrl = 'https://player.vimeo.com/video/' . $id[3] . '?title=0&byline=0&portrait=0';
            }
        }

        return $videoUrl;
    }
}