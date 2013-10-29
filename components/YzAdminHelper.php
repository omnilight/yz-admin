<?php

class YzAdminHelper
{
    /**
     * Returns url, created by the rules used in admin panel.
     * Example:
     * <code>
     *  YzAdminHelper::adminUrl(array('/some/controller'),array('Model.property'=>5))
     * </code>
     * @param array|string $url
     * @param array $params
     * @param bool $addReturnUrl
     * @internal param array $_params
     * @return array
     */
    public static function adminUrl($url,$params = array(),$addReturnUrl=true)
    {
        if($url === null)
            return null;

        if(!is_array($url))
            $url = array($url);

        $processedParams = array();
        foreach($params as $key => $value) {
            $segments = explode('.',$key);
            $segments = array_reverse($segments);
            $item = $value;
            foreach($segments as $segment) {
                $item = array($segment => $item);
            }
            $processedParams = CMap::mergeArray($processedParams,$item);
        }

        $url = CMap::mergeArray($url,$processedParams);
        if($addReturnUrl) {
            $url['returnUrl'] = Yii::app()->request->getRequestUri();
        }
        return $url;
    }

    public static function adminHost()
    {
        // TODO Add universal administration panel detection method
        if(defined('ZP_BACKEND_HOST'))
            return 'http://'.ZP_BACKEND_HOST;
        else
            return Yii::app()->getRequest()->getHostInfo('');
    }
}