<?php namespace jdpike\Services;


use Illuminate\Support\Facades\Storage;
use jdpike\PageRepository;
use Illuminate\Filesystem\FilesystemAdapter;
use League\Flysystem\Adapter\Local;

class WikiPageHelpers extends Local {

    /**
     * Returns the readable link titles for the list of links
     *
     * @param string $directory
     * @return array
     */
    public function getPageLinks(PageRepository $pages, $directory = '')
    {
        $path = $pages->getDataPath();

        $directoryNames = $this->getSubdirectories($path);
        //dd($directoryNames);

        $links = $this->createLinkAttributes($directoryNames);

        return $links;
    }

    /**
     * Returns the relative URL for the list of links
     *
     * @param string $directory
     * @return mixed
     */
    public function getLinkUrls($directory = '')
    {
        $relativeUrls = Storage::directories($directory);

        return $relativeUrls;
    }


    /**
     * Convert names into URI slugs
     * Copied from http://cubiq.org/the-perfect-php-clean-url-generator
     *
     * @param $str
     * @return mixed|string
     */
    public function slugify($str)
    {
        $clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $str);
        $clean = strtolower(trim($clean, '-'));
        $clean = preg_replace("/[\/_|+ -]+/", '-', $clean);

        return $clean;
    }

    /**
     * Create readable links from the directory names
     *
     * @param $newLinks
     * @return array
     */
    private function createLinkAttributes($newLinks)
    {
        $links = [];
        foreach ($newLinks as $key => $title) {
            $links[$key]['title'] = $this->deSlugify($title);
        }

        return $links;
    }

    /**
     * Get an array of immediate subdirectories
     *
     * @param $directory
     * @return array
     */
    private function getSubdirectories($path)
    {

        $titles = $this->listContents($path);
        dd($titles);

        $newLinks = [];
        foreach ($titles as $k => $title) {
            $newLinks[$k] = explode('/', $title);
            $newLinks[$k] = last($newLinks[$k]);
        }

        return $newLinks;
    }

    /**
     * Return the directory name after the last slash.  This
     * will be the page's title.
     *
     * @param $filePath
     * @return array|mixed
     */
    public function getLastSubdirectory($filePath)
    {
        $directories = explode('/', $filePath);
        $endPoint = last($directories);

        return $endPoint;
    }

    /**
     * Convert email-addresses to Email Addresses
     *
     * @param $title
     * @return string
     */
    public function deSlugify($title)
    {
        return ucwords(str_replace('-', ' ', $title));
    }

}