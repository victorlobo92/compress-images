<?php

class Scavenge {
    public static function scavenge_folder($dir) {
        $heavy_list = [];
        if (is_dir($dir)) {
            if ($dh = opendir($dir)) {
                while (($file = readdir($dh)) !== false) {
                    switch (filetype($dir . $file)) {
                        case 'file':
                            $heavy_list[] = [
                                'folder' => $dir,
                                'name' => $file
                            ];
                        break;
                        case 'dir':
                            if($file === '.' || $file === '..') continue 2;
    
                            $heavy_list = array_merge($heavy_list, self::scavenge_folder($dir . $file . '/'));
                        break;
                    }
                }
                closedir($dh);
            }
        }
    
        return $heavy_list;
    }
}