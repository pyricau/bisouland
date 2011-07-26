<?php
/**
 * CSS Cache
 * Copyright (c) 2009 Adrian Gaudebert
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package Minifier
 * @author Adrian Gaudebert - adrian@gaudebert.fr
 * @copyright 2009 (c) Adrian Gaudebert
 * @license http://www.opensource.org/licenses/mit-license.php
 * @link http://minifier.lqbs.fr
 */

/**
 * Cache css files.
 *
 * @author Adrian Gaudebert - adrian@gaudebert.fr
 */
class Cache
{
    /**
     * Folder for cached files.
     *
     * @var string
     */
    static public $cacheFolder = "app/plugins/Minifier/cache/";
    static public function getCacheFolder() { return self::$cacheFolder; }
    static public function setCacheFolder($p_cacheFolder) { self::$cacheFolder = $p_cacheFolder; }

    /**
     * Create a cached file
     *
     * @param $p_cacheName Name of the cached file
     * @param $p_cacheContent Content to cache in the file
     */
    static public function setCache($p_cacheName, $p_cacheContent)
    {
        // écriture du code dans le fichier
        $file = fopen(self::$cacheFolder . $p_cacheName . '.css', 'w');
        $result = fwrite($file, $p_cacheContent);
        fclose($file);

        return $result;
    }

    /**
     * Delete a cached file
     *
     * @param $p_cacheName Name of the cached file
     */
    static public function deleteCache($p_cacheName)
    {
        return @unlink( self::getCacheLink($p_cacheName) );
    }

    /**
     * Check wether a cached file exists
     *
     * @param $p_cacheName Name of the cached file
     */
    static public function isCache($p_cacheName)
    {
        return is_file( self::getCacheLink($p_cacheName) );
    }

    /**
     * Get a link to a cached file
     *
     * @param $p_cacheName Name of the cached file
     */
    static public function getCacheLink($p_cacheName)
    {
        return self::$cacheFolder . $p_cacheName . '.css';
    }
}

?>
