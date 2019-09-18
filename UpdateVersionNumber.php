<?php

// Mollie Shopware Plugin Version: 1.4.4

function handle_dir($directory, $exclude = [], $version){

    $version = preg_replace('/[^0-9\.]/', '', $version);

    if (substr($directory, -1) !== '/'){
        $directory .= '/';
    }


    $handle = opendir($directory);
    while ($file = readdir($handle)){
        if (!in_array($file, $exclude)){
            if (is_dir($directory . $file)){

                handle_dir($directory . $file, $exclude, $version);

            }
            else{

                if ('MollieApiFactory.php'){
                    handle_api_factory($directory . $file, $version);
                }

                if ($file === 'plugin.xml'){
                    handle_plugin_xml($directory . $file, $version);
                }


            }
        }

    }




}

function handle_api_factory($filename, $version){

    $contents = file_get_contents($filename);


    $replace = 'MollieShopware/' . $version;

    if (!preg_match('/MollieShopware\/[0-9\.]+\s*/', $contents, $match))
        return false;

    $contents = str_replace($match[0], $replace, $contents);

    file_put_contents($filename, $contents);

}

function handle_plugin_xml($filename, $version){

    $contents = file_get_contents($filename);

    $contents = preg_replace('/\s*(\(v?[0-9\.]+\))?<\/description>/', ' (' . $version . ')</description>', $contents);
    $contents = preg_replace('/\s*([0-9\.]+)?<\/version>/', $version . '</version>', $contents);

    file_put_contents($filename, $contents);

}

if (count($argv) === 2) {
    $version = $argv[1];

    handle_dir(__DIR__, ['..', '.', 'vendor'], $version);

}
else{

    die("\n\n\nUsage: php insert_version.php [versionnumber]\n\n\n");

}