<?php

class qa_html_theme_layer extends qa_html_theme_base {

    // theme replacement functions

    function head_custom()
    {
        qa_html_theme_base::head_custom();
        if(qa_opt('embed_enable_thickbox')) { 
            $this->output('<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>');
            $this->output('<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-thickbox/3.1/jquery-thickbox.min.js"></script>');
            $this->output('<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-thickbox/3.1/thickbox.min.css" />');
        }
    }

    function q_view_content($q_view)
    {
        if (isset($q_view['content'])){
            $q_view['content'] = $this->embed_replace($q_view['content']);
        }
        qa_html_theme_base::q_view_content($q_view);
    }

    function a_item_content($a_item)
    {
        if (isset($a_item['content'])) {
            $a_item['content'] = $this->embed_replace($a_item['content']);
        }
        qa_html_theme_base::a_item_content($a_item);
    }

    function c_item_content($c_item)
    {
        if (isset($c_item['content'])) {
            $c_item['content'] = $this->embed_replace($c_item['content']);
        }
        qa_html_theme_base::c_item_content($c_item);
    }

    function embed_replace($text) {
        
        $w  = qa_opt('embed_video_width');
        
        $h = qa_opt('embed_video_height');
        
        $w2 = qa_opt('embed_image_width');
        
        $h2 = qa_opt('embed_image_height');
        
        $types = array(
            'youtube'=>array(
                array(
                    'https:\/\/(?:www\.)?youtube\.com\/watch\?v=([A-Za-z0-9_-]+)',
                    '<iframe width="'.$w.'" height="'.$h.'" src="https://www.youtube.com/embed/$1" frameborder="0" allowfullscreen></iframe>'
                ),
                array(
                    'https:\/\/youtu\.be\/([A-Za-z0-9_-]+)',
                    '<iframe width="'.$w.'" height="'.$h.'" src="https://www.youtube.com/embed/$1" frameborder="0" allowfullscreen></iframe>'
                )
            ),
            'vimeo'=>array(
                array(
                    'https:\/\/(?:www\.)?vimeo\.com\/([0-9]+)',
                    '<iframe src="https://player.vimeo.com/video/$1" width="'.$w.'" height="'.$h.'" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>'
                )
            ),
            'metacafe'=>array(
                array(
                    'https:\/\/(?:www\.)?metacafe\.com\/watch\/([0-9]+)\/([a-z0-9_]+)',
                    '<iframe src="https://www.metacafe.com/embed/$1/$2/" width="'.$w.'" height="'.$h.'" frameborder="0" allowfullscreen></iframe>'
                )
            ),
            'dailymotion'=>array(
                array(
                    'https:\/\/(?:www\.)?dailymotion\.com\/video\/([A-Za-z0-9]+)',
                    '<iframe frameborder="0" width="'.$w.'" height="'.$h.'" src="https://www.dailymotion.com/embed/video/$1"></iframe>'
                )
            ),
            'image'=>array(
                array(
                    '(https*:\/\/[-\%_\/.a-zA-Z0-9+]+\.(png|jpg|jpeg|gif|bmp))',
                    '<img src="$1" style="max-width:'.$w2.'px;max-height:'.$h2.'px" />'
                )
            ),
            'mp3'=>array(
                array(
                    '(https*:\/\/[-\%_\/.a-zA-Z0-9]+\.mp3)',
                    qa_opt('embed_mp3_player_code')
                )
            ),
            'gmap'=>array(
                array(
                    '(https*:\/\/maps.google.com\/?[^< ]+)',
                    '<iframe width="'.qa_opt('embed_gmap_width').'" height="'.qa_opt('embed_gmap_height').'" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="$1&amp;ie=UTF8&amp;output=embed"></iframe><br /><small><a href="$1&amp;ie=UTF8&amp;output=embed" style="color:#0000FF;text-align:left">View Larger Map</a></small>'
                )
            ),
        );

        foreach($types as $t => $ra) {
            foreach($ra as $r) {
                if( (!isset($r[2]) && !qa_opt('embed_enable')) || (isset($r[2]) && !qa_opt('embed_enable_'.$r[2])) ) continue;
                
                if(isset($r[2]) && @$r[2] == 'img' && qa_opt('embed_enable_thickbox')) {
                    preg_match_all('/'.$r[0].'/',$text,$imga);
                    if(!empty($imga)) {
                        foreach($imga[1] as $img) {
                            $replace = '<a href="'.$img.'" class="thickbox"><img  src="'.$img.'" style="max-width:'.$w2.'px;max-height:'.$h2.'px" /></a>';
                            $text = preg_replace('|<a[^>]+>'.$img.'</a>|i',$replace,$text);
                            $text = preg_replace('|(?<![\'"=])'.$img.'|i',$replace,$text);
                        }
                    }
                    continue;
                }
                $text = preg_replace('/<a[^>]+>'.$r[0].'<\/a>/i',$r[1],$text);
                $text = preg_replace('/(?<![\'"=])'.$r[0].'/i',$r[1],$text);
            }
        }
        return $text;
    }
}
?>
