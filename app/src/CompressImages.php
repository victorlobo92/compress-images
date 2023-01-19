<?php

class CompressImages {
    private $heavy_folder;
    private $quality;
    private $png_quality;
    private $max_size;
    private $action;

    public function __construct($action)
    {
        $this->heavy_folder = 'compress/';
        $this->quality = 70;
        $this->png_quality = 9;
        $this->max_size = 5242880;
        $this->action = $action;

        $this->run_action();
    }

    public function run_action() {
        if (method_exists($this, $this->action)) {
            call_user_func([$this, $this->action]);
        }
    }

    public function find_and_compress()
    {
        $heavy_list = Scavenge::scavenge_folder($this->heavy_folder);
        $heavy_list_count = count($heavy_list);

        print("Found $heavy_list_count images...\n");

        Compress::compress_images($heavy_list, $this->quality, $this->png_quality, $this->max_size);
    }
}
