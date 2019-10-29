<?php
namespace NeroOssidiana {

    class Console {

        public static function log($data) {
            if (is_array($data)) {
                if (count($data) > 0) {
                    $str = "\\n[";
                    foreach ($data as $key => $value) {
                        $str .= "$key: $value,\\n ";
                    }

                    $data = substr($str, 0, -4) . "]";
                } else {
                    $data = "[]";
                }
            }

            if (is_bool($data) === true) {
                $data = $data ? "true" : "false";
            }

            echo "<script>console.log('PHP: " . $data . "')</script>";
        }
    }
}


