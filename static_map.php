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

    public function isLatLong($s){
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
        $tmpfname = tempnam("/tmp", "map_");
        \PMVC\plug('curl')->get($url,function($r) use ($tmpfname){
            file_put_contents($tmpfname,$r->body);
        });
        \PMVC\plug('curl')->run();
        return $tmpfname;
    }
}
