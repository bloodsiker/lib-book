<?php

namespace AdminBundle\Helper;

/**
 * Class AdminHelper
 * @package AdminBundle\Helper
 */
class AdminHelper
{
    /**
     * Perform joining of path components
     * <code>
     * join_paths('/path', 'to', 'file.mp4'); // result: '/path/to/file.mp4'
     * </code>
     *
     * @return mixed
     */
    public function joinPaths()
    {
        $paths = array();

        foreach (func_get_args() as $arg) {
            if ($arg !== '') {
                $paths[] = $arg;
            }
        }

        return preg_replace('#/+#', '/', join('/', $paths));
    }

    /**
     * Returns directory for temporary files
     *
     * @return string
     */
    public function getTmpDir()
    {
        return ini_get('upload_tmp_dir') ? ini_get('upload_tmp_dir') : sys_get_temp_dir();
    }
}
