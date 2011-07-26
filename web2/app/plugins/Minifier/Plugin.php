<?php
/**
 * CSS Minifier
 * Copyright (c) 2009 Adrian Gaudebert
 *
 * THE SOFTWARE IS PROVIDED 'AS IS', WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package Atomik
 * @subpackage Plugins
 * @author Adrian Gaudebert - adrian@gaudebert.fr
 * @copyright 2009 (c) Adrian Gaudebert
 * @license http://www.opensource.org/licenses/mit-license.php
 * @link http://minifier.lqbs.fr
 */

/**
 * Minifier plugin
 *
 * Minify and compress in one file all the CSS files a page request.
 *
 * Gets all the CSS files specified in the 'styles' Atomik key,
 * minifies it and caches it, then displays a link to the cached file.
 *
 * No configuration is currently available.
 *
 * To use Minifier, install it, active it in your bootstrap file,
 * add your css files using Atomik like you do it useally. Just replace
 * this lines in your layout file :
 *
 * <?php foreach (A('styles', array()) as $style): ?>
 * <link rel='stylesheet' type='text/css' href='<?php echo Atomik::asset($style) ?>' />
 * <?php endforeach; ?>
 *
 * by :
 *
 * <link rel='stylesheet' type='text/css' href='<?php echo Atomik::asset( MinifierPlugin::minify() ); ?>' />
 *
 * or by :
 *
 * <?php MinifierPlugin::minify(true); ?> // if the force_css_link configuration key is to false
 *
 * or by :
 *
 * <?php MinifierPlugin::minify(); ?> // if the force_css_link configuration key is to true
 *
 * Using this plugin means :
 *  * Your css files's size is reduced (no more spaces, nor comments)
 *  * Only one HTTP request is used to get all your css files
 *  * Your css files are cached
 *
 * So you can send your css to your server without reducing them each
 * time. You also can use lots of css files without thinking about
 * the number of HTTP request, there will always be only one.
 *
 * @package Atomik
 * @subpackage Plugins
 */
class MinifierPlugin
{
    /**
     * Default configuration
     *
     * @var array
     */
    public static $config = array(
        'cache' => array(
            'dir' => 'assets/css_cache/',
        ),
        'force_css_link' => false,
    );

    /**
     * Plugin starts
     *
     * @param array $config
     */
    public static function start($config)
    {
        self::$config = array_merge(self::$config, $config);

        require('libraries/cache.class.php');
        Cache::setCacheFolder(self::$config['cache']['dir']);
    }

    /**
     * Main function of the plugin
     * Get the list of the CSS files, minify and compress them,
     * cache them, then return a link to the cached file.
     *
     * @param forceCssLink boolean, display the entire HTML <link> if set to true, return the css file link otherwise.
     *
     * @return The link to the cached css file if forceCssLink AND the 'force_css_link' configuration key are to false, nothing otherwise.
     */
    public static function minify($forceCssLink = false)
    {
        $styles = A('styles', array());

        $dir = self::getDir( $styles[0] );
        $files = self::getFiles( $styles );
        $fileList = self::getFileList( $files );

        $cacheName = str_replace('/', '_', $dir) . '_' . $fileList;

        // Check if there has been any modification on the css files
        foreach ($files as $file)
        {
            $fileLink = self::getFileLink( $file, $dir );
            if ( Cache::isCache($cacheName) && ( filemtime($fileLink) > filemtime( Cache::getCacheLink($cacheName) ) ) )
            {
                Cache::deleteCache($cacheName);
                break;
            }
        }

        // Create the cache file if it doesn't exist
        if (!Cache::isCache($cacheName))
        {
            $cssContent = '';

            foreach ($files as $file)
            {
                $fileLink = self::getFileLink( $file, $dir );
                if (is_file( $fileLink ))
                {
                    $content = @file_get_contents( $fileLink );
                    $cssContent .= self::minifyFile( $content );
                }
            }

            Cache::setCache($cacheName, $cssContent);
        }

        // Create the link to the cached css file
        $cssCachedFileLink = self::$config['cache']['dir'] . $cacheName . '.css';

        if ($forceCssLink || self::$config['force_css_link'])
        {
            // Display the link to the minified css file
            echo '<link rel="stylesheet" type="text/css" href="' . Atomik::asset( $cssCachedFileLink ) . '" />';
        }
        else
        {
            // Return the link to the minified css file
            return $cssCachedFileLink;
        }
    }

    /**
     * Minify a given css file
     * @param $css The content of a css file
     * @return string The minified css file content
     */
    private static function minifyFile( $css )
    {
        $css = preg_replace('/\/\*[\d\D]*?\*\/|\t+/', '', $css);
        $css = preg_replace('/\s\s+/', '', $css);
        $css = preg_replace('/\s*({|}|\[|\]|=|~|\+|>|\||;|:|,)\s*/', '$1', $css);

        return trim( $css );
    }

    /**
     * Get the directory of the css files
     */
    private static function getDir( $link )
    {
        $dirList = explode('/', $link );
        array_pop($dirList);

        $dir = '';
        foreach ($dirList as $d)
        {
            $dir .= $d . '/';
        }

        return $dir;
    }

    /**
     * Get an array of the css files to minify
     */
    private static function getFiles( array $styles )
    {
        $files = array();

        foreach ($styles as $cssFile)
        {
            $cssFiles = explode('/', $cssFile);
            $cssFileName = end($cssFiles);
            $cssNames = explode('.', $cssFileName);
            $files[] = $cssNames[0];
        }

        return $files;
    }

    /**
     * Get a string of the css files to minify
     * This is used to create the cache file name
     */
    private static function getFileList( array $files )
    {
        $fileList = '';
        $first = true;
        foreach ($files as $file)
        {
            if (!$first) $fileList .= '_';
            $fileList .= $file;
            $first = false;
        }

        return $fileList;
    }

    /**
     *  Get a link to a css file
     */
    private static function getFileLink( $file, $dir )
    {
        return $dir . $file . '.css';
    }
}
