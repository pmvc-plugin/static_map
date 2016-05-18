<?php
namespace PMVC\PlugIn\static_map;

${_INIT_CONFIG}[_CLASS] = __NAMESPACE__.'\static_map';

class static_map extends \PMVC\PlugIn
{

    private $api='https://maps.googleapis.com/maps/api/staticmap?';

    private $markers;
    
    public function addMarkers($latlon,$label='',$color='blue')
    {
        $this->markers[]='color:'.$color
            .'|label:'.$label
            .'|' .$latlon;
    }

    public function toUrl()
    {
        if (empty($this['zoom'])) {
            if ($this->isLatLong($this['center'])) {
                $this['zoom'] = 14;
            }
        }
        if (empty($this['size'])) {
            $this['size'] = '640x640';
        }
        $arr = array(
            'center'=>$this['center']
            ,'size'=>$this['size']
            ,'zoom'=>$this['zoom']
        );
        $query = http_build_query($arr);
        $url = $this->api.$query;
        if(!empty($this->markers)){
            $url.='&markers='.join('&markers=',$this->markers);
        }
        return $url;
    }

    /**
     * @see https://developers.google.com/maps/documentation/streetview/intro#url_parameters
     * @parameters int heading [0-360] 
     * @parameters int fov [0-120] default 90 
     * @parameters int pitch [-90-90] default 10 
     */
    public function toStreet()
    {
        $url = \PMVC\plug('url')->getUrl('https://maps.googleapis.com/maps/api/streetview');
        $query = [
            'location'=> $this['center'],
            'size'=> \PMVC\value($this,['size'],'640x300')
        ];
        if (isset($this['heading'])) {
            $query['heading'] = $this['heading'];
        }
        if (isset($this['fov'])) {
            $query['fov'] = $this['fov'];
        }
        if (isset($this['pitch'])) {
            $query['pitch'] = $this['pitch'];
        }
        $url->query = $query;
        return (string)$url;
    }

    public function isLatLong($s)
    {
        $arr = explode(',',$s);
        if ( 2===count($arr) 
            && is_numeric($arr[0])
            && is_numeric($arr[1])
        ) {
            return true;
        } else {
            return false;
        }
    }

    public function toFile($url=null)
    {
        if (is_null($url)) {
            $url = $this->toUrl();
        }
        $tmpfname = \PMVC\plug('tmp')->file('map_');
        \PMVC\plug('curl')->get($url,function($r) use ($tmpfname){
            file_put_contents($tmpfname,$r->body);
        });
        \PMVC\plug('curl')->run();
        return $tmpfname;
    }
}
